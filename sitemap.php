<?php
// [BARU] Mulai output buffering untuk "menangkap" spasi/error
ob_start();

// 1. Hubungkan ke database Anda
include 'includes/koneksi.php'; 

// [BARU] Bersihkan buffer (hapus spasi/error apa pun dari file koneksi.php)
ob_end_clean();

// 2. Tentukan URL dasar website Anda
$baseUrl = 'https://warungerik.com';

// 3. Atur Header agar file ini dibaca sebagai XML
//    (PENTING: Ini harus dipanggil SETELAH buffer dibersihkan)
header('Content-Type: application/xml; charset=utf-8');

// [DIPINDAH] Mulai membuat output XML
//             (PENTING: Ini harus dicetak SETELAH header diatur)
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

// Fungsi bantuan untuk membuat entri URL
function createUrlEntry($loc, $priority = '0.80', $lastmod = null) {
    // Jika tanggal tidak diset, gunakan tanggal hari ini
    $lastmod = $lastmod ? $lastmod : date('c'); // Format ISO 8601 (Wajib untuk sitemap)

    echo "\t<url>\n";
    echo "\t\t<loc>" . htmlspecialchars($loc) . "</loc>\n";
    echo "\t\t<lastmod>" . $lastmod . "</lastmod>\n";
    echo "\t\t<priority>" . $priority . "</priority>\n";
    echo "\t</url>\n";
}

echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// === 4. Halaman Statis (Halaman Utama) ===
createUrlEntry($baseUrl . '/', '1.00'); 
createUrlEntry($baseUrl . '/tentang.php', '0.70');
createUrlEntry($baseUrl . '/testimoni.php', '0.70');
createUrlEntry($baseUrl . '/cek_pesanan.php', '0.70'); 

// === 5. Halaman Kategori (Dinamis dari Database) ===
$result_kat = $koneksi->query("SELECT id FROM kategori ORDER BY id ASC");
if ($result_kat) {
    while ($kat = $result_kat->fetch_assoc()) {
        createUrlEntry($baseUrl . '/index.php?kategori=' . $kat['id'], '0.90');
    }
}

// === 6. Halaman Produk (Dinamis dari Database) ===
$result_prod = $koneksi->query("SELECT id FROM produk WHERE cek_stok = 1 ORDER BY id ASC");
if ($result_prod) {
    while ($prod = $result_prod->fetch_assoc()) {
        createUrlEntry($baseUrl . '/bayar.php?id=' . $prod['id'], '0.80');
    }
}

// Selesai
echo '</urlset>';

// Tutup koneksi
$koneksi->close();
?>