<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json'); 

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/FasilitasModel.php'; 
require_once __DIR__ . '/AuthController.php';

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

$targetDir = __DIR__ . '/../assets/img/facilities/';
if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }

switch ($action) {
    case 'read':
        echo json_encode($fasilitasModel->getAllFasilitas());
        break;

    case 'readU':
        echo json_encode($fasilitasModel->getAllFasilitasUser());
        break;

    case 'create':
    case 'update':
        $oldImagePath = '';
        if ($action === 'update') {
            $oldFasil = $fasilitasModel->getFasilitasById($data['id']);
            $oldImagePath = $oldFasil ? $oldFasil['image'] : '';
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newFileName = 'fasil_' . time() . '.' . $fileExtension; 
            $targetFilePath = $targetDir . $newFileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $data['image'] = '../../assets/img/facilities/' . $newFileName;

                if (!empty($oldImagePath) && strpos($oldImagePath, 'fasil_') !== false) {
                    $oldFileName = basename($oldImagePath); 
                    $oldFilePhysical = $targetDir . $oldFileName;
                    if (file_exists($oldFilePhysical)) { unlink($oldFilePhysical); }
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
        if ($oldFasil && !empty($oldFasil['image']) && strpos($oldFasil['image'], 'fasil_') !== false) {
            $oldFileName = basename($oldFasil['image']);
            $oldFilePhysical = $targetDir . $oldFileName;
            if (file_exists($oldFilePhysical)) { unlink($oldFilePhysical); }
        }

        if ($fasilitasModel->deleteFasilitas($data['id'])) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}
?>