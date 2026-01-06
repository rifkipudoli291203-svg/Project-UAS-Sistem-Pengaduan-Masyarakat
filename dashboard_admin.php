<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// ===== DASHBOARD CARD QUERY =====
$total = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan")
)['total'];

$diproses = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status='diproses'")
)['total'];

$selesai = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status='selesai'")
)['total'];

// ===== FILTER STATUS =====
$where = "";
$label = "Semua Pengaduan";

if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    $where = "WHERE pengaduan.status='$status'";
    $label = ucfirst($status);
}

// ===== TABEL DATA =====
$query = mysqli_query($koneksi, "
    SELECT pengaduan.id_pengaduan, users.nama, pengaduan.judul, pengaduan.isi_pengaduan, 
           pengaduan.status, pengaduan.tanggal_pengaduan
    FROM pengaduan
    JOIN users ON pengaduan.id_user = users.id_user
    $where
    ORDER BY pengaduan.tanggal_pengaduan DESC
");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Pengaduan Masyarakat</title>
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
        }

        .header p {
            color: #666;
            font-size: 1rem;
        }

        /* Cards */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .card.total .card-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .card.proses .card-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .card.selesai .card-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .card h3 {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card p {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
        }

        .card-link {
            text-decoration: none;
        }

        /* Table Container */
        .table-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h2 {
            color: #333;
            font-size: 1.5rem;
        }

        .filter-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
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
            white-space: nowrap;
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
            display: inline-block;
        }

        .status-diproses {
            background: linear-gradient(135deg, #fff5e6, #ffe0b3);
            color: #ff9800;
        }

        .status-selesai {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #4caf50;
        }

        /* Button */
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-view {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 6px 15px;
            font-size: 0.85rem;
        }

        .btn-view:hover {
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        /* Isi Pengaduan Column */
        .isi-preview {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #666;
            font-size: 0.9rem;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease-out;
            max-height: 85vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-info {
            margin-bottom: 25px;
        }

        .info-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #667eea;
            min-width: 150px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            color: #333;
            flex: 1;
        }

        .pengaduan-content {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            line-height: 1.8;
            color: #333;
            border-left: 4px solid #667eea;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.2);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
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

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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

        .card, .table-container {
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

            .cards {
                grid-template-columns: 1fr;
            }

            .table-container {
                padding: 20px;
            }

            table {
                font-size: 0.85rem;
            }

            th, td {
                padding: 10px 8px;
            }

            .isi-preview {
                max-width: 120px;
            }

            .modal-content {
                width: 95%;
                margin: 10% auto;
            }

            .modal-body {
                padding: 20px;
            }

            .info-item {
                flex-direction: column;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 5px;
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
            <h1>Dashboard Administrator</h1>
        </div>

        <!-- Dashboard Cards -->
        <div class="cards">
            <a href="dashboard_admin.php" class="card-link">
                <div class="card total">
                    <div class="card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>Total Pengaduan</h3>
                    <p><?= $total; ?></p>
                </div>
            </a>

            <a href="dashboard_admin.php?status=diproses" class="card-link">
                <div class="card proses">
                    <div class="card-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3>Sedang Diproses</h3>
                    <p><?= $diproses; ?></p>
                </div>
            </a>

            <a href="dashboard_admin.php?status=selesai" class="card-link">
                <div class="card selesai">
                    <div class="card-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Selesai</h3>
                    <p><?= $selesai; ?></p>
                </div>
            </a>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>Data Pengaduan</h2>
                <div class="filter-badge">
                    <i class="fas fa-filter"></i> <?= $label; ?>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i> Nama</th>
                        <th><i class="fas fa-heading"></i> Judul</th>
                        <th><i class="fas fa-file-alt"></i> Isi Pengaduan</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-cog"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($query) > 0) {
                        while ($data = mysqli_fetch_assoc($query)) { 
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($data['nama']); ?></td>
                        <td><?= htmlspecialchars($data['judul']); ?></td>
                        <td>
                            <button class="btn btn-view" onclick="showDetail(
                                '<?= htmlspecialchars($data['nama'], ENT_QUOTES); ?>',
                                '<?= htmlspecialchars($data['judul'], ENT_QUOTES); ?>',
                                `<?= htmlspecialchars($data['isi_pengaduan'], ENT_QUOTES); ?>`,
                                '<?= htmlspecialchars($data['tanggal_pengaduan']); ?>',
                                '<?= htmlspecialchars($data['status']); ?>'
                            )">
                                <i class="fas fa-eye"></i> Lihat Isi
                            </button>
                        </td>
                        <td>
                            <?php if ($data['status'] == 'diproses') { ?>
                                <span class="status status-diproses">
                                    <i class="fas fa-spinner"></i> Diproses
                                </span>
                            <?php } else { ?>
                                <span class="status status-selesai">
                                    <i class="fas fa-check"></i> Selesai
                                </span>
                            <?php } ?>
                        </td>
                        <td>
                            <a class="btn" href="tanggapi_pengaduan.php?id=<?= $data['id_pengaduan']; ?>">
                                <i class="fas fa-reply"></i> Tanggapi
                            </a>
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
                                <h3>Tidak ada data pengaduan</h3>
                                <p>Belum ada pengaduan yang masuk ke sistem</p>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal Detail Pengaduan -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-file-alt"></i> Detail Pengaduan</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="modal-info">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i> Nama Pelapor
                        </div>
                        <div class="info-value" id="modalNama"></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i> Tanggal
                        </div>
                        <div class="info-value" id="modalTanggal"></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-heading"></i> Judul
                        </div>
                        <div class="info-value" id="modalJudul"></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-info-circle"></i> Status
                        </div>
                        <div class="info-value" id="modalStatus"></div>
                    </div>
                </div>
                <div class="info-label" style="margin-bottom: 10px;">
                    <i class="fas fa-file-alt"></i> Isi Pengaduan
                </div>
                <div class="pengaduan-content" id="modalIsi"></div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(nama, judul, isi, tanggal, status) {
            document.getElementById('modalNama').textContent = nama;
            document.getElementById('modalJudul').textContent = judul;
            document.getElementById('modalIsi').textContent = isi;
            document.getElementById('modalTanggal').textContent = formatTanggal(tanggal);
            
            // Format status
            const statusElement = document.getElementById('modalStatus');
            if (status === 'diproses') {
                statusElement.innerHTML = '<span class="status status-diproses"><i class="fas fa-spinner"></i> Diproses</span>';
            } else {
                statusElement.innerHTML = '<span class="status status-selesai"><i class="fas fa-check"></i> Selesai</span>';
            }
            
            document.getElementById('detailModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function formatTanggal(tanggal) {
            const date = new Date(tanggal);
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
            };
            return date.toLocaleDateString('id-ID', options);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('detailModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>

</body>
</html>