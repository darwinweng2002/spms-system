<?php
require_once 'db.php';
header("Content-Type: application/json");

$stmt = $pdo->query("SELECT * FROM request_letters ORDER BY created_at DESC");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($data)) {
    echo json_encode([]);
} else {
    echo json_encode($data);
}
?>
