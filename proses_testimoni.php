<?php
include 'includes/koneksi.php';

// ============================================================
// LANGKAH 1: VERIFIKASI GOOGLE RECAPTCHA v2
// Ganti nilai di bawah dengan SECRET KEY dari Google reCAPTCHA
// Daftar di: https://www.google.com/recaptcha/admin/create
// ============================================================
$secret_key = "6Ldkdm4sAAAAAMCWvXnbMg1QDOCYfKKGlZMyL0L0";

// Ambil token reCAPTCHA yang dikirim dari form
$recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

// Jika token kosong (user tidak klik captcha), langsung tolak
if (empty($recaptcha_response)) {
    header("Location: testimoni.php?status=captcha_gagal");
    exit();
}

// Kirim permintaan verifikasi ke server Google
$verify_url = "https://www.google.com/recaptcha/api/siteverify";
$data = [
    'secret'   => $secret_key,
    'response' => $recaptcha_response,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data)
    ]
];

$context      = stream_context_create($options);
$verify_result = file_get_contents($verify_url, false, $context);
$verify_result = json_decode($verify_result, true);

// Jika verifikasi Google gagal, redirect dengan pesan error
if (!isset($verify_result['success']) || $verify_result['success'] !== true) {
    header("Location: testimoni.php?status=captcha_gagal");
    exit();
}

// ============================================================
// LANGKAH 2: VALIDASI DATA FORM
// ============================================================
$nama      = isset($_POST['nama'])      ? trim($_POST['nama'])      : '';
$rating    = isset($_POST['rating'])    ? (int)$_POST['rating']     : 0;
$testimoni = isset($_POST['testimoni']) ? trim($_POST['testimoni']) : '';

// Pastikan semua field terisi dan rating valid (1–5)
if (empty($nama) || empty($testimoni) || $rating < 1 || $rating > 5) {
    header("Location: testimoni.php?status=gagal");
    exit();
}

// Sanitasi input untuk keamanan (cegah XSS)
$nama      = htmlspecialchars($nama,      ENT_QUOTES, 'UTF-8');
$testimoni = htmlspecialchars($testimoni, ENT_QUOTES, 'UTF-8');

// ============================================================
// LANGKAH 3: SIMPAN KE DATABASE
// Status 'pending' artinya menunggu persetujuan admin
// ============================================================
$query = "INSERT INTO testimoni (nama, rating, testimoni, status, tanggal_submit) 
          VALUES (?, ?, ?, 'pending', NOW())";

$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    // Bind parameter: s = string, i = integer
    mysqli_stmt_bind_param($stmt, 'sis', $nama, $rating, $testimoni);

    if (mysqli_stmt_execute($stmt)) {
        // Berhasil disimpan
        mysqli_stmt_close($stmt);
        header("Location: testimoni.php?status=sukses");
        exit();
    } else {
        // Gagal execute
        mysqli_stmt_close($stmt);
        header("Location: testimoni.php?status=gagal");
        exit();
    }
} else {
    // Gagal prepare statement
    header("Location: testimoni.php?status=gagal");
    exit();
}
?>