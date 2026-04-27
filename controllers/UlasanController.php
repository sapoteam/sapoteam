<?php

if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json'); 

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/UlasanModel.php';
require_once __DIR__ . '/../config/cloudinary_helper.php'; 

$ulasanModel = new UlasanModel($conn);

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$data = array_merge($_POST, $input);
$action = $data['action'] ?? ($_GET['action'] ?? '');

function requireAdmin() {
    $allowed_roles = ['Admin', 'Pegawai']; 
    if (!isset($_SESSION['admin_logged_in']) || !in_array($_SESSION['admin_role'], $allowed_roles)) {
        echo json_encode(['status' => 'error', 'message' => 'Akses Ditolak! Anda tidak memiliki izin.']); 
        exit;
    }
}

switch ($action) {

    case 'read_approved':
        echo json_encode($ulasanModel->getApprovedUlasan());
        break;

    case 'create':
        $uploadedFiles = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp']; 

        if (isset($_FILES['fotos'])) {
            $totalFiles = count($_FILES['fotos']['name']);
            $limit = min($totalFiles, 5); 

            for ($i = 0; $i < $limit; $i++) {
                if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileExtension = strtolower(pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION));

                    if (in_array($fileExtension, $allowedExtensions)) {

                        $cloudUrl = uploadToCloudinary(
                            $_FILES['fotos']['tmp_name'][$i],
                            'oemahkeboen/reviews'
                        );
                        if ($cloudUrl) {
                            $uploadedFiles[] = $cloudUrl;
                        }
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
            foreach ($oldReview['foto'] as $url) {

                $publicId = getPublicIdFromUrl($url);
                if ($publicId) deleteFromCloudinary($publicId);
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