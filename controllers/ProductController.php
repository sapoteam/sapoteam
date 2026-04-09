<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json'); 

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/AuthController.php';

global $conn;
$auth = new AuthController($conn);

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); exit;
}

$productModel = new ProductModel($conn);

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$data = array_merge($_POST, $input);
$action = $data['action'] ?? ($_GET['action'] ?? '');

$targetDir = __DIR__ . '/../assets/img/products/';
if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }

switch ($action) {
    case 'read':
        echo json_encode($productModel->getAllProducts());
        break;

    case 'create':
    case 'update':
        $oldImagePath = '';
        if ($action === 'update') {
            $oldProduct = $productModel->getProductById($data['id']);
            $oldImagePath = $oldProduct ? $oldProduct['image'] : '';
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newFileName = 'prod_' . time() . '.' . $fileExtension; 
            $targetFilePath = $targetDir . $newFileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $data['image'] = '../../assets/img/products/' . $newFileName;

                if (!empty($oldImagePath) && strpos($oldImagePath, 'prod_') !== false) {
                    $oldFileName = basename($oldImagePath); 

                    $oldFilePhysical = $targetDir . $oldFileName;
                    if (file_exists($oldFilePhysical)) {
                        unlink($oldFilePhysical); 
                    }
                }
            }
        }

        if ($action === 'create') {
            $success = $productModel->createProduct($data);
            $msg = 'Produk berhasil ditambahkan!';
        } else {
            $success = $productModel->updateProduct($data);
            $msg = 'Produk berhasil diupdate!';
        }

        if ($success) echo json_encode(['status' => 'success', 'message' => $msg]);
        else echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ke database.']);
        break;

    case 'delete':

        $oldProduct = $productModel->getProductById($data['id']);
        if ($oldProduct && !empty($oldProduct['image']) && strpos($oldProduct['image'], 'prod_') !== false) {
            $oldFileName = basename($oldProduct['image']); 

            $oldFilePhysical = $targetDir . $oldFileName;
            if (file_exists($oldFilePhysical)) {
                unlink($oldFilePhysical); 
            }
        }

        if ($productModel->deleteProduct($data['id'])) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error']);
        break;

    case 'toggle':
        if ($productModel->toggleStatus($data['id'], $data['status'])) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}
?>