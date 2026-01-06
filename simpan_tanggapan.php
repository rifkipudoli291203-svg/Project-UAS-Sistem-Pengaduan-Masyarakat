<?php
session_start();
include "koneksi.php";

// CEK DATA POST (ANTI ERROR)
if (!isset($_POST['id_pengaduan']) || !isset($_POST['isi_tanggapan'])) {
    die("Data tidak lengkap");
}

$id_pengaduan  = $_POST['id_pengaduan'];
$isi_tanggapan = $_POST['isi_tanggapan'];

// SIMPAN TANGGAPAN
mysqli_query($koneksi, "
    INSERT INTO tanggapan (id_pengaduan, isi_tanggapan, tanggal_tanggapan)
    VALUES ('$id_pengaduan', '$isi_tanggapan', CURDATE())
");

// UPDATE STATUS PENGADUAN
mysqli_query($koneksi, "
    UPDATE pengaduan 
    SET status='selesai'
    WHERE id_pengaduan='$id_pengaduan'
");

header("Location: dashboard_admin.php");
exit;
?>