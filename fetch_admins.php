<?php
require_once 'db.php';

header("Content-Type: application/json");

try {
    $stmt = $pdo->prepare("SELECT id, name, username, position, campus, avatar FROM admin_users ORDER BY id ASC");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($admins);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
