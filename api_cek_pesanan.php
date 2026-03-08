<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

$response = [
    'success' => false,
    'message' => 'Order ID tidak valid.',
    'data' => null
];

if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $stmt = $koneksi->prepare("
        SELECT t.*, p.deskripsi AS deskripsi_produk, p.nama_produk
        FROM transaksi t
        LEFT JOIN produk p ON p.id = t.produk_id
        WHERE t.order_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order_details = $result->fetch_assoc();

        $response['success'] = true;
        $response['message'] = 'Pesanan ditemukan.';
        $response['data'] = $order_details;
    } else {
        $response['message'] = "Pesanan dengan Order ID <strong>" . htmlspecialchars($order_id) . "</strong> tidak ditemukan.";
    }

    $stmt->close();
}

$koneksi->close();
echo json_encode($response);
?>