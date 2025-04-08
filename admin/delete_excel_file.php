<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if (!isset($data['id'], $data['file_name'])) {
    echo json_encode(["success" => false, "message" => "Missing file ID or file name"]);
    exit;
}

$fileId = $data['id'];
$fileName = $data['file_name'];
$uploadDir = __DIR__ . '/uploads/';
$filePath = $uploadDir . basename($fileName);

// Delete from database
$stmt = $pdo->prepare("DELETE FROM employee_files WHERE id = ?");
$deleted = $stmt->execute([$fileId]);

// Delete file from disk
if ($deleted && file_exists($filePath)) {
    unlink($filePath);
}

echo json_encode([
    "success" => $deleted,
    "message" => $deleted ? "File deleted" : "Database delete failed"
]);
exit;
