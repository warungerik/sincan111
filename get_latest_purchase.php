<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

// Ambil 15 transaksi terakhir yang berhasil untuk dijadikan "playlist" notifikasi
$query = "
    SELECT t.nama_pelanggan, p.nama_produk, p.gambar
    FROM transaksi t
    JOIN produk p ON t.produk_id = p.id
    WHERE t.status_pembayaran = 'success' OR t.status_pembayaran = 'settlement'
    ORDER BY t.tanggal_transaksi DESC
    LIMIT 100"; // Mengambil 15 data terakhir

$result = mysqli_query($koneksi, $query);
$purchases = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($item = mysqli_fetch_assoc($result)) {
        // Menyensor nama untuk privasi, hanya mengambil kata pertama
        $nama_parts = explode(' ', trim($item['nama_pelanggan']));
        $nama_samar = htmlspecialchars($nama_parts[0]);

        $purchases[] = [
            'nama_pelanggan' => $nama_samar,
            'nama_produk' => htmlspecialchars($item['nama_produk']),
            'gambar' => htmlspecialchars($item['gambar'])
        ];
    }
}

// Kembalikan daftar transaksi dalam format JSON
echo json_encode($purchases);
?>