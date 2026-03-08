<?php
include 'includes/koneksi.php';
header('Content-Type: application/json');

$stmt = $koneksi->prepare("
    SELECT kode_voucher, tipe_diskon, nilai_diskon, tanggal_kadaluarsa
    FROM voucher
    WHERE status = 'aktif'
    AND (tanggal_kadaluarsa IS NULL OR tanggal_kadaluarsa >= CURDATE())
    ORDER BY id DESC
");
$stmt->execute();
$result = $stmt->get_result();

$vouchers = [];
while ($row = $result->fetch_assoc()) {
    $vouchers[] = $row;
}

echo json_encode($vouchers);