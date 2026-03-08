<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: login.php");
  exit();
}
include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)$_POST['id'];
  $kode_voucher = trim($_POST['kode_voucher']);
  $tipe_diskon = $_POST['tipe_diskon'];
  $nilai_diskon = (float)$_POST['nilai_diskon'];
  $status = $_POST['status'];
  $tanggal_kadaluarsa = !empty($_POST['tanggal_kadaluarsa']) ? $_POST['tanggal_kadaluarsa'] : NULL;

    $kategori_ids = $_POST['kategori_ids'] ?? [];

    $current_voucher_id = 0; // Variabel untuk menampung ID voucher

  if ($id > 0) { // Update
    $stmt = $koneksi->prepare("UPDATE voucher SET kode_voucher = ?, tipe_diskon = ?, nilai_diskon = ?, status = ?, tanggal_kadaluarsa = ? WHERE id = ?");
    $stmt->bind_param("ssdssi", $kode_voucher, $tipe_diskon, $nilai_diskon, $status, $tanggal_kadaluarsa, $id);
        
        if ($stmt->execute()) {
            $current_voucher_id = $id; // Jika update, kita sudah tahu ID-nya
        }
  } else { // Insert
    $stmt = $koneksi->prepare("INSERT INTO voucher (kode_voucher, tipe_diskon, nilai_diskon, status, tanggal_kadaluarsa) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $kode_voucher, $tipe_diskon, $nilai_diskon, $status, $tanggal_kadaluarsa);
        
        if ($stmt->execute()) {
            $current_voucher_id = $koneksi->insert_id; // Ambil ID dari voucher yang BARU SAJA dibuat
        }
  }
  $stmt->close();

    if ($current_voucher_id > 0) {
        
        $stmt_delete_kat = $koneksi->prepare("DELETE FROM voucher_kategori WHERE voucher_id = ?");
        $stmt_delete_kat->bind_param("i", $current_voucher_id);
        $stmt_delete_kat->execute();
        $stmt_delete_kat->close();

        if (!empty($kategori_ids)) {
            $stmt_insert_kat = $koneksi->prepare("INSERT INTO voucher_kategori (voucher_id, kategori_id) VALUES (?, ?)");
            
            foreach ($kategori_ids as $kategori_id) {
                $kat_id_int = (int)$kategori_id;
                $stmt_insert_kat->bind_param("ii", $current_voucher_id, $kat_id_int);
                $stmt_insert_kat->execute();
            }
            $stmt_insert_kat->close();
        }

    header("Location: voucher.php?status=sukses");

    } else { // Jika query voucher utama gagal
    header("Location: voucher.php?status=gagal");
    }
  exit();
}


// Proses Hapus
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $stmt = $koneksi->prepare("DELETE FROM voucher WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    header("Location: voucher.php?status=dihapus");
  } else {
    header("Location: voucher.php?status=gagalhapus");
  }
  $stmt->close();
  exit();
}

header("Location: voucher.php");
exit();
?>