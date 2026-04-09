<?php

if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json'); 

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/UlasanModel.php';

$ulasanModel = new UlasanModel($conn);

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$data = array_merge($_POST, $input);
$action = $data['action'] ?? ($_GET['action'] ?? '');

$targetDir = __DIR__ . '/../assets/img/reviews/';
if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }

function requireAdmin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'Admin') {
        echo json_encode(['status' => 'error', 'message' => 'Akses Ditolak! Anda bukan Admin.']); 
        exit;
    }
}

switch ($action) {

    case 'read_approved':
        echo json_encode($ulasanModel->getApprovedUlasan());
        break;

        case 'create':
        $uploadedFiles = [];
        if (isset($_FILES['fotos'])) {
            $totalFiles = count($_FILES['fotos']['name']);
            $limit = min($totalFiles, 5); 

            for ($i = 0; $i < $limit; $i++) {
                if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileExtension = pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION);
                    $newFileName = 'rev_' . time() . '_' . rand(100,999) . '_' . $i . '.' . $fileExtension; 
                    $targetFilePath = $targetDir . $newFileName;

                    if (move_uploaded_file($_FILES['fotos']['tmp_name'][$i], $targetFilePath)) {
                        $uploadedFiles[] = '../../assets/img/reviews/' . $newFileName;
                    }
                }
            }
        }

        if ($ulasanModel->createUlasan($data, $uploadedFiles)) {
            echo json_encode(['status' => 'success', 'message' => 'Ulasan berhasil dikirim! Menunggu persetujuan admin.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim ulasan.']);
        }
        break;

    case 'read_all':
        requireAdmin(); 

        echo json_encode($ulasanModel->getAllUlasan());
        break;

    case 'approve':
        requireAdmin(); 

        if ($ulasanModel->updateStatus($data['id'], 'Approved')) {
            echo json_encode(['status' => 'success', 'message' => 'Ulasan disetujui & tayang!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status.']);
        }
        break;

    case 'delete':
            requireAdmin(); 

            $oldReview = $ulasanModel->getUlasanById($data['id']);

            if ($oldReview && !empty($oldReview['foto']) && is_array($oldReview['foto'])) {
                foreach ($oldReview['foto'] as $path) {
                    if (strpos($path, 'rev_') !== false) {
                        $oldFileName = basename($path);
                        $oldFilePhysical = $targetDir . $oldFileName;
                        if (file_exists($oldFilePhysical)) { unlink($oldFilePhysical); }
                    }
                }
            }

            if ($ulasanModel->deleteUlasan($data['id'])) {
                echo json_encode(['status' => 'success', 'message' => 'Ulasan berhasil dihapus.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus ulasan.']);
            }
            break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}
?>