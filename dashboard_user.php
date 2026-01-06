<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'] ?? 'User';

// ===== DASHBOARD USER CARD =====
$total = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE id_user='$id_user'")
)['total'];

$diproses = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE id_user='$id_user' AND status='diproses'")
)['total'];

$selesai = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE id_user='$id_user' AND status='selesai'")
)['total'];

// ===== QUERY DENGAN JOIN KE TABEL TANGGAPAN =====
$query = mysqli_query($koneksi, "
    SELECT 
        p.id_pengaduan, 
        p.judul, 
        p.isi_pengaduan, 
        p.status, 
        p.tanggal_pengaduan,
        t.isi_tanggapan,
        t.tanggal_tanggapan
    FROM pengaduan p
    LEFT JOIN tanggapan t ON p.id_pengaduan = t.id_pengaduan
    WHERE p.id_user='$id_user'
    ORDER BY p.tanggal_pengaduan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Sistem Pengaduan Masyarakat</title>
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
            align-items: center;
        }

        .navbar a {
            text-decoration: none;
            color: #333;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: #f8f9ff;
            border-radius: 20px;
            color: #667eea;
            font-weight: 600;
        }

        /* Container */
        .container {
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Welcome Section */
        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .welcome-text h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .welcome-text p {
            color: #666;
            font-size: 1rem;
        }

        .btn-new-complaint {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-new-complaint:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
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
        }

        .card.total::before {
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .card.proses::before {
            background: linear-gradient(90deg, #f093fb, #f5576c);
        }

        .card.selesai::before {
            background: linear-gradient(90deg, #4facfe, #00f2fe);
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
            display: flex;
            align-items: center;
            gap: 10px;
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

        /* Button */
        .btn-detail {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 8px 18px;
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

        .btn-detail:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        .btn-reply {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            padding: 8px 18px;
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

        .btn-reply:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }

        .btn-disabled {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
        }

        .btn-disabled:hover {
            transform: none;
            box-shadow: none;
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
            margin-bottom: 20px;
        }

        .tanggapan-section {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px dashed #e0e0e0;
        }

        .tanggapan-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            color: #4caf50;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .tanggapan-content {
            background: linear-gradient(135deg, #e8f5e9, #f1f8f4);
            padding: 20px;
            border-radius: 10px;
            line-height: 1.8;
            color: #333;
            border-left: 4px solid #4caf50;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .tanggapan-date {
            font-size: 0.85rem;
            color: #666;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .no-reply {
            background: #fff3cd;
            padding: 15px;
            border-radius: 10px;
            color: #856404;
            border-left: 4px solid #ffc107;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .empty-state h3 {
            margin-bottom: 10px;
            color: #666;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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

        .card, .table-container, .welcome-section {
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
                flex-wrap: wrap;
            }

            .welcome-section {
                text-align: center;
                padding: 25px;
            }

            .welcome-text h1 {
                font-size: 1.5rem;
            }

            .btn-new-complaint {
                width: 100%;
                justify-content: center;
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
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($nama_user); ?></span>
            </div>
            <a href="dashboard_user.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="form_pengaduan.php">
                <i class="fas fa-plus-circle"></i> Buat Pengaduan
            </a>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="container">

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Selamat Datang, <?= htmlspecialchars($nama_user); ?>! 👋</h1>
            </div>
            <a href="form_pengaduan.php" class="btn-new-complaint">
                <i class="fas fa-plus-circle"></i>
                Buat Pengaduan Baru
            </a>
        </div>

        <!-- Dashboard Cards -->
        <div class="cards">
            <div class="card total">
                <div class="card-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>Total Pengaduan</h3>
                <p><?= $total; ?></p>
            </div>

            <div class="card proses">
                <div class="card-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h3>Sedang Diproses</h3>
                <p><?= $diproses; ?></p>
            </div>

            <div class="card selesai">
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Selesai</h3>
                <p><?= $selesai; ?></p>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Riwayat Pengaduan Saya
                </h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-heading"></i> Judul Pengaduan</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-calendar"></i> Tanggal</th>
                        <th><i class="fas fa-reply"></i> Balasan</th>
                        <th><i class="fas fa-eye"></i> Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($query) > 0) {
                        while ($data = mysqli_fetch_assoc($query)) { 
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($data['judul']); ?></td>
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
                        <td><?= date('d M Y', strtotime($data['tanggal_pengaduan'])); ?></td>
                        <td>
                            <?php if ($data['status'] == 'selesai' && !empty($data['isi_tanggapan'])) { ?>
                                <button class="btn-reply" onclick="showReply(
                                    '<?= htmlspecialchars($data['judul'], ENT_QUOTES); ?>',
                                    `<?= htmlspecialchars($data['isi_tanggapan'], ENT_QUOTES); ?>`,
                                    '<?= htmlspecialchars($data['tanggal_tanggapan']); ?>'
                                )">
                                    <i class="fas fa-comment-dots"></i> Lihat Balasan
                                </button>
                            <?php } else { ?>
                                <button class="btn-detail btn-disabled" disabled>
                                    <i class="fas fa-hourglass-half"></i> Belum Ada
                                </button>
                            <?php } ?>
                        </td>
                        <td>
                            <button class="btn-detail" onclick="showDetail(
                                '<?= htmlspecialchars($data['judul'], ENT_QUOTES); ?>',
                                `<?= htmlspecialchars($data['isi_pengaduan'], ENT_QUOTES); ?>`,
                                '<?= htmlspecialchars($data['tanggal_pengaduan']); ?>',
                                '<?= htmlspecialchars($data['status']); ?>',
                                `<?= !empty($data['isi_tanggapan']) ? htmlspecialchars($data['isi_tanggapan'], ENT_QUOTES) : ''; ?>`,
                                '<?= !empty($data['tanggal_tanggapan']) ? htmlspecialchars($data['tanggal_tanggapan']) : ''; ?>'
                            )">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </button>
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
                                <h3>Belum Ada Pengaduan</h3>
                                <p>Anda belum membuat pengaduan. Klik tombol "Buat Pengaduan Baru" untuk memulai.</p>
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
                <span class="close" onclick="closeModal('detailModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="modal-info">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i> Tanggal Dibuat
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

                <!-- Tanggapan Section -->
                <div id="tanggapanSection" class="tanggapan-section" style="display: none;">
                    <div class="tanggapan-header">
                        <i class="fas fa-reply"></i>
                        <span>Balasan dari Admin</span>
                    </div>
                    <div class="tanggapan-content" id="modalTanggapan"></div>
                    <div class="tanggapan-date" id="modalTanggapanDate"></div>
                </div>

                <div id="noReplySection" style="display: none;">
                    <div class="no-reply">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Pengaduan Anda sedang diproses. Mohon menunggu balasan dari admin.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Balasan -->
    <div id="replyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-comment-dots"></i> Balasan Admin</h2>
                <span class="close" onclick="closeModal('replyModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-heading"></i> Judul Pengaduan
                    </div>
                    <div class="info-value" id="replyJudul"></div>
                </div>
                <div class="tanggapan-header">
                    <i class="fas fa-reply"></i>
                    <span>Balasan dari Admin</span>
                </div>
                <div class="tanggapan-content" id="replyTanggapan"></div>
                <div class="tanggapan-date" id="replyTanggapanDate"></div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(judul, isi, tanggal, status, tanggapan, tanggalTanggapan) {
            document.getElementById('modalJudul').textContent = judul;
            document.getElementById('modalIsi').textContent = isi;
            document.getElementById('modalTanggal').textContent = formatTanggal(tanggal);
            
            // Format status
            const statusElement = document.getElementById('modalStatus');
            if (status === 'diproses') {
                statusElement.innerHTML = '<span class="status status-diproses"><i class="fas fa-spinner fa-spin"></i> Sedang Diproses</span>';
                document.getElementById('noReplySection').style.display = 'block';
                document.getElementById('tanggapanSection').style.display = 'none';
            } else {
                statusElement.innerHTML = '<span class="status status-selesai"><i class="fas fa-check"></i> Selesai Ditangani</span>';
                
                if (tanggapan) {
                    document.getElementById('tanggapanSection').style.display = 'block';
                    document.getElementById('modalTanggapan').textContent = tanggapan;
                    document.getElementById('modalTanggapanDate').innerHTML = '<i class="fas fa-clock"></i> Ditanggapi pada: ' + formatTanggal(tanggalTanggapan);
                    document.getElementById('noReplySection').style.display = 'none';
                } else {
                    document.getElementById('tanggapanSection').style.display = 'none';
                    document.getElementById('noReplySection').style.display = 'block';
                }
            }
            
            document.getElementById('detailModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function showReply(judul, tanggapan, tanggalTanggapan) {
            document.getElementById('replyJudul').textContent = judul;
            document.getElementById('replyTanggapan').textContent = tanggapan;
            document.getElementById('replyTanggapanDate').innerHTML = '<i class="fas fa-clock"></i> Ditanggapi pada: ' + formatTanggal(tanggalTanggapan);
            
            document.getElementById('replyModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function formatTanggal(tanggal) {
            if (!tanggal) return '-';
            const date = new Date(tanggal);
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.style.display = 'none';
                });
                document.body.style.overflow = 'auto';
            }
        });
    </script>

</body>
</html>