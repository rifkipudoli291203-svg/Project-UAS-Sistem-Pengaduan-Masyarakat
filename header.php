<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Sistem Pengaduan'; ?></title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICON -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f6fa;
        }
        .card-dashboard {
            border-radius: 12px;
            transition: .2s;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-clipboard-data"></i> Pengaduan
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">

                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_admin.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan_pengaduan.php">Laporan</a>
                    </li>
                <?php } ?>

                <?php if ($_SESSION['role'] == 'user') { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_user.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="form_pengaduan.php">Pengaduan</a>
                    </li>
                <?php } ?>

                <li class="nav-item">
                    <a class="nav-link text-warning" href="logout.php">Logout</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<div class="container">
