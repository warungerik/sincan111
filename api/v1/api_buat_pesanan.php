<?php

// 2. Header JSON
header('Content-Type: application/json');

// --- FUNGSI BANTUAN JWT (WAJIB DISALIN DI SINI) ---
$secret_key = "KUNCI_RAHASIA_WARUNGERIK_YANG_SANGAT_AMAN_DAN_PANJANG";
function base64url_encode($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
function base64url_decode($data) { return base64_decode(strtr($data, '-_', '+/')); }
function validate_jwt($jwt, $secret) { list($headerB64, $payloadB64, $signatureB64) = explode('.', $jwt); if (!$headerB64 || !$payloadB64 || !$signatureB64) { return null; } $signature = base64url_decode($signatureB64); $headerAndPayload = $headerB64 . "." . $payloadB64; $expectedSignature = hash_hmac('sha256', $headerAndPayload, $secret, true); if (!hash_equals($signature, $expectedSignature)) { return null; } $payload = json_decode(base64url_decode($payloadB64)); if ($payload->exp < time()) { return null; } return $payload; }
function get_bearer_token() { $headers = getallheaders(); if (isset($headers['Authorization'])) { $authHeader = $headers['Authorization']; if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) { return $matches[1]; } } return null; }
function get_authenticated_user_id() { 
    global $secret_key; 
    $token = get_bearer_token(); 
    if (!$token) { return null; } 
    $payload = validate_jwt($token, $secret_key); 
    return $payload ? $payload->user_id : null; 
}
// --- AKHIR FUNGSI BANTUAN JWT ---

// 4. Sertakan file koneksi dan midtrans
include '../../includes/koneksi.php';
include '../../config_midtrans.php'; // <-- Pastikan ini pakai Kunci PRODUCTION

$biaya_layanan_persen = 0.005; // Diambil dari bayar.php
const RESELLER_DISCOUNT_PERCENT = 0.20; // 30%

// 5. Siapkan array response
$response = [
    'success' => false,
    'message' => 'Permintaan tidak valid.',
    'data' => null
];

// Dapatkan User ID dari Token (bisa NULL jika user tidak login/guest)
$user_id_auth = get_authenticated_user_id();
$user_role = 'member'; // Default Role

