<?php
require_once '../../controllers/AuthController.php';

global $conn; 
$auth = new AuthController($conn);

$error_msg = $auth->login();    
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai - Oemah Keboen</title>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #43643b;      
            --primary-soft: #5b7d52; 
            --bg-krem: #f8faef;      
            --jambu-yellow: #F7DC81; 
            --text-dark: #191d16;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-krem);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 35px rgba(67, 100, 59, 0.1);
            border: 1px solid rgba(195, 200, 188, 0.4);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .brand-header h2 {
            font-family: 'Noto Serif', serif;
            color: var(--primary);
            font-style: italic;
            margin-top: 10px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .input-group-text {
            background-color: var(--bg-krem);
            border-color: #dce8d2;
            color: var(--primary);
        }

        .form-control {
            border-color: #dce8d2;
            padding: 12px;
            border-radius: 12px;
        }

        .form-control:focus {
            border-color: var(--primary-soft);
            box-shadow: 0 0 0 0.25rem rgba(67, 100, 59, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-soft) 100%);
            border: none;
            color: white;
            padding: 14px;
            border-radius: 16px;
            font-weight: 600;
            transition: 0.3s;
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 100, 59, 0.2);
            color: var(--jambu-yellow);
        }

        .error-msg {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card mx-auto">
        <div class="brand-header">
            <img src="../../assets/img/logo.png" alt="Logo" width="60" class="rounded-circle shadow-sm">
            <h2>Oemah Keboen</h2>
            <p class="text-muted small">Sistem Informasi Manajemen Internal</p>
        </div>

        <?php if($error_msg): ?>
            <div class="error-msg">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Username Pegawai</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>  
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukan Password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login shadow-sm">Masuk ke Dashboard</button>
        </form>

        <div class="text-center mt-4">
            <a href="../user/index.php" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda Pengunjung
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>