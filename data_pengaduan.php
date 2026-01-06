<?php
include "../config/koneksi.php";

$query = mysqli_query($koneksi, "
    SELECT users.nama, pengaduan.judul, pengaduan.tanggal_pengaduan, pengaduan.status
    FROM pengaduan
    JOIN users ON pengaduan.id_user = users.id_user
");
?>

<table border="1">
    <tr>
        <th>Nama</th>
        <th>Judul</th>
        <th>Tanggal</th>
        <th>Status</th>
    </tr>

    <?php while ($data = mysqli_fetch_array($query)) { ?>
    <tr>
        <td><?= $data['nama']; ?></td>
        <td><?= $data['judul']; ?></td>
        <td><?= $data['tanggal_pengaduan']; ?></td>
        <td><?= $data['status']; ?></td>
    </tr>
    <?php } ?>
</table>