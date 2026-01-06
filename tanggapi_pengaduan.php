<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil detail pengaduan
$query = mysqli_query($koneksi, "
    SELECT 
        pengaduan.id_pengaduan,
        pengaduan.judul,
        pengaduan.isi_pengaduan,
        pengaduan.tanggal_pengaduan,
        pengaduan.status,
        users.nama,
        users.username
    FROM pengaduan
    JOIN users ON pengaduan.id_user = users.id_user
    WHERE pengaduan.id_pengaduan = '$id'
");

$pengaduan = mysqli_fetch_assoc($query);

if (!$pengaduan) {
    header("Location: dashboard_admin.php");
    exit;
}

// Cek apakah sudah ada tanggapan
$tanggapan_query = mysqli_query($koneksi, "
    SELECT isi_tanggapan, tanggal_tanggapan 
    FROM tanggapan 
    WHERE id_pengaduan = '$id'
");
$tanggapan_exist = mysqli_fetch_assoc($tanggapan_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanggapi Pengaduan - Sistem Pengaduan Masyarakat</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-left p {
            color: #666;
            font-size: 0.95rem;
        }

        .btn-back {
            background: #e0e0e0;
            color: #333;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #d0d0d0;
            transform: translateX(-3px);
        }

        /* Grid Layout */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        /* Detail Pengaduan Card */
        .detail-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .card-header h2 {
            color: #333;
            font-size: 1.3rem;
        }

        .card-header i {
            color: #667eea;
            font-size: 1.5rem;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-label {
            color: #666;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-value {
            color: #333;
            font-size: 1rem;
            padding: 12px 15px;
            background: #f8f9ff;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }

        .info-value.large {
            font-size: 1.1rem;
            font-weight: 600;
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

        /* Status Badge */
        .status {
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-diproses {
            background: linear-gradient(135deg, #fff5e6, #ffe0b3);
            color: #ff9800;
        }

        .status-selesai {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #4caf50;
        }

        /* Form Card */
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            resize: vertical;
            min-height: 200px;
            transition: all 0.3s ease;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .char-counter {
            text-align: right;
            color: #999;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Alert Box */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert i {
            font-size: 1.2rem;
        }

        /* Previous Response */
        .previous-response {
            background: linear-gradient(135deg, #e8f5e9, #f1f8f4);
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #4caf50;
            margin-bottom: 20px;
        }

        .response-header {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4caf50;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .response-date {
            color: #666;
            font-size: 0.85rem;
            margin-top: 10px;
        }

        /* Buttons */
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 10px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            width: 100%;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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

        .header, .content-grid {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .navbar-links {
                width: 100%;
                justify-content: center;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header-left h1 {
                font-size: 1.5rem;
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
            <div class="header-left">
                <h1>
                    <i class="fas fa-reply"></i>
                    Tanggapi Pengaduan
                </h1>
                <p>Berikan tanggapan untuk pengaduan masyarakat</p>
            </div>
            <a href="dashboard_admin.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            
            <!-- Detail Pengaduan -->
            <div class="detail-card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i>
                    <h2>Detail Pengaduan</h2>
                </div>

                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-hashtag"></i>
                        ID Pengaduan
                    </div>
                    <div class="info-value">#<?= htmlspecialchars($pengaduan['id_pengaduan']); ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-user"></i>
                        Nama Pelapor
                    </div>
                    <div class="info-value"><?= htmlspecialchars($pengaduan['nama']); ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-calendar"></i>
                        Tanggal Pengaduan
                    </div>
                    <div class="info-value">
                        <?php
                        $date = new DateTime($pengaduan['tanggal_pengaduan']);
                        echo $date->format('l, d F Y');
                        ?>
                    </div>
                </div>

                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-info-circle"></i>
                        Status
                    </div>
                    <div>
                        <?php if ($pengaduan['status'] == 'diproses') { ?>
                            <span class="status status-diproses">
                                <i class="fas fa-spinner fa-spin"></i> Sedang Diproses
                            </span>
                        <?php } else { ?>
                            <span class="status status-selesai">
                                <i class="fas fa-check"></i> Selesai
                            </span>
                        <?php } ?>
                    </div>
                </div>

                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-heading"></i>
                        Judul Pengaduan
                    </div>
                    <div class="info-value large"><?= htmlspecialchars($pengaduan['judul']); ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-file-alt"></i>
                        Isi Pengaduan
                    </div>
                    <div class="pengaduan-content"><?= htmlspecialchars($pengaduan['isi_pengaduan']); ?></div>
                </div>
            </div>

            <!-- Form Tanggapan -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-comment-dots"></i>
                    <h2><?= $pengaduan['status'] == 'selesai' ? 'Tanggapan' : 'Form Tanggapan'; ?></h2>
                </div>

                <?php if ($pengaduan['status'] == 'selesai') { ?>
                    <!-- Jika sudah selesai, hanya tampilkan tanggapan -->
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>Pengaduan ini telah selesai ditanggapi dan tidak dapat diubah lagi.</span>
                    </div>

                    <?php if ($tanggapan_exist) { ?>
                        <div class="previous-response">
                            <div class="response-header">
                                <i class="fas fa-reply"></i>
                                Tanggapan Admin:
                            </div>
                            <div style="white-space: pre-wrap; line-height: 1.8;"><?= htmlspecialchars($tanggapan_exist['isi_tanggapan']); ?></div>
                            <div class="response-date">
                                <i class="fas fa-clock"></i>
                                Ditanggapi pada: <?php
                                $date = new DateTime($tanggapan_exist['tanggal_tanggapan']);
                                echo $date->format('d F Y');
                                ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Data tanggapan tidak ditemukan.</span>
                        </div>
                    <?php } ?>

                <?php } else { ?>
                    <!-- Jika masih diproses, tampilkan form -->
                    <?php if ($tanggapan_exist) { ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>Pengaduan ini sudah ditanggapi. Anda dapat memperbarui tanggapan di bawah ini.</span>
                        </div>

                        <div class="previous-response">
                            <div class="response-header">
                                <i class="fas fa-history"></i>
                                Tanggapan Sebelumnya:
                            </div>
                            <div><?= htmlspecialchars($tanggapan_exist['isi_tanggapan']); ?></div>
                            <div class="response-date">
                                <i class="fas fa-clock"></i>
                                Ditanggapi pada: <?php
                                $date = new DateTime($tanggapan_exist['tanggal_tanggapan']);
                                echo $date->format('d F Y');
                                ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Pengaduan ini belum mendapat tanggapan. Silakan berikan tanggapan Anda.</span>
                        </div>
                    <?php } ?>

                    <form action="simpan_tanggapan.php" method="post" id="responseForm">
                        <input type="hidden" name="id_pengaduan" value="<?= $pengaduan['id_pengaduan']; ?>">

                        <div class="form-group">
                            <label>
                                <i class="fas fa-comment-medical"></i>
                                Isi Tanggapan
                            </label>
                            <textarea 
                                name="isi_tanggapan" 
                                id="isiTanggapan"
                                placeholder="Tuliskan tanggapan Anda untuk pengaduan ini..."
                                required
                                maxlength="1000"
                            ><?= $tanggapan_exist ? htmlspecialchars($tanggapan_exist['isi_tanggapan']) : ''; ?></textarea>
                            <div class="char-counter">
                                <span id="charCount">0</span> / 1000 karakter
                            </div>
                        </div>

                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-paper-plane"></i>
                            <?= $tanggapan_exist ? 'Perbarui Tanggapan' : 'Kirim Tanggapan'; ?>
                        </button>
                    </form>
                <?php } ?>
            </div>

        </div>

    </div>

    <script>
        <?php if ($pengaduan['status'] != 'selesai') { ?>
        // Character counter
        const textarea = document.getElementById('isiTanggapan');
        const charCount = document.getElementById('charCount');

        function updateCharCount() {
            const count = textarea.value.length;
            charCount.textContent = count;
            
            if (count > 900) {
                charCount.style.color = '#f44336';
            } else if (count > 800) {
                charCount.style.color = '#ff9800';
            } else {
                charCount.style.color = '#999';
            }
        }

        textarea.addEventListener('input', updateCharCount);
        
        // Initial count
        updateCharCount();

        // Form validation
        document.getElementById('responseForm').addEventListener('submit', function(e) {
            const textarea = document.getElementById('isiTanggapan');
            const submitBtn = document.getElementById('submitBtn');
            
            if (textarea.value.trim().length < 10) {
                e.preventDefault();
                alert('Tanggapan minimal 10 karakter!');
                textarea.focus();
                return false;
            }

            // Disable button to prevent double submit
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        });
        <?php } ?>
    </script>

</body>
</html>