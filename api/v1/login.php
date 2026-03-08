<?php
// api/v1/login.php

header('Content-Type: application/json');
include '../../includes/koneksi.php'; // Sesuaikan path koneksi Anda

// --- Fungsi Bantuan untuk Membuat JWT ---
// (Ini adalah cara membuat JWT sederhana tanpa perlu library eksternal)
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function create_jwt($user_id, $email) {
    // Ganti ini dengan kunci rahasIA Anda sendiri yang sangat panjang dan acak
    $secret_key = "KUNCI_RAHASIA_WARUNGERIK_YANG_SANGAT_AMAN_DAN_PANJANG";

    // Header token
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $base64UrlHeader = base64url_encode($header);

    // Payload (data isi token)
    $payload = json_encode([
        'user_id' => $user_id,
        'email' => $email,
        'iat' => time(), // Issued at (kapan dibuat)
        'exp' => time() + (60 * 60 * 24 * 7) // Expired dalam 7 hari
    ]);
    $base64UrlPayload = base64url_encode($payload);

    // Signature (Tanda tangan)
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
    $base64UrlSignature = base64url_encode($signature);

    // Gabungkan ketiganya
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}
// --- Akhir Fungsi Bantuan JWT ---


$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->email) || !isset($data->password)) {
        $response['message'] = 'Email dan password wajib diisi.';
    } else {
        $email = trim($data->email);
        $password = trim($data->password);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            $response['message'] = 'Format email atau password tidak valid.';
        } else {
            try {
                // 1. Ambil data user berdasarkan email
                $stmt = $koneksi->prepare("SELECT id, email, password FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    // 2. Verifikasi password yang di-hash
                    if (password_verify($password, $user['password'])) {
                        
                        // 3. BUAT TOKEN! (INI BAGIAN PENTING)
                        $token = create_jwt($user['id'], $user['email']);

                        // 4. KIRIM TOKEN KE CLIENT
                        $response['success'] = true;
                        $response['message'] = 'Login berhasil!';
                        $response['token'] = $token; // <-- INI YANG HILANG SEBELUMNYA

                    } else {
                        $response['message'] = 'Password salah.';
                    }
                } else {
                    $response['message'] = 'Email tidak ditemukan.';
                }
                $stmt->close();
            } catch (Exception $e) {
                $response['message'] = 'Error database: ' . $e->getMessage();
            }
        }
    }
}

$koneksi->close();
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>