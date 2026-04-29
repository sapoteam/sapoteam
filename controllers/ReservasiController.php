<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json'); 

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/ReservasiModel.php';
require_once __DIR__ . '/AuthController.php';

global $conn;
$reservasiModel = new ReservasiModel($conn);

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $data['action'] ?? ($_GET['action'] ?? '');

// Semua action kecuali readU dan create wajib login
$protected_actions = ['read', 'update', 'update_status', 'delete'];

if (in_array($action, $protected_actions)) {
    $allowed_roles = ['Admin', 'Pegawai'];
    if (!isset($_SESSION['admin_logged_in']) || !in_array($_SESSION['admin_role'], $allowed_roles)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
}
if ($action === 'delete' && $_SESSION['admin_role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak! Hanya Admin yang boleh menghapus data reservasi.']); 
    exit;
}

switch ($action) {

    // ── ADMIN ONLY: semua data lengkap ──
    case 'read':
        echo json_encode($reservasiModel->getAllReservasi());
        break;

    // ── PUBLIC: hanya tanggal + fasilitas_id + status untuk kalender ──
    case 'readU':
        echo json_encode($reservasiModel->getBookedDates());
        break;

    // ── PUBLIC: buat reservasi baru ──
    case 'create':
        if (empty($data['tanggal']) || empty($data['fasilitas_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
            break;
        }
        if ($reservasiModel->checkDoubleBooking($data['tanggal'], $data['fasilitas_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Area sudah dibooking pada tanggal tersebut!']);
            break;
        }
        if ($reservasiModel->createReservasi($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Reservasi berhasil ditambahkan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ke database.']);
        }
        break;

    // ── ADMIN ONLY ──
    case 'update':
        if ($reservasiModel->checkDoubleBooking($data['tanggal'], $data['fasilitas_id'], $data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Area sudah dibooking pada tanggal tersebut!']);
            break;
        }
        if ($reservasiModel->updateReservasi($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data reservasi berhasil diupdate!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate database.']);
        }
        break;

    case 'update_status':
        if ($reservasiModel->updateStatus($data['id'], $data['status'])) {
            echo json_encode(['status' => 'success', 'message' => 'Status berhasil diubah!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status.']);
        }
        break;

    case 'delete':
        if ($reservasiModel->deleteReservasi($data['id'])) {
            echo json_encode(['status' => 'success', 'message' => 'Reservasi dihapus permanen.']);
        } else {
            echo json_encode(['status' => 'error']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}
?>