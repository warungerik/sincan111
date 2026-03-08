<?php
// api/v1/update_password.php

header('Content-Type: application/json');
include '../../includes/koneksi.php'; // Sesuaikan path koneksi

// Salin FUNGSI BANTUAN JWT (dari atas) di sini
// ... (paste kode 70 baris fungsi JWT di sini) ...
// --- FUNGSI BANTUAN JWT (Salin ini ke 3 file baru) ---
// Kunci rahasia Anda (HARUS SAMA dengan yang di login.php)
$secret_key = "KUNCI_RAHASIA_WARUNGERIK_YANG_SANGAT_AMAN_DAN_PANJANG";
function base64url_encode($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
function base64url_decode($data) { return base64_decode(strtr($data, '-_', '+/')); }
function validate_jwt($jwt, $secret) { list($headerB64, $payloadB64, $signatureB64) = explode('.', $jwt); if (!$headerB64 || !$payloadB64 || !$signatureB64) { return null; } $signature = base64url_decode($signatureB64); $headerAndPayload = $headerB64 . "." . $payloadB64; $expectedSignature = hash_hmac('sha256', $headerAndPayload, $secret, true); if (!hash_equals($signature, $expectedSignature)) { return null; } $payload = json_decode(base64url_decode($payloadB64)); if ($payload->exp < time()) { return null; } return $payload; }
function get_bearer_token() { $headers = getallheaders(); if (isset($headers['Authorization'])) { $authHeader = $headers['Authorization']; if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) { return $matches[1]; } } return null; }
function authenticate_user() { global $secret_key; $token = get_bearer_token(); if (!$token) { echo json_encode(['success' => false, 'message' => 'Token tidak ditemukan.']); exit; } $payload = validate_jwt($token, $secret_key); if (!$payload) { echo json_encode(['success' => false, 'message' => 'Token tidak valid atau expired.']); exit; } return $payload->user_id; }
// --- AKHIR FUNGSI BANTUAN JWT ---


$response = ['success' => false, 'message' => 'Gagal memperbarui password.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Autentikasi user
        $user_id = authenticate_user();
        
        // 2. Ambil data dari body
        $data = json_decode(file_get_contents("php://input"));
        $old_password = $data->old_password;
        $new_password = $data->new_password;

        if (empty($old_password) || empty($new_password)) {
             $response['message'] = 'Password lama dan baru tidak boleh kosong.';
        } else {
            // 3. Cek password lama
            $stmt_cek = $koneksi->prepare("SELECT password FROM users WHERE id = ?");
            $stmt_cek->bind_param("i", $user_id);
            $stmt_cek->execute();
            $result = $stmt_cek->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && password_verify($old_password, $user['password'])) {
                // 4. Jika password lama benar, update ke password baru
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                
                $stmt_update = $koneksi->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt_update->bind_param("si", $new_hashed_password, $user_id);
                
                if ($stmt_update->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Password berhasil diperbarui.';
                } else {
                    $response['message'] = 'Gagal menyimpan password baru.';
                }
                $stmt_update->close();
            } else {
                $response['message'] = 'Password lama Anda salah.';
            }
            $stmt_cek->close();
        }
    } catch (Exception $e) {
        $response['message'] = 'Error database: ' + $e->getMessage();
    }
}

$koneksi->close();
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>