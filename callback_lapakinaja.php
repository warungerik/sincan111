<?php
// =====================================================
// callback_lapakinaja.php — FILE BARU
// Daftarkan URL ini di Dashboard LapakinAja:
// Dashboard → Merchant → Settings → callbackUrl
// Contoh: https://warungerik.com/callback_lapakinaja.php
// =====================================================

function kirimPesanFonnte($target, $message, $token) {
    if (substr($target, 0, 1) === '0') {
        $target = '62' . substr($target, 1);
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => ['target' => $target, 'message' => $message],
        CURLOPT_HTTPHEADER => ["Authorization: " . $token],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function log_message($message) {
    $log_file = 'lapakinaja_log.txt';
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_file, "[$timestamp] " . print_r($message, true) . "\n", FILE_APPEND);
}

log_message("--- Notifikasi LapakinAja Diterima ---");

include 'includes/koneksi.php';
include 'config_lapakinaja.php';
include 'includes/config_fonnte.php';
date_default_timezone_set('Asia/Jakarta');

// ── 1. Hanya terima POST ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method Not Allowed']));
}

// ── 2. Ambil header & body ──
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$timestamp = $_SERVER['HTTP_X_TIMESTAMP'] ?? '';
$rawBody   = file_get_contents('php://input');

// ── 3. Validasi timestamp (max 5 menit, cegah replay attack) ──
if (empty($timestamp) || abs(time() - intval($timestamp)) > 300) {
    http_response_code(401);
    log_message("GAGAL: Timestamp expired atau kosong.");
    exit(json_encode(['error' => 'Request expired']));
}

// ── 4. Verifikasi HMAC-SHA256 ──
$expected = hash_hmac('sha256', $rawBody . $timestamp, LAPAKINAJA_API_KEY);
if (!hash_equals($expected, $signature)) {
    http_response_code(401);
    log_message("GAGAL: Signature tidak valid.");
    exit(json_encode(['error' => 'Signature tidak valid']));
}

// ── 5. Parse payload ──
$data               = json_decode($rawBody, true);
$order_id           = $data['order_id'] ?? '';
$transaction_status = strtoupper($data['status'] ?? '');

log_message("Order ID: $order_id | Status: $transaction_status");

