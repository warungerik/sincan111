<?php
$host = 'localhost';
$user = 'warf9928_store'; 
$pass = 'Advan.ku123';
$db   = 'warf9928_store';

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>