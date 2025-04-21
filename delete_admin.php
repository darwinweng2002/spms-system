<?php
require_once 'db.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// Decode JSON data from request
$data = json_decode(file_get_contents("php://input"), true);
$adminId = $data['id'] ?? null;

if (!$adminId) {
    echo json_encode(["status" => "error", "message" => "Admin ID is required."]);
    exit;
}

try {
    // First, fetch the admin's avatar path
    $stmt = $pdo->prepare("SELECT avatar FROM admin_users WHERE id = :id");
    $stmt->execute([':id' => $adminId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo json_encode(["status" => "error", "message" => "Admin not found."]);
        exit;
    }

    // Delete the admin from the database
    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = :id");
    $stmt->execute([':id' => $adminId]);

    // Delete the avatar file if it's not the default avatar
    if ($admin['avatar'] && $admin['avatar'] !== "uploads/default-avatar.png") {
        unlink($admin['avatar']); // Delete the file
    }

    echo json_encode(["status" => "success", "message" => "Admin deleted successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