// ── 6. Handle EXPIRED ──
if ($transaction_status === 'EXPIRED') {
    $stmt = $koneksi->prepare("UPDATE transaksi SET status_pembayaran = 'expired' WHERE order_id = ? AND status_pembayaran = 'pending'");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    log_message("Transaksi $order_id ditandai EXPIRED.");
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

// ── 7. Hanya proses SUCCESS ──
if ($transaction_status !== 'SUCCESS') {
    log_message("Status bukan SUCCESS ($transaction_status). Tidak ada aksi.");
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

// ── 8. Cek apakah transaksi masih pending (sama persis seperti callback Midtrans) ──
$stmt_check = $koneksi->prepare("SELECT status_pembayaran, produk_id, jumlah FROM transaksi WHERE order_id = ?");
$stmt_check->bind_param("s", $order_id);
$stmt_check->execute();
$transaksi = $stmt_check->get_result()->fetch_assoc();

if ($transaksi && $transaksi['status_pembayaran'] == 'pending') {

    $produk_id   = $transaksi['produk_id'];
    $jumlah_beli = $transaksi['jumlah'];
    log_message("Memulai alokasi $jumlah_beli kunci untuk Order ID: $order_id");

    $koneksi->begin_transaction();
    try {
        $stmt_get_keys = $koneksi->prepare("SELECT id, key_value FROM produk_keys WHERE produk_id = ? AND status = 'tersedia' LIMIT ? FOR UPDATE");
        $stmt_get_keys->bind_param("ii", $produk_id, $jumlah_beli);
        $stmt_get_keys->execute();
        $keys_data = $stmt_get_keys->get_result()->fetch_all(MYSQLI_ASSOC);

        if (count($keys_data) === $jumlah_beli) {
            $kunci_terjual_array = [];
            $key_ids_to_update   = [];

            foreach ($keys_data as $key) {
                $kunci_terjual_array[] = $key['key_value'];
                $key_ids_to_update[]   = $key['id'];
            }

            $placeholders = implode(',', array_fill(0, count($key_ids_to_update), '?'));
            $stmt_update_key = $koneksi->prepare("UPDATE produk_keys SET status = 'terjual', tanggal_terjual = NOW() WHERE id IN ($placeholders)");
            $stmt_update_key->bind_param(str_repeat('i', count($key_ids_to_update)), ...$key_ids_to_update);
            $stmt_update_key->execute();

            $stmt_update_produk = $koneksi->prepare("UPDATE produk SET stok = stok - ?, jumlah_terjual = jumlah_terjual + ? WHERE id = ?");
            $stmt_update_produk->bind_param("iii", $jumlah_beli, $jumlah_beli, $produk_id);
            $stmt_update_produk->execute();

            $kunci_terjual_string_db = implode("\n", $kunci_terjual_array);
            $stmt_update_transaksi = $koneksi->prepare("UPDATE transaksi SET status_pembayaran = 'success', key_terjual = ? WHERE order_id = ?");
            $stmt_update_transaksi->bind_param("ss", $kunci_terjual_string_db, $order_id);
            $stmt_update_transaksi->execute();

            $koneksi->commit();
            log_message("SUKSES: $jumlah_beli kunci dialokasikan ke Order ID $order_id.");

            // ── 9. Ambil data pesanan untuk kirim WhatsApp ──
            $stmt_get_data = $koneksi->prepare(
                "SELECT t.nama_pelanggan, t.wa_pelanggan, t.harga, p.nama_produk, p.deskripsi, k.nama_kategori
                 FROM transaksi t
                 JOIN produk p ON t.produk_id = p.id
                 JOIN kategori k ON p.kategori_id = k.id
                 WHERE t.order_id = ?"
            );
            $stmt_get_data->bind_param("s", $order_id);
            $stmt_get_data->execute();
            $data_pesanan = $stmt_get_data->get_result()->fetch_assoc();

            if ($data_pesanan) {
                $kunci_bernomor = "";
                $nomor = 1;
                foreach ($kunci_terjual_array as $kunci) {
                    $kunci_bernomor .= $nomor . ". " . $kunci . "\n";
                    $nomor++;
                }
                $kunci_bernomor = trim($kunci_bernomor);
                $waktu_pesan = date('d-m-Y H:i:s') . ' WIB';

                // ── Pesan WA sama persis seperti callback Midtrans ──
                $pesan_wa = "Halo " . $data_pesanan['nama_pelanggan'] . ",\n" .
                    "Terima kasih telah berbelanja di *WARUNGERIK.COM*! 🙏\n" .
                    "Berikut adalah detail pesanan Anda:\n" .
                    "📝 *ID Pesanan:* " . $order_id . "\n" .
                    "🗓️ *Waktu Pesan:* " . $waktu_pesan . "\n" .
                    "🗂️ *Kategori:* " . $data_pesanan['nama_kategori'] . "\n" .
                    "📦 *Produk:* " . $data_pesanan['nama_produk'] . " (x" . $jumlah_beli . ")\n" .
                    "💰 *Total Harga:* Rp " . number_format($data_pesanan['harga']) . "\n\n" .
                    "🔑 *PRODUK ANDA:*\n" . $kunci_bernomor . "\n\n" .
                    "📜 *DESKRIPSI :*\n" . $data_pesanan['deskripsi'] . "\n\n" .
                    "\n*BUTUH BANTUAN? CHAT ADMIN :* 085183129647\n*Untuk Claim Garansi Wajib Follow Channel Official Kami :* https://whatsapp.com/channel/0029VbC6IU42ZjCk8kRLox24\n*JANGAN LUPA ISI TESTIMONI:* https://warungerik.com/testimoni.php\n\n";

                $fonnte_response = kirimPesanFonnte($data_pesanan['wa_pelanggan'], $pesan_wa, $fonnte_token);
                log_message("Fonnte Response for $order_id: " . $fonnte_response);
            }

        } else {
            $koneksi->rollback();
            log_message("GAGAL: Stok kunci tidak mencukupi. Dibutuhkan: $jumlah_beli, Tersedia: " . count($keys_data) . " untuk Produk ID $produk_id.");
        }
    } catch (Exception $e) {
        $koneksi->rollback();
        log_message("EXCEPTION: " . $e->getMessage());
    }

} else {
    log_message("Transaksi untuk Order ID $order_id sudah diproses atau statusnya bukan 'pending'.");
}

// ── 10. Wajib return 200 OK, kalau tidak LapakinAja akan retry ──
http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Callback diterima']);
?>