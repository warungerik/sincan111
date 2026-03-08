<?php
// api/v1/update_profile.php

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


$response = ['success' => false, 'message' => 'Gagal memperbarui profil.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Autentikasi user
        $user_id = authenticate_user();
        
        // 2. Ambil data dari body
        $data = json_decode(file_get_contents("php://input"));
        $nama = trim($data->nama);
        $email = trim($data->email);
        $wa = trim($data->wa);

        if (empty($nama) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($wa)) {
             $response['message'] = 'Format data tidak valid.';
        } else {
            // 3. Update database
            $stmt = $koneksi->prepare("UPDATE users SET nama = ?, email = ?, nomor_wa = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nama, $email, $wa, $user_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Profil berhasil diperbarui.';
            } else {
                // Cek jika error karena email duplikat
                if ($koneksi->errno == 1062) {
                    $response['message'] = 'Email tersebut sudah digunakan akun lain.';
                } else {
                    $response['message'] = 'Gagal menyimpan ke database.';
                }
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $response['message'] = 'Error database: ' . $e->getMessage();
    }
}

$koneksi->close();
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>