<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pengaduan Baru - Sistem Pengaduan Masyarakat</title>
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
            max-width: 900px;
            margin: 0 auto;
        }

        /* Form Container */
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.5s ease-out;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .form-header h1 i {
            color: #667eea;
        }

        .form-header p {
            color: #666;
            font-size: 1rem;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 30px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
            font-size: 1rem;
        }

        .form-group label i {
            color: #667eea;
            font-size: 1.1rem;
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
            background: #f8f9ff;
        }

        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-group textarea {
            min-height: 200px;
            resize: vertical;
            line-height: 1.6;
        }

        /* Character Counter */
        .char-counter {
            text-align: right;
            font-size: 0.85rem;
            color: #999;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 5px;
        }

        .char-counter.warning {
            color: #ff9800;
        }

        .char-counter.danger {
            color: #f44336;
        }

        /* Buttons Container */
        .buttons-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
        }

        /* Buttons */
        .btn {
            padding: 15px 40px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            flex: 1;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        .btn-back {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #e0e5ec 0%, #b8c6db 100%);
        }

        /* Loading State */
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-submit.loading {
            position: relative;
        }

        .btn-submit.loading i {
            animation: spin 1s linear infinite;
        }

        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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

            .form-container {
                padding: 25px;
            }

            .form-header h1 {
                font-size: 1.5rem;
                flex-direction: column;
            }

            .buttons-container {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Success Message Animation */
        .success-message {
            display: none;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            animation: slideDown 0.5s ease-out;
        }

        .success-message.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
        <div class="form-container">
            <div class="form-header">
                <h1>
                    <i class="fas fa-file-alt"></i>
                    Buat Pengaduan Baru
                </h1>
                <p>Sampaikan keluhan atau aspirasi Anda kepada kami</p>
            </div>

            <!-- Form -->
            <form action="simpan_pengaduan.php" method="post" id="formPengaduan">
                <input type="hidden" name="id_user" value="<?= $id_user; ?>">

                <!-- Judul Pengaduan -->
                <div class="form-group">
                    <label for="judul">
                        <i class="fas fa-heading"></i>
                        Judul Pengaduan <span style="color: red;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="judul" 
                        name="judul" 
                        required
                        maxlength="100"
                    >
                    <div class="char-counter" id="judulCounter">
                        <i class="fas fa-keyboard"></i>
                        <span>0 / 100 karakter</span>
                    </div>
                </div>

                <!-- Isi Pengaduan -->
                <div class="form-group">
                    <label for="isi_pengaduan">
                        <i class="fas fa-align-left"></i>
                        Isi Pengaduan <span style="color: red;">*</span>
                    </label>
                    <textarea 
                        id="isi_pengaduan" 
                        name="isi_pengaduan" 
                        required
                        maxlength="1000"
                    ></textarea>
                    <div class="char-counter" id="isiCounter">
                        <i class="fas fa-keyboard"></i>
                        <span>0 / 1000 karakter</span>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="buttons-container">
                    <a href="dashboard_user.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-submit" id="btnSubmit">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Character Counter untuk Judul
        const judulInput = document.getElementById('judul');
        const judulCounter = document.getElementById('judulCounter');
        
        judulInput.addEventListener('input', function() {
            const length = this.value.length;
            const max = 100;
            judulCounter.querySelector('span').textContent = `${length} / ${max} karakter`;
            
            if (length > max * 0.9) {
                judulCounter.classList.add('danger');
                judulCounter.classList.remove('warning');
            } else if (length > max * 0.7) {
                judulCounter.classList.add('warning');
                judulCounter.classList.remove('danger');
            } else {
                judulCounter.classList.remove('warning', 'danger');
            }
        });

        // Character Counter untuk Isi
        const isiInput = document.getElementById('isi_pengaduan');
        const isiCounter = document.getElementById('isiCounter');
        
        isiInput.addEventListener('input', function() {
            const length = this.value.length;
            const max = 1000;
            isiCounter.querySelector('span').textContent = `${length} / ${max} karakter`;
            
            if (length > max * 0.9) {
                isiCounter.classList.add('danger');
                isiCounter.classList.remove('warning');
            } else if (length > max * 0.7) {
                isiCounter.classList.add('warning');
                isiCounter.classList.remove('danger');
            } else {
                isiCounter.classList.remove('warning', 'danger');
            }
        });

        // Form Submission dengan Loading State
        const form = document.getElementById('formPengaduan');
        const btnSubmit = document.getElementById('btnSubmit');
        
        form.addEventListener('submit', function(e) {
            // Validasi
            if (judulInput.value.trim() === '' || isiInput.value.trim() === '') {
                e.preventDefault();
                alert('Mohon isi semua field yang wajib diisi!');
                return;
            }

            // Loading state
            btnSubmit.disabled = true;
            btnSubmit.classList.add('loading');
            btnSubmit.innerHTML = '<i class="fas fa-spinner"></i> Mengirim...';
        });

        // Konfirmasi sebelum meninggalkan halaman jika ada perubahan
        let formChanged = false;
        
        judulInput.addEventListener('input', () => formChanged = true);
        isiInput.addEventListener('input', () => formChanged = true);
        
        window.addEventListener('beforeunload', function(e) {
            if (formChanged && judulInput.value.trim() !== '' && isiInput.value.trim() !== '') {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Prevent form changed flag when submitting
        form.addEventListener('submit', function() {
            formChanged = false;
        });

        // Auto-resize textarea
        isiInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    </script>

</body>
</html>