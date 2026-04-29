<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/cloudinary_helper.php'; 

global $conn;

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($action === 'read' || $action === 'get') {
    $productModel = new ProductModel($conn);
    if ($action === 'read') {
        echo json_encode($productModel->getAllProducts());
    } else {
        $id = intval($_GET['id'] ?? 0);
        $product = $productModel->getProductById($id);
        if ($product) {
            echo json_encode(['status' => 'success', 'data' => $product]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan']);
        }
    }
    exit;
}

$auth = new AuthController($conn);
$allowed = ['Admin', 'Pegawai'];
if (!isset($_SESSION['admin_logged_in']) || !in_array($_SESSION['admin_role'], $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); 
    exit;
}

$productModel = new ProductModel($conn);
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$data = array_merge($_POST, $input);
$action = $data['action'] ?? ($_GET['action'] ?? '');

switch ($action) {
    case 'create':
    case 'update':
        $nama_input = $data['nama'] ?? '';
        $exclude_id = ($action === 'update') ? ($data['id'] ?? null) : null;

        if ($productModel->isNameExists($nama_input, $exclude_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama produk ini sudah ada! Silakan gunakan nama lain.']);
            break;
        }
        $oldImageUrl = '';
        if ($action === 'update') {
            $oldProduct = $productModel->getProductById($data['id']);
            $oldImageUrl = $oldProduct ? $oldProduct['image'] : '';
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $cloudUrl = uploadToCloudinary(
                $_FILES['image']['tmp_name'],
                'oemahkeboen/products'
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
            $success = $productModel->createProduct($data);
            $msg = 'Produk berhasil ditambahkan!';
        } else {
            $success = $productModel->updateProduct($data);
            $msg = 'Produk berhasil diupdate!';
        }

        echo json_encode($success
            ? ['status' => 'success', 'message' => $msg]
            : ['status' => 'error', 'message' => 'Gagal menyimpan ke database.']
        );
        break;

    case 'delete':
        $oldProduct = $productModel->getProductById($data['id']);
        if ($oldProduct && !empty($oldProduct['image'])) {

            $publicId = getPublicIdFromUrl($oldProduct['image']);
            if ($publicId) deleteFromCloudinary($publicId);
        }

        echo json_encode($productModel->deleteProduct($data['id'])
            ? ['status' => 'success']
            : ['status' => 'error']
        );
        break;

    case 'toggle':
        echo json_encode($productModel->toggleStatus($data['id'], $data['status'])
            ? ['status' => 'success']
            : ['status' => 'error']
        );
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}
?>