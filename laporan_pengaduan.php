<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// ====== QUERY REPORT ======
$query_sql = "
    SELECT users.nama, pengaduan.judul, pengaduan.tanggal_pengaduan, pengaduan.status
    FROM pengaduan
    JOIN users ON pengaduan.id_user = users.id_user
";

$where = [];
$filter_active = false;

// Filter status
if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    $where[] = "pengaduan.status='$status'";
    $filter_active = true;
}

// Filter tanggal
if (!empty($_GET['dari']) && !empty($_GET['sampai'])) {
    $dari = mysqli_real_escape_string($koneksi, $_GET['dari']);
    $sampai = mysqli_real_escape_string($koneksi, $_GET['sampai']);
    $where[] = "pengaduan.tanggal_pengaduan BETWEEN '$dari' AND '$sampai'";
    $filter_active = true;
}

// Gabungkan WHERE jika ada filter
if (count($where) > 0) {
    $query_sql .= " WHERE " . implode(" AND ", $where);
}

$query_sql .= " ORDER BY pengaduan.tanggal_pengaduan DESC";

$query = mysqli_query($koneksi, $query_sql);
$total_data = mysqli_num_rows($query);

// Hitung statistik
$total_pengaduan = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan")
)['total'];

$total_diproses = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status='diproses'")
)['total'];

$total_selesai = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status='selesai'")
)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengaduan - Sistem Pengaduan Masyarakat</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .navbar-links {
            display: flex;
            gap: 15px;
        }

        .navbar a {
            text-decoration: none;
            color: #333;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .navbar a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        /* Container */
        .container {
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header p {
            color: #666;
            font-size: 1rem;
        }

        /* Statistics Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card-link {
            text-decoration: none;
            display: block;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            border: 3px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.active {
            border: 3px solid #667eea;
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
        }

        .stat-card.active::before {
            content: '';
            position: absolute;
            top: -3px;
            right: -3px;
            width: 24px;
            height: 24px;
            background: #667eea;
            border-radius: 0 12px 0 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card.active::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 1px;
            right: 2px;
            color: white;
            font-size: 0.7rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-card.total .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.proses .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card.selesai .stat-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-info h3 {
            color: #666;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-info p {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-header h2 {
            color: #333;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-toggle {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .filter-toggle:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #666;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-group select,
        .form-group input {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #666;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        /* Export Buttons */
        .export-section {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .result-info {
            color: #666;
            font-weight: 500;
        }

        .result-info span {
            color: #667eea;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .export-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-export {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-pdf {
            background: #f44336;
            color: white;
        }

        .btn-pdf:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        .btn-excel {
            background: #4caf50;
            color: white;
        }

        .btn-excel:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .btn-print {
            background: #2196f3;
            color: white;
        }

        .btn-print:hover {
            background: #1976d2;
            transform: translateY(-2px);
        }

        /* Table Container */
        .table-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        th:first-child {
            border-top-left-radius: 10px;
        }

        th:last-child {
            border-top-right-radius: 10px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        tr:hover {
            background: #f8f9ff;
        }

        /* Status Badge */
        .status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-diproses {
            background: linear-gradient(135deg, #fff5e6, #ffe0b3);
            color: #ff9800;
        }

        .status-selesai {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #4caf50;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        /* Active Filter Badge */
        .filter-badge {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header, .stats-cards, .filter-section, .export-section, .table-container {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .navbar-links {
                width: 100%;
                justify-content: center;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .export-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .export-buttons {
                width: 100%;
                flex-wrap: wrap;
            }

            .btn-export {
                flex: 1;
            }

            table {
                font-size: 0.85rem;
            }

            th, td {
                padding: 10px;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .navbar, .filter-section, .export-section {
                display: none;
            }

            .container {
                max-width: 100%;
            }

            .table-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-bullhorn"></i>
            <span>Pengaduan Masyarakat</span>
        </div>
        <div class="navbar-links">
            <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="laporan_pengaduan.php"><i class="fas fa-file-alt"></i> Laporan</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">

        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-chart-bar"></i>
                Laporan Pengaduan Masyarakat
                <?php if ($filter_active) { ?>
                    <span class="filter-badge">
                        <i class="fas fa-filter"></i> Filter Aktif
                    </span>
                <?php } ?>
            </h1>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <a href="laporan_pengaduan.php" class="stat-card-link">
                <div class="stat-card total <?= !$filter_active ? 'active' : ''; ?>">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Pengaduan</h3>
                        <p><?= $total_pengaduan; ?></p>
                    </div>
                </div>
            </a>

            <a href="laporan_pengaduan.php?status=diproses" class="stat-card-link">
                <div class="stat-card proses <?= (isset($_GET['status']) && $_GET['status'] == 'diproses') ? 'active' : ''; ?>">
                    <div class="stat-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Sedang Diproses</h3>
                        <p><?= $total_diproses; ?></p>
                    </div>
                </div>
            </a>

            <a href="laporan_pengaduan.php?status=selesai" class="stat-card-link">
                <div class="stat-card selesai <?= (isset($_GET['status']) && $_GET['status'] == 'selesai') ? 'active' : ''; ?>">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Selesai</h3>
                        <p><?= $total_selesai; ?></p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
                <h2>
                    <i class="fas fa-filter"></i>
                    Filter Laporan
                </h2>
            </div>

            <form method="get" class="filter-form">
                <div class="form-group">
                    <label>
                        <i class="fas fa-info-circle"></i>
                        Status Pengaduan
                    </label>
                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="diproses" <?= (isset($_GET['status']) && $_GET['status'] == 'diproses') ? 'selected' : ''; ?>>Diproses</option>
                        <option value="selesai" <?= (isset($_GET['status']) && $_GET['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-calendar-alt"></i>
                        Dari Tanggal
                    </label>
                    <input type="date" name="dari" value="<?= $_GET['dari'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-calendar-check"></i>
                        Sampai Tanggal
                    </label>
                    <input type="date" name="sampai" value="<?= $_GET['sampai'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <a href="laporan_pengaduan.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <div class="result-info">
                Menampilkan <span><?= $total_data; ?></span> data pengaduan
            </div>
            <div class="export-buttons">
                <button class="btn-export btn-pdf" onclick="alert('Fitur export PDF dalam pengembangan')">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button class="btn-export btn-excel" onclick="alert('Fitur export Excel dalam pengembangan')">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button class="btn-export btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> No</th>
                        <th><i class="fas fa-user"></i> Nama Pelapor</th>
                        <th><i class="fas fa-heading"></i> Judul Pengaduan</th>
                        <th><i class="fas fa-calendar"></i> Tanggal</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($total_data > 0) {
                        $no = 1;
                        while ($data = mysqli_fetch_assoc($query)) { 
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($data['nama']); ?></td>
                        <td><?= htmlspecialchars($data['judul']); ?></td>
                        <td><?= date('d M Y', strtotime($data['tanggal_pengaduan'])); ?></td>
                        <td>
                            <?php if ($data['status'] == 'diproses') { ?>
                                <span class="status status-diproses">
                                    <i class="fas fa-spinner fa-spin"></i> Diproses
                                </span>
                            <?php } else { ?>
                                <span class="status status-selesai">
                                    <i class="fas fa-check"></i> Selesai
                                </span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>Tidak ada data</h3>
                                <p>Tidak ada pengaduan yang sesuai dengan filter yang dipilih</p>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>