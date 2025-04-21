<?php
require_once 'db.php';

header("Content-Type: application/json");

// Ensure request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// Sanitize user inputs
$name = trim($_POST['name']);
$username = trim($_POST['username']);
$position = trim($_POST['position']);
$campus = trim($_POST['campus']);
$password = trim($_POST['password']);

// Ensure all fields are filled
if (empty($name) || empty($username) || empty($position) || empty($campus) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
    exit;
}

// Hash the password before storing it
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Handle Image Upload
$avatarPath = "upload/default-avatar.jpg"; // Default avatar path
if (!empty($_FILES["avatar"]["name"])) {
    $targetDir = "upload/"; // Ensure this folder exists
    $fileName = time() . "_" . basename($_FILES["avatar"]["name"]); // Unique filename
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
             
    // Allow only specific image formats
    $allowedTypes = ["jpg", "jpeg", "png"];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFilePath)) {
            $avatarPath = $targetFilePath; // Save uploaded file path
        } else {
            echo json_encode(["status" => "error", "message" => "Error uploading image."]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid image format. Allowed: JPG, JPEG, PNG"]);
        exit;
    }
}

// Insert into database
try {
    $stmt = $pdo->prepare("INSERT INTO admin_users (name, username, position, campus, password, avatar) VALUES (:name, :username, :position, :campus, :password, :avatar)");
    $stmt->execute([
        ':name' => $name,
        ':username' => $username,
        ':position' => $position,
        ':campus' => $campus,
        ':password' => $hashedPassword,
        ':avatar' => $avatarPath
    ]);

    echo json_encode(["status" => "success", "message" => "Admin added successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
