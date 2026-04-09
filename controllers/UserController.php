<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/AuthController.php'; 

global $conn;
$auth = new AuthController($conn);

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userModel = new UserModel($conn);

$input = json_decode(file_get_contents('php://input'), true);

$action = isset($_GET['action']) ? $_GET['action'] : (isset($input['action']) ? $input['action'] : '');

switch ($action) {
    case 'read':
        $users = $userModel->getAllUsers();
        echo json_encode($users);
        break;

    case 'create':
        if ($userModel->createUser($input)) {
            echo json_encode(['status' => 'success', 'message' => 'Pegawai ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal atau Username sudah ada']);
        }
        break;

    case 'update':
        if ($userModel->updateUser($input)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
        break;

    case 'delete':
        if ($userModel->deleteUser($input['id'])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
        break;

    case 'toggle':
        if ($userModel->toggleStatus($input['id'], $input['is_active'])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}
?>