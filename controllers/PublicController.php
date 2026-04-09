<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/conn.php';

$q = $conn->query("SELECT is_panen FROM status_panen LIMIT 1");
$isPanen = false;

if ($q && $q->num_rows > 0) {
    $isPanen = $q->fetch_assoc()['is_panen'] == 1;
}

echo json_encode(['status' => 'success', 'is_panen' => $isPanen]);
?>