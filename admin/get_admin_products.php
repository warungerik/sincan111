<?php
// admin/get_admin_products.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    exit('Akses ditolak');
}
include '../includes/koneksi.php';

$kategori_filter_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : null;

$query = "SELECT * FROM produk";
if ($kategori_filter_id) {
    $query .= " WHERE kategori_id = ?";
}
$query .= " ORDER BY id DESC";

$stmt = $koneksi->prepare($query);
if ($kategori_filter_id) {
    $stmt->bind_param("i", $kategori_filter_id);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
<tr data-name="<?= strtolower(htmlspecialchars($row['nama_produk'])) ?>">
    <td class="td-check">
        <input type="checkbox" class="product-checkbox" value="<?= $row['id'] ?>">
    </td>
    <td>
        <img src="../assets/images/<?= htmlspecialchars($row['gambar']) ?>" class="product-img" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
    </td>
    <td>
        <div class="product-name"><?= htmlspecialchars($row['nama_produk']) ?></div>
        <div class="product-sub">ID #<?= $row['id'] ?></div>
    </td>
    <td class="price-cell">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
    <td>
        <?php if ($row['cek_stok'] == 1): ?>
            <span class="stock-badge has-stock"><i class="fas fa-cubes" style="font-size:10px;"></i> <?= $row['stok'] ?></span>
        <?php else: ?>
            <span class="stock-badge unlimited"><i class="fas fa-infinity" style="font-size:10px;"></i> Bebas</span>
        <?php endif; ?>
    </td>
    <td class="actions-cell">
        <div class="action-group">
            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-icon edit" title="Edit"><i class="fas fa-pen"></i></a>
            <?php if ($row['cek_stok'] == 1): ?>
                <a href="kelola_keys.php?id=<?= $row['id'] ?>" class="btn-icon keys" title="Kelola Keys"><i class="fas fa-key"></i></a>
            <?php endif; ?>
            <a href="proses.php?aksi=hapus&id=<?= $row['id'] ?>" class="btn-icon del" title="Hapus" onclick="return confirm('Yakin hapus produk ini?')"><i class="fas fa-trash"></i></a>
        </div>
    </td>
</tr>
<?php
    endwhile;
else:
?>
<tr>
    <td colspan="6">
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-box-open"></i></div>
            <p>Tidak ada produk di kategori ini.</p>
        </div>
    </td>
</tr>
<?php endif; ?>