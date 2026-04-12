<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json'); 

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/DashboardModel.php';

$allowed_roles = ['Admin', 'Pegawai'];
if (!isset($_SESSION['admin_logged_in']) || !in_array($_SESSION['admin_role'], $allowed_roles)) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); 
    exit;
}

$dashboardModel = new DashboardModel($conn);

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$data = array_merge($_POST, $input);
$action = $data['action'] ?? ($_GET['action'] ?? '');

switch ($action) {
    case 'get_data':
        echo json_encode(['status' => 'success', 'data' => $dashboardModel->getDashboardData()]);
        break;

    case 'toggle_panen':

        if ($_SESSION['admin_role'] !== 'Admin') {
            echo json_encode(['status' => 'error', 'message' => 'Akses Ditolak! Hanya Admin yang bisa mengubah status panen.']);
            break;
        }

        $isActive = isset($data['statusPanen']) && $data['statusPanen'] == true;
        if ($dashboardModel->togglePanen($isActive)) {
            echo json_encode(['status' => 'success', 'message' => 'Status Panen berhasil diperbarui!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate status panen.']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}
?>