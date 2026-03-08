<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    exit('Akses ditolak');
}
include '../includes/koneksi.php';

if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = $_POST['nama_kategori'];
    $stmt = $koneksi->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
    $stmt->bind_param("s", $nama_kategori);
    $stmt->execute();
    header("Location: kategori.php");
    exit();
}

if (isset($_POST['update_kategori'])) {
    $id = $_POST['id'];
    $nama_kategori = $_POST['nama_kategori'];
    $stmt = $koneksi->prepare("UPDATE kategori SET nama_kategori = ? WHERE id = ?");
    $stmt->bind_param("si", $nama_kategori, $id);
    $stmt->execute();
    header("Location: kategori.php");
    exit();
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    $stmt = $koneksi->prepare("DELETE FROM kategori WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: kategori.php");
    exit();
}