<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

require_once __DIR__ . '/../config/conn.php';

if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi berakhir, silakan login ulang.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$old_password = $input['old_password'] ?? '';
$new_password = $input['new_password'] ?? '';

if (empty($old_password) || empty($new_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

if (strlen($new_password) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'Password minimal 6 karakter.']);
    exit;
}

global $conn;
$user_id = $_SESSION['admin_id'];

$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User tidak ditemukan!']);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($old_password, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Password lama salah!']);
    exit;
}

$hashed_new = password_hash($new_password, PASSWORD_DEFAULT);

$update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$update->bind_param("si", $hashed_new, $user_id);

if ($update->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password berhasil diperbarui!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate database.']);
}
?>