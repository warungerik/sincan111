<?php
include 'includes/koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Metode tidak valid.']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$kode_voucher = $data['kode_voucher'] ?? '';
$cart = $data['cart'] ?? []; // Ambil data cart

if (empty($kode_voucher)) {
  echo json_encode(['success' => false, 'message' => 'Kode voucher tidak boleh kosong.']);
  exit;
}

if (empty($cart)) {
  echo json_encode(['success' => false, 'message' => 'Keranjang kosong.']);
  exit;
}

$stmt = $koneksi->prepare("SELECT id, tipe_diskon, nilai_diskon FROM voucher WHERE kode_voucher = ? AND status = 'aktif' AND (tanggal_kadaluarsa IS NULL OR tanggal_kadaluarsa >= CURDATE())");
$stmt->bind_param("s", $kode_voucher);
$stmt->execute();
$result = $stmt->get_result();

if (!($voucher = $result->fetch_assoc())) {
  echo json_encode(['success' => false, 'message' => 'Kode voucher tidak valid atau sudah kadaluarsa.']);
  $stmt->close();
  $koneksi->close();
  exit;
}

$voucher_id = $voucher['id'];
$stmt->close();

$stmt_kat = $koneksi->prepare("SELECT kategori_id FROM voucher_kategori WHERE voucher_id = ?");
$stmt_kat->bind_param("i", $voucher_id);
$stmt_kat->execute();
$result_kat = $stmt_kat->get_result();

$kategori_berlaku = [];
while ($row_kat = $result_kat->fetch_assoc()) {
  $kategori_berlaku[] = (int)$row_kat['kategori_id'];
}
$stmt_kat->close();

$semua_kategori_berlaku = empty($kategori_berlaku);
$voucher_bisa_dipakai = false;

if ($semua_kategori_berlaku) {
    $voucher_bisa_dipakai = true;
} else {
    foreach ($cart as $item) {
        $item_kategori_id = (int)($item['kategori_id'] ?? 0);
        if (in_array($item_kategori_id, $kategori_berlaku)) {
            $voucher_bisa_dipakai = true;
            break;
        }
    }
}

if (!$voucher_bisa_dipakai) {
  echo json_encode(['success' => false, 'message' => 'Voucher tidak berlaku untuk produk ini.']);
  $koneksi->close();
  exit;
}

echo json_encode([
  'success' => true,
  'message' => 'Voucher berhasil diterapkan!',
  'tipe' => $voucher['tipe_diskon'],
  'nilai' => $voucher['nilai_diskon']
]);

$koneksi->close();
?>