// 6. API ini hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 7. Ambil data JSON yang dikirim oleh aplikasi
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data);

    try {
        // ... (VALIDASI INPUT TETAP SAMA) ...
        if (!isset($data->produk_id, $data->nama_pelanggan, $data->email_pelanggan, $data->wa_pelanggan, $data->jumlah) || 
            !is_numeric($data->produk_id) || 
            empty(trim($data->nama_pelanggan)) || 
            !filter_var(trim($data->email_pelanggan), FILTER_VALIDATE_EMAIL) || 
            empty(trim($data->wa_pelanggan)) || 
            !is_numeric($data->jumlah)) 
        {
            $response['message'] = "Error: Semua data harus diisi dengan format yang benar.";
            echo json_encode($response);
            exit;
        }

        // 9. Ambil semua data
        $produk_id = (int)$data->produk_id;
        $nama_pelanggan = trim($data->nama_pelanggan);
        $email_pelanggan = trim($data->email_pelanggan);
        $wa_pelanggan = trim($data->wa_pelanggan);
        $jumlah = (int)$data->jumlah;
        $kode_voucher = isset($data->kode_voucher) ? trim($data->kode_voucher) : '';
        
        // -----------------------------------------------------------
        // KUNCI PERBAIKAN 1: AMBIL ROLE DARI DATABASE JIKA USER LOGIN
        // -----------------------------------------------------------
        if ($user_id_auth) {
            $stmt_user = $koneksi->prepare("SELECT role FROM users WHERE id = ?");
            $stmt_user->bind_param("i", $user_id_auth);
            $stmt_user->execute();
            $user_data = $stmt_user->get_result()->fetch_assoc();
            
            if ($user_data && $user_data['role']) {
                $user_role = strtolower($user_data['role']);
            }
            $stmt_user->close();
        }


        // 10. Cek Produk dan Stok
        $stmt = $koneksi->prepare("SELECT * FROM produk WHERE id = ?");
        $stmt->bind_param("i", $produk_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $produk = $result->fetch_assoc();

        if (!$produk) {
            $response['message'] = "Error: Produk tidak ditemukan.";
            echo json_encode($response);
            exit;
        }
        
        // Cek stok (dari file bayar.php Anda)
        if ($produk['cek_stok'] == 1 && $jumlah > $produk['stok']) {
            $response['message'] = "Error: Jumlah pembelian tidak valid atau melebihi stok ({$produk['stok']}).";
            echo json_encode($response);
            exit;
        }

        // -----------------------------------------------------------
        // KUNCI PERBAIKAN 2: LOGIKA HITUNG HARGA BERDASARKAN ROLE
        // -----------------------------------------------------------
        $harga_asli = (float)$produk['harga'];
        $diskon_persen = (float)$produk['diskon_persen'];
        
        // Hitung harga setelah diskon reguler (Harga Member)
        $harga_member = ($diskon_persen > 0) ? $harga_asli - ($harga_asli * $diskon_persen / 100) : $harga_asli;

        // Harga Satuan yang akan digunakan untuk Midtrans/Database
        $harga_satuan_final = $harga_member;

        // APLIKASIKAN DISKON RESELLER DARI HARGA MEMBER
        if ($user_role === 'reseller') {
            $diskon_reseller = $harga_member * RESELLER_DISCOUNT_PERCENT;
            $harga_satuan_final = $harga_member - $diskon_reseller;
        }
        
        // JANGAN GUNAKAN HARGA DARI FRONTEND (hapus $data->harga_akhir)
        // Hitung Subtotal total di backend
        $subtotal = $harga_satuan_final * $jumlah;

        // 11. Logika Voucher (TETAP SAMA)
        $potongan_voucher = 0;
        if (!empty($kode_voucher)) {
            $stmt_voucher = $koneksi->prepare("SELECT * FROM voucher WHERE kode_voucher = ? AND status = 'aktif' AND (tanggal_kadaluarsa IS NULL OR tanggal_kadaluarsa >= CURDATE())");
            $stmt_voucher->bind_param("s", $kode_voucher);
            $stmt_voucher->execute();
            if ($voucher = $stmt_voucher->get_result()->fetch_assoc()) {
                $potongan_voucher = ($voucher['tipe_diskon'] == 'persen') ? $subtotal * ($voucher['nilai_diskon'] / 100) : $voucher['nilai_diskon'];
                $potongan_voucher = min($potongan_voucher, $subtotal);
            }
            $stmt_voucher->close();
        }
        
        $subtotal_setelah_voucher = $subtotal - $potongan_voucher;
        $biaya_layanan = ceil($subtotal_setelah_voucher * $biaya_layanan_persen);
        $total_harga_server = $subtotal_setelah_voucher + $biaya_layanan;
        $order_id_server = 'WARUNGERIK-' . time() . '-' . $produk_id;


        // 12. Siapkan Item Details Midtrans
        // Gunakan $harga_satuan_final yang sudah mencakup diskon reseller
        $item_details_midtrans = [
            [
                'id' => $produk['id'], 
                'price' => round($harga_satuan_final), // Harga satuan yang fix
                'quantity' => $jumlah, 
                'name' => $produk['nama_produk'] . ($user_role === 'reseller' ? ' (Reseller)' : '')
            ]
        ];
        
        if ($potongan_voucher > 0) {
            $item_details_midtrans[] = ['id' => 'VOUCHER', 'price' => -round($potongan_voucher), 'quantity' => 1, 'name' => 'Voucher ' . htmlspecialchars($kode_voucher)];
        }
        $item_details_midtrans[] = ['id' => 'FEE', 'price' => round($biaya_layanan), 'quantity' => 1, 'name' => "Biaya Layanan"];

        // 13. Masukkan Transaksi ke Database (status pending)
        $stmt_transaksi = $koneksi->prepare("INSERT INTO transaksi (order_id, produk_id, harga, status_pembayaran, nama_pelanggan, email_pelanggan, wa_pelanggan, jumlah, user_id) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?)");
        
        $stmt_transaksi->bind_param("sidsssii", 
            $order_id_server, 
            $produk_id, 
            $total_harga_server, 
            $nama_pelanggan, 
            $email_pelanggan, 
            $wa_pelanggan, 
            $jumlah, 
            $user_id_auth // <-- Menggunakan ID dari JWT/NULL
        );
        $stmt_transaksi->execute();

        // 14. Siapkan Parameter Midtrans
        $params = [
            'transaction_details' => ['order_id' => $order_id_server, 'gross_amount' => round($total_harga_server)],
            'item_details' => $item_details_midtrans,
            'customer_details' => ['first_name' => $nama_pelanggan, 'email' => $email_pelanggan, 'phone' => $wa_pelanggan],
        ];
        
        if ($user_id_auth) {
             $params['customer_details']['user_id'] = $user_id_auth;
        }

        // 15. Panggil Snap::getSnapToken
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        // 16. Update Snap Token ke Database
        $stmt_update_token = $koneksi->prepare("UPDATE transaksi SET snap_token = ? WHERE order_id = ?");
        $stmt_update_token->bind_param("ss", $snapToken, $order_id_server);
        $stmt_update_token->execute();
        $stmt_transaksi->close();
        $stmt_update_token->close();

        // 17. Kirim Response SUKSES
        $response['success'] = true;
        $response['message'] = 'Pesanan berhasil dibuat. Mengalihkan ke pembayaran...';
        $response['data'] = [
            'order_id' => $order_id_server, 
            'total_harga' => $total_harga_server
        ];

    } catch (Exception $e) {
        $response['message'] = 'Terjadi error di server: ' . $e->getMessage();
        http_response_code(500);
    }

} else {
    $response['message'] = 'Metode request tidak diizinkan, harus POST.';
    http_response_code(405);
}

$koneksi->close();

// 18. Kembalikan response JSON
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>