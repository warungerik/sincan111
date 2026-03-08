<?php
// api/v1/register.php

header('Content-Type: application/json');

include '../../includes/koneksi.php'; // Sesuaikan path koneksi Anda

$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->nama) || !isset($data->email) || !isset($data->password) || !isset($data->wa)) {
        $response['message'] = 'Semua field (nama, email, wa, password) wajib diisi.';
    } else {
        $nama = trim($data->nama);
        $email = trim($data->email);
        $password = trim($data->password);
        $wa = trim($data->wa);

        if (empty($nama) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password) || empty($wa)) {
            $response['message'] = 'Format data tidak valid.';
        } else {
            try {
                // 1. Cek apakah email sudah ada
                $stmt_cek = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
                $stmt_cek->bind_param("s", $email);
                $stmt_cek->execute();
                $result_cek = $stmt_cek->get_result();

                if ($result_cek->num_rows > 0) {
                    $response['message'] = 'Email ini sudah terdaftar. Silakan login.';
                } else {
                    // 2. SANGAT PENTING: Hash password
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    // 3. Masukkan user baru
                    $stmt_insert = $koneksi->prepare("INSERT INTO users (nama, email, nomor_wa, password) VALUES (?, ?, ?, ?)");
                    $stmt_insert->bind_param("ssss", $nama, $email, $wa, $hashed_password);
                    
                    if ($stmt_insert->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Registrasi berhasil! Silakan login.';
                    } else {
                        $response['message'] = 'Registrasi gagal, terjadi kesalahan server.';
                    }
                    $stmt_insert->close();
                }
                $stmt_cek->close();
            } catch (Exception $e) {
                $response['message'] = 'Error database: ' . $e->getMessage();
            }
        }
    }
}

$koneksi->close();
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>