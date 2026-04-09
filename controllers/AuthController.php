<?php

session_start();
require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct($conn) {
        $this->userModel = new UserModel($conn);
    }

    public function login() {

        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header("Location: ../../views/admin/dashboard.php");
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            if (empty($username) || empty($password)) {
                $error = 'Username dan Password wajib diisi!';
            } else {

                $user = $this->userModel->getUserByUsername($username);

                if ($user) {

                    if ($password === $user['password'] || password_verify($password, $user['password'])) {

                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['admin_name'] = $user['nama'];
                        $_SESSION['admin_role'] = $user['role'];

                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $error = 'Password yang Anda masukkan salah!';
                    }
                } else {
                    $error = 'Username tidak ditemukan atau akun nonaktif!';
                }
            }
        }

        return $error; 

    }
    public function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header("Location: login.php");
            exit;
        }
    }

        public function requireRole($allowed_role) {

        $this->checkAuth();

        if ($_SESSION['admin_role'] !== $allowed_role) {
            echo "<script>alert('Akses Ditolak! Halaman ini hanya untuk $allowed_role.'); window.location.href='dashboard.php';</script>";
            exit;
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../../views/admin/login.php");
        exit;
    }
}
?>