<?php
header('Content-Type: application/json');

// 2. Menyertakan file koneksi (Perbaiki path-nya!)
include '../../includes/koneksi.php';

// 3. Inisialisasi array untuk response
$response = [
    'success' => false,
    'message' => 'Aksi tidak valid atau Order ID kosong.',
    'data' => null
];

// 4. Cek apakah koneksi database gagal
if (!$koneksi) {
    $response['message'] = 'Koneksi database gagal.';
    echo json_encode($response);
    exit;
}

// 5. Proses permintaan jika order_id dikirim
if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
    $order_id = htmlspecialchars(trim($_GET['order_id']));
    $response['message'] = 'Order ID tidak ditemukan.';

    try {
        // Query disamakan dengan riwayat transaksi, tetapi menggunakan order_id di klausa WHERE
        $query = "
            SELECT
                t.order_id,
                p.nama_produk,
                t.harga,
                t.status_pembayaran,
                t.created_at,
                t.nama_pelanggan,
                t.email_pelanggan,
                t.wa_pelanggan,
                t.key_terjual 
            FROM transaksi t
            LEFT JOIN produk p ON t.produk_id = p.id
            WHERE t.order_id = ? 
            LIMIT 1
        ";
        
        $stmt = $koneksi->prepare($query);
        
        if ($stmt === false) {
            throw new Exception("Gagal menyiapkan statement: " . $koneksi->error);
        }
        
        // Binding parameter order_id (string)
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order_details = $result->fetch_assoc();
            
            // Tambahkan key status_pengiriman untuk konsistensi di front-end (opsional, tergantung kebutuhan)
            $order_details['status_pengiriman'] = ($order_details['status_pembayaran'] === 'settlement' || $order_details['status_pembayaran'] === 'success') ? 'PROSES' : 'PENDING';

            $response['success'] = true;
            $response['message'] = 'Pesanan ditemukan.';
            $response['data'] = $order_details;

        } else {
            $response['message'] = "Pesanan dengan Order ID " . $order_id . " tidak ditemukan.";
        }
        
        $stmt->close();

    } catch (Exception $e) {
        $response['success'] = false; 
        $response['message'] = 'Error sistem: Gagal memproses data. ' . $e->getMessage();
        $response['data'] = null;
    }
}

// 6. Tutup koneksi database
$koneksi->close();

// 7. Mengembalikan response dalam format JSON
echo json_encode($response, JSON_UNESCAPED_SLASHES);