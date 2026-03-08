<?php
// =====================================================
// cek_status_qris.php — FILE BARU
// Dipanggil oleh JS di bayar.php untuk polling status
// =====================================================

include 'includes/koneksi.php';

header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';

if (empty($order_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Order ID tidak valid']);
    exit;
}

$stmt = $koneksi->prepare("SELECT status_pembayaran FROM transaksi WHERE order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Transaksi tidak ditemukan']);
    exit;
}

$map = [
    'success'    => 'success',
    'settlement' => 'success',
    'pending'    => 'pending',
    'expired'    => 'expired',
    'failed'     => 'failed',
];

$status = $map[$row['status_pembayaran']] ?? 'pending';

echo json_encode(['status' => $status]);