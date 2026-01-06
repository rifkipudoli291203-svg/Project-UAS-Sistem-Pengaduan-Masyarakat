<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}
?>

<h2>Form Pengaduan</h2>

<form action="simpan_pengaduan.php" method="post">
    <label>Judul Pengaduan</label><br>
    <input type="text" name="judul" required><br><br>

    <label>Isi Pengaduan</label><br>
    <textarea name="isi_pengaduan" required></textarea><br><br>

    <button type="submit">Kirim</button>
</form>