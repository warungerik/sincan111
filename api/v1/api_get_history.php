<?php
// api/v1/api_get_history.php

header('Content-Type: application/json');
include '../../includes/koneksi.php';

// --- FUNGSI BANTUAN JWT (Salin semua fungsi JWT) ---
$secret_key = "KUNCI_RAHASIA_WARUNGERIK_YANG_SANGAT_AMAN_DAN_PANJANG";
function base64url_encode($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
function base64url_decode($data) { return base64_decode(strtr($data, '-_', '+/')); }
function validate_jwt($jwt, $secret) { list($headerB64, $payloadB64, $signatureB64) = explode('.', $jwt); if (!$headerB64 || !$payloadB64 || !$signatureB64) { return null; } $signature = base64url_decode($signatureB64); $headerAndPayload = $headerB64 . "." . $payloadB64; $expectedSignature = hash_hmac('sha256', $headerAndPayload, $secret, true); if (!hash_equals($signature, $expectedSignature)) { return null; } $payload = json_decode(base64url_decode($payloadB64)); if ($payload->exp < time()) { return null; } return $payload; }
function get_bearer_token() { $headers = getallheaders(); if (isset($headers['Authorization'])) { $authHeader = $headers['Authorization']; if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) { return $matches[1]; } } return null; }
function get_authenticated_user_id() {
  global $secret_key;
  $token = get_bearer_token();
  if (!$token) { return null; }
  $payload = validate_jwt($token, $secret_key);
  return $payload ? $payload->user_id : null;
}
// --- AKHIR FUNGSI BANTUAN JWT ---

$response = ['success' => false, 'message' => 'Gagal mengambil riwayat transaksi.', 'data' => []];

$user_id_auth = get_authenticated_user_id();

if (!$user_id_auth) {
  $response['message'] = 'Token tidak valid atau tidak ditemukan. Silakan login.';
  http_response_code(401);
  echo json_encode($response);
  exit;
}

try {
  // Query untuk mengambil semua kolom yang diperlukan dari transaksi dan nama produk
  $query = "
    SELECT
      t.order_id,
      p.nama_produk,
      t.harga,
      t.status_pembayaran,
      t.created_at,
      -- KOLOM DATA PELANGGAN (untuk RiwayatDetail)
      t.nama_pelanggan,
      t.email_pelanggan,
      t.wa_pelanggan,
            -- KOLOM KHUSUS (Key yang dikirim)
            t.key_terjual 
            
    FROM transaksi t
    LEFT JOIN produk p ON t.produk_id = p.id
    WHERE t.user_id = ?
    ORDER BY t.created_at DESC
  ";
 
  $stmt = $koneksi->prepare($query);
  $stmt->bind_param("s", $user_id_auth);
  $stmt->execute();
  $result = $stmt->get_result();

  $history_data = [];
  while ($row = $result->fetch_assoc()) {
    // Tambahkan fallback untuk status pengiriman (karena kolomnya tidak ada)
    $row['status_pengiriman'] = ($row['status_pembayaran'] === 'settlement' || $row['status_pembayaran'] === 'success') ? 'PROSES' : 'PENDING';
    $history_data[] = $row;
  }

  $response['success'] = true;
  $response['message'] = 'Riwayat transaksi berhasil diambil.';
  $response['data'] = $history_data;
 
  $stmt->close();

} catch (Exception $e) {
  $response['message'] = 'Error database: ' . $e->getMessage();
  http_response_code(500);
}

$koneksi->close();
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>