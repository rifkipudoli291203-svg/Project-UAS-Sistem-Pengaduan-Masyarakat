<?php
session_start();
include "koneksi.php";

$id_user = $_SESSION['id_user'];
$judul   = $_POST['judul'];
$isi     = $_POST['isi_pengaduan'];

$query = "INSERT INTO pengaduan 
          (id_user, judul, isi_pengaduan, tanggal_pengaduan, status)
          VALUES 
          ('$id_user', '$judul', '$isi', CURDATE(), 'diproses')";

mysqli_query($koneksi, $query);

header("Location: dashboard_user.php");
?>