<?php
session_start();
require_once 'db.php'; // Ensure database connection is included

// Enable error reporting (For debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response type to JSON
header("Content-Type: application/json");

// Ensure the request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// Ensure database connection is working
if (!$pdo) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

// Get user input from form (Sanitize input)
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate empty fields
if (empty($username) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
    exit;
}

try {
    // Check if username exists
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user does not exist
    if (!$user) {
        error_log("Login Failed - User not found: $username");
        echo json_encode(["status" => "error", "message" => "User not found."]);
        exit;
    }

    // Debugging Logs
    error_log("Entered Username: " . $username);
    error_log("Entered Password: " . $password);
    error_log("Stored Hashed Password: " . $user['password']);

    // Verify the password (Must be hashed in database)
    if (!password_verify($password, $user['password'])) {
        error_log("Password verification failed for username: $username");
        echo json_encode(["status" => "error", "message" => "Incorrect password."]);
        exit;
    }

    // Successful Login - Start Session
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    $_SESSION['admin_role'] = $user['position']; // Ensure correct role assignment

    error_log("Login Successful for: " . $username);
    echo json_encode(["status" => "success", "message" => "Login successful!"]);
    exit;
    
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Database error. Please try again."]);
    exit;
}
?>
