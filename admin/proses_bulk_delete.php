<?php
session_start();
include '../includes/koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if (isset($_POST['ids'])) {
    $ids = json_decode($_POST['ids']);
    
    if (!empty($ids)) {
        // Mengubah array ID menjadi string untuk query (misal: 1, 2, 3)
        $ids_string = implode(',', array_map('intval', $ids));
        
        // Opsional: Ambil nama file gambar jika ingin menghapus file fisik juga
        // $query_gambar = "SELECT gambar FROM produk WHERE id IN ($ids_string)";
        
        $sql = "DELETE FROM produk WHERE id IN ($ids_string)";
        
        if ($koneksi->query($sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $koneksi->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada produk dipilih']);
    }
}
?>