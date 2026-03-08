<?php
// get_products.php
include 'includes/koneksi.php';

$kategori_filter_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : null;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

$query_produk = "SELECT produk.*, kategori.nama_kategori FROM produk 
                 LEFT JOIN kategori ON produk.kategori_id = kategori.id";
$conditions = [];
$params = [];
$types = "";

if (!empty($search_term)) {
    $conditions[] = "nama_produk LIKE ?";
    $params[] = "%" . $search_term . "%";
    $types .= "s";
}

if ($kategori_filter_id) {
    $conditions[] = "produk.kategori_id = ?";
    $params[] = $kategori_filter_id;
    $types .= "i";
}

if (!empty($conditions)) {
    $query_produk .= " WHERE " . implode(" AND ", $conditions);
    $query_produk .= " ORDER BY nama_produk ASC";
} else {
    $query_produk .= " ORDER BY RAND()";
}

$stmt_produk = $koneksi->prepare($query_produk);
if (!empty($params)) {
    $stmt_produk->bind_param($types, ...$params);
}
$stmt_produk->execute();
$result_semua_produk = $stmt_produk->get_result();

if ($result_semua_produk->num_rows > 0):
    while ($row = $result_semua_produk->fetch_assoc()):
        $nama       = htmlspecialchars($row['nama_produk']);
        $harga_asli = $row['harga'];
        $diskon     = $row['diskon_persen'];
        $punya_diskon = $diskon > 0;
        $harga_final  = $punya_diskon ? $harga_asli - ($harga_asli * $diskon / 100) : $harga_asli;
?>
<a href="bayar.php?id=<?= $row['id'] ?>">
    <div class="product-card">
        <div class="product-img-wrap">
            <img src="assets/images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= $nama ?>">
            <?php if ($punya_diskon): ?>
                <span class="discount-ribbon">-<?= $diskon ?>%</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (!empty($row['nama_kategori'])): ?>
                <span class="card-category"><?= htmlspecialchars($row['nama_kategori']) ?></span>
            <?php endif; ?>
            <h3 class="card-name"><?= $nama ?></h3>
            <div class="card-price-row">
                <?php if ($punya_diskon): ?>
                    <span class="price-final">Rp <?= number_format($harga_final, 0, ',', '.') ?></span>
                    <span class="price-original">Rp <?= number_format($harga_asli, 0, ',', '.') ?></span>
                <?php else: ?>
                    <span class="price-normal">Rp <?= number_format($harga_asli, 0, ',', '.') ?></span>
                <?php endif; ?>
            </div>
            <?php if (!empty($row['jumlah_terjual']) && $row['jumlah_terjual'] > 0): ?>
                <div class="card-sold">
                    <i class="fas fa-fire fire-icon"></i>
                    <?= number_format($row['jumlah_terjual']) ?> Terjual
                </div>
            <?php endif; ?>
        </div>
    </div>
</a>
<?php
    endwhile;
else:
?>
<div class="empty-state">
    <i class="fas fa-box-open"></i>
    <p>Tidak ada produk yang ditemukan.</p>
</div>
<?php
endif;
?>