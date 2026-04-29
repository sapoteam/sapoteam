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
    <link rel="icon" type="image/x-icon" href="../../assets/img/logo.png">
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
            overflow: hidden;
        }

        .page-content {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .page-content.fade-enter {
            opacity: 0;
            transform: translateY(16px);
        }

        .page-content.fade-exit {
            opacity: 0;
            transform: translateY(-12px);
        }

        /* ── Login card ── */
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

        .brand-header img {
            animation: ok-pulse 2.5s ease-in-out infinite;
        }

        @keyframes ok-pulse {
            0%,100% { transform: scale(1); }
            50%      { transform: scale(1.05); }
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
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
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
            transition: transform 0.3s ease, box-shadow 0.3s ease, color 0.3s ease;
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 100, 59, 0.2);
            color: var(--jambu-yellow);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            text-align: center;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-8px); }
            40%      { transform: translateX(8px); }
            60%      { transform: translateX(-5px); }
            80%      { transform: translateX(5px); }
        }

        .btn-toggle-password {
            background-color: var(--bg-krem);
            border-color: #dce8d2;
            color: var(--primary);
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
        }

        .btn-toggle-password:hover {
            background-color: #eef2e6;
            color: var(--primary);
            border-color: #dce8d2;
        }
    </style>
</head>
<body>

<?php include '../../views/loading_screen.php'; ?>

<div class="page-content" id="pageContent">
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

        <form method="POST" action="" id="loginForm">
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
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukan Password" required>
                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" id="togglePassword">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-login shadow-sm">Masuk ke Dashboard</button>
        </form>

        <div class="text-center mt-4">
            <a href="../user/index.php" class="text-decoration-none small text-muted" id="backLink">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda Pengunjung
            </a>
        </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
    var page = document.getElementById('pageContent');

    page.classList.add('fade-enter');
    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            page.classList.remove('fade-enter');
        });
    });

    var form = document.getElementById('loginForm');
    form.addEventListener('submit', function () {
        page.classList.add('fade-exit');
    });

    var backLink = document.getElementById('backLink');
    backLink.addEventListener('click', function (e) {
        e.preventDefault();
        var href = this.href;
        page.classList.add('fade-exit');
        setTimeout(function () {
            window.location.href = href;
        }, 300);
    });

    var togglePassword = document.getElementById('togglePassword');
    var passwordInput  = document.getElementById('password');
    var toggleIcon     = document.getElementById('toggleIcon');

    togglePassword.addEventListener('click', function () {
        var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        toggleIcon.classList.toggle('bi-eye-slash', type === 'password');
        toggleIcon.classList.toggle('bi-eye',       type === 'text');
    });
})();
</script>

</body>
</html>