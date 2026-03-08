<?php
header('Content-Type: application/json');

// 2. Pastikan path include-nya benar! (Naik 2 level)
include '../../includes/koneksi.php';

// 3. Siapkan array response
$categories = [];
$response = [
    'success' => false,
    'message' => 'Terjadi kesalahan.',
    'data' => null
];

try {
    // 4. Query untuk mengambil semua kategori
    // Asumsi: Nama tabel Anda adalah 'kategori'
    $query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
    
    $stmt = $koneksi->prepare($query_kategori);
    $stmt->execute();
    $result_kategori = $stmt->get_result();

    if ($result_kategori->num_rows > 0) {
        // 5. Masukkan setiap kategori ke array
        while ($row = $result_kategori->fetch_assoc()) {
            $categories[] = [
                'id_kategori' => (int)$row['id'], // Sesuaikan nama kolom jika beda
                'nama_kategori' => $row['nama_kategori'] // Sesuaikan nama kolom jika beda
            ];
        }

        $response['success'] = true;
        $response['message'] = 'Kategori ditemukan.';
        $response['data'] = $categories;

    } else {
        $response['success'] = true;
        $response['message'] = 'Belum ada kategori.';
        $response['data'] = []; // Kirim array kosong
    }

} catch (Exception $e) {
    $response['message'] = 'Error database: ' . $e->getMessage();
}

$koneksi->close();

// 6. Kembalikan sebagai JSON
echo json_encode($response);

?>