<?php

// 2. Header JSON
header('Content-Type: application/json');


// 4. Sertakan file koneksi
include '../../includes/koneksi.php';

// 5. Siapkan array response
$products = [];
$response = [
    'success' => false,
    'message' => 'Terjadi kesalahan.',
    'data' => null
];

try {
    // 6. Ambil parameter (jika ada)
    $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

    $query_produk = "SELECT * FROM produk";
    $conditions = [];
    $params = [];
    $types = "";

    if (!empty($search_term)) {
        $conditions[] = "nama_produk LIKE ?";
        $params[] = "%" . $search_term . "%";
        $types .= "s";
    }
    
    if (!empty($conditions)) {
        $query_produk .= " WHERE " . implode(" AND ", $conditions);
    }
    $query_produk .= " ORDER BY nama_produk ASC";

    $stmt_produk = $koneksi->prepare($query_produk);
    if (!empty($params)) {
        $stmt_produk->bind_param($types, ...$params);
    }
    $stmt_produk->execute();
    $result_semua_produk = $stmt_produk->get_result();
    
    // 7. UBAH LOGIKA OUTPUT
    if ($result_semua_produk->num_rows > 0) {
        while ($row = $result_semua_produk->fetch_assoc()) {
            
            // Logika harga
            $harga_asli = (float)$row['harga'];
            $diskon = (int)$row['diskon_persen']; // <-- Perbaikan typo
            $harga_setelah_diskon = $harga_asli;
            if ($diskon > 0) {
                 $harga_setelah_diskon = $harga_asli - ($harga_asli * $diskon / 100);
            }

            // Masukkan ke array $products
            $products[] = [
                'id' => $row['id'],
                'kategori_id' => (int)$row['kategori_id'], // Untuk filter tab
                'nama_produk' => $row['nama_produk'],
                'gambar' => 'https://warungerik.com/assets/images/' . htmlspecialchars($row['gambar']), 
                'harga_asli' => $harga_asli,
                'diskon_persen' => $diskon,
                'harga_final' => $harga_setelah_diskon,
                'terjual' => isset($row['jumlah_terjual']) ? (int)$row['jumlah_terjual'] : 0,
                'stok' => (int)$row['stok'], // Untuk cek stok
                'cek_stok' => (int)$row['cek_stok'] // Untuk cek stok
            ];
        }
        
        $response['success'] = true;
        $response['message'] = 'Produk ditemukan.';
        $response['data'] = $products;

    } else {
        $response['success'] = true;
        $response['message'] = 'Tidak ada produk yang ditemukan.';
        $response['data'] = []; // Kirim array kosong
    }

} catch (Exception $e) {
    $response['message'] = 'Error database: ' . $e->getMessage();
}

$koneksi->close();

// 8. KEMBALIKAN SEBAGAI JSON (INI PERBAIKAN GAMBAR)
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>