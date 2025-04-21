<?php
session_start();
require_once '../db.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// Validate input
$name = trim($_POST['name']);
$position = trim($_POST['position']);
$campus = trim($_POST['campus']);

if (empty($name) || empty($position) || empty($campus)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Insert new admin
$stmt = $pdo->prepare("INSERT INTO admin_users (name, position, campus) VALUES (:name, :position, :campus)");
$stmt->bindParam(':name', $name);
$stmt->bindParam(':position', $position);
$stmt->bindParam(':campus', $campus);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Admin added successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add admin."]);
}
?>
