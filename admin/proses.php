<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    exit('Akses ditolak. Silakan login kembali.');
}
include '../includes/koneksi.php';

function uploadGambar() {
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === 4) {
        return ['status' => 'no_file'];
    }
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    if ($error !== 0) {
        return ['status' => 'error', 'message' => 'Terjadi error saat upload file. Kode: ' . $error];
    }
    
    $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensiGambar = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensiGambar, $ekstensiValid)) {
        return ['status' => 'error', 'message' => 'File yang diupload bukan gambar!'];
    }

    if ($ukuranFile > 2000000) {
        return ['status' => 'error', 'message' => 'Ukuran gambar terlalu besar! (Maks 2MB)'];
    }

    $namaFileBaru = uniqid('img_', true) . '.' . $ekstensiGambar;
    $tujuanUpload = '../assets/images/' . $namaFileBaru;

    if (move_uploaded_file($tmpName, $tujuanUpload)) {
        return ['status' => 'success', 'filename' => $namaFileBaru];
    } else {
        return ['status' => 'error', 'message' => 'Gagal memindahkan file. Cek folder permission (755).'];
    }
}

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $kategori_id = (int)$_POST['kategori_id'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $gambar_lama = $_POST['gambar_lama'];
    
    $diskon_persen = isset($_POST['diskon_persen']) ? (int)$_POST['diskon_persen'] : 0;
    if ($diskon_persen < 0 || $diskon_persen > 100) {
        $diskon_persen = 0;
    }

    $cek_stok = isset($_POST['cek_stok']) ? 1 : 0;
    
    $uploadResult = uploadGambar();
    $gambar = $gambar_lama; 

    if ($uploadResult['status'] === 'success') {
        $gambar = $uploadResult['filename'];
        if ($gambar_lama && file_exists('../assets/images/' . $gambar_lama)) {
            unlink('../assets/images/' . $gambar_lama);
        }
    } elseif ($uploadResult['status'] === 'error') {
        die("Error Upload: " . $uploadResult['message']);
    }

    if (empty($id)) { 
        $query = "INSERT INTO produk (kategori_id, nama_produk, harga, deskripsi, cek_stok, diskon_persen, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("isdsiis", $kategori_id, $nama_produk, $harga, $deskripsi, $cek_stok, $diskon_persen, $gambar);
    } else { 
        $id = (int)$id;
        $query = "UPDATE produk SET kategori_id=?, nama_produk=?, harga=?, deskripsi=?, cek_stok=?, diskon_persen=?, gambar=? WHERE id=?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("isdsiisi", $kategori_id, $nama_produk, $harga, $deskripsi, $cek_stok, $diskon_persen, $gambar, $id);
    }

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        die("Gagal menyimpan data ke database: " . $stmt->error);
    }
}


if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = (int)$_GET['id'];
    
    $stmt_select = $koneksi->prepare("SELECT gambar FROM produk WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $row = $result->fetch_assoc();
    
    if ($row && !empty($row['gambar']) && file_exists('../assets/images/' . $row['gambar'])) {
        unlink('../assets/images/' . $row['gambar']);
    }

    $stmt_delete_keys = $koneksi->prepare("DELETE FROM produk_keys WHERE produk_id = ?");
    $stmt_delete_keys->bind_param("i", $id);
    $stmt_delete_keys->execute();

    $stmt_delete = $koneksi->prepare("DELETE FROM produk WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    $stmt_delete->execute();

    header("Location: index.php");
    exit();
}
?>