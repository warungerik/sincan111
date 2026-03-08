<?php
// api/v1/get_user.php

header('Content-Type: application/json');
include '../../includes/koneksi.php'; // Sesuaikan path koneksi

// --- FUNGSI BANTUAN JWT (REVISI) ---
// Kunci rahasia Anda (HARUS SAMA dengan yang di login.php)
$secret_key = "KUNCI_RAHASIA_WARUNGERIK_YANG_SANGAT_AMAN_DAN_PANJANG";

function base64url_encode($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
function base64url_decode($data) { return base64_decode(strtr($data, '-_', '+/')); }
function validate_jwt($jwt, $secret) { 
    list($headerB64, $payloadB64, $signatureB64) = explode('.', $jwt); 
    if (!$headerB64 || !$payloadB64 || !$signatureB64) { return null; } 
    $signature = base64url_decode($signatureB64); 
    $headerAndPayload = $headerB64 . "." . $payloadB64; 
    $expectedSignature = hash_hmac('sha256', $headerAndPayload, $secret, true); 
    
    // Periksa tanda tangan
    if (!hash_equals($signature, $expectedSignature)) { return null; } 
    
    $payload = json_decode(base64url_decode($payloadB64)); 
    
    // Periksa expired time
    if ($payload->exp < time()) { return null; } 
    
    return $payload; 
}

function get_bearer_token() { 
    $headers = getallheaders(); 
    if (isset($headers['Authorization'])) { 
        $authHeader = $headers['Authorization']; 
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) { 
            return $matches[1]; 
        } 
    } 
    return null; 
}

/**
 * Fungsi untuk mengautentikasi dan mengembalikan User ID dari JWT.
 * Tidak melakukan 'echo' atau 'exit' di sini.
 */
function authenticate_user_id() { 
    global $secret_key; 
    $token = get_bearer_token(); 
    if (!$token) { return null; }
    
    $payload = validate_jwt($token, $secret_key); 
    
    // Mengembalikan user_id dari payload atau null jika validasi gagal
    return $payload ? $payload->user_id : null; 
}
// --- AKHIR FUNGSI BANTUAN JWT ---


$response = ['success' => false, 'message' => 'Gagal mengambil data.', 'data' => null];

try {
    // 1. Autentikasi user dan dapatkan ID-nya
    $user_id = authenticate_user_id();

    if (!$user_id) {
        $response['message'] = 'Token tidak valid atau tidak ditemukan. Silakan login.';
        http_response_code(401);
        // Langsung lompat ke penutup agar response di-echo
        goto end_script; 
    }

    // 2. Ambil data dari database, termasuk kolom ROLE
    $stmt = $koneksi->prepare("SELECT nama, email, nomor_wa, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $data = $result->fetch_assoc();
        
        // Pastikan role di set, fallback ke 'member' jika kosong (LOGIKA SUDAH BENAR)
        // Gunakan operator null coalescing (??) untuk fallback
        $data['role'] = $data['role'] ?? 'member'; 
        
        $response['success'] = true;
        $response['message'] = 'Data berhasil diambil.';
        $response['data'] = $data;
    } else {
        $response['message'] = 'User tidak ditemukan.';
    }

    $stmt->close();

} catch (Exception $e) {
    // Penanganan exception database
    if (isset($stmt)) $stmt->close();
    $response['message'] = 'Error database: ' . $e->getMessage();
    http_response_code(500);
}

end_script:
$koneksi->close();
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>