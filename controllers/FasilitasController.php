<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json'); 

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/FasilitasModel.php'; 
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/cloudinary_helper.php'; 

global $conn;
$fasilitasModel = new FasilitasModel($conn); 

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$data = array_merge($_POST, $input);
$action = $data['action'] ?? ($_GET['action'] ?? '');

$protected_actions = ['create', 'update', 'delete'];

if (in_array($action, $protected_actions)) {
    $auth = new AuthController($conn);
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'Admin') {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); 
        exit;
    }
}

switch ($action) {
    case 'read':
        echo json_encode($fasilitasModel->getAllFasilitas());
        break;

    case 'readU':
        echo json_encode($fasilitasModel->getAllFasilitasUser());
        break;

    case 'create':
    case 'update':
        $nama_input = $data['nama'] ?? '';
        $exclude_id = ($action === 'update') ? ($data['id'] ?? null) : null;

        if ($fasilitasModel->isNameExists($nama_input, $exclude_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama area ini sudah digunakan! Silakan gunakan nama lain.']);
            break; 
        }
        
        $oldImageUrl = '';
        if ($action === 'update') {
            $oldFasil = $fasilitasModel->getFasilitasById($data['id']);
            $oldImageUrl = $oldFasil ? $oldFasil['image'] : '';
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $cloudUrl = uploadToCloudinary(
                $_FILES['image']['tmp_name'],
                'oemahkeboen/facilities'
            );

            if ($cloudUrl) {
                $data['image'] = $cloudUrl;

                if (!empty($oldImageUrl)) {
                    $publicId = getPublicIdFromUrl($oldImageUrl);
                    if ($publicId) deleteFromCloudinary($publicId);
                }
            }
        }

        if ($action === 'create') {
            $success = $fasilitasModel->createFasilitas($data);
            $msg = 'Area berhasil ditambahkan!';
        } else {
            $success = $fasilitasModel->updateFasilitas($data);
            $msg = 'Area berhasil diupdate!';
        }

        if ($success) echo json_encode(['status' => 'success', 'message' => $msg]);
        else echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ke database.']);
        break;

    case 'delete':
        $oldFasil = $fasilitasModel->getFasilitasById($data['id']);
        if ($oldFasil && !empty($oldFasil['image'])) {

            $publicId = getPublicIdFromUrl($oldFasil['image']);
            if ($publicId) deleteFromCloudinary($publicId);
        }

        if ($fasilitasModel->deleteFasilitas($data['id'])) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}
?>