<?php
session_start();
require_once 'db.php'; // Database connection

header('Content-Type: application/json'); // Ensure JSON response

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // ✅ Validate and sanitize input fields
        $last_name = trim($_POST['last_name']);
        $first_name = trim($_POST['first_name']);
        $middle_name = trim($_POST['middle_name']);
        $position = trim($_POST['position']);
        $campus = trim($_POST['campus']);
        $created_at = date("Y-m-d H:i:s"); // Auto-generate timestamp

        // ✅ Ensure required fields are not empty
        if (empty($last_name) || empty($first_name) || empty($position) || empty($campus)) {
            echo json_encode(["success" => false, "message" => "All required fields must be filled."]);
            exit;
        }

        // ✅ Insert Employee into Database
        $stmt = $pdo->prepare("INSERT INTO employees (last_name, first_name, middle_name, position, campus, created_at) 
                               VALUES (:last_name, :first_name, :middle_name, :position, :campus, :created_at)");
        $stmt->execute([
            'last_name' => $last_name,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'position' => $position,
            'campus' => $campus,
            'created_at' => $created_at
        ]);

        // ✅ Return JSON response for AJAX
        echo json_encode([
            "success" => true,
            "employee_name" => htmlspecialchars("$last_name, $first_name $middle_name"),
            "position" => htmlspecialchars($position),
            "campus" => htmlspecialchars($campus),
            "created_at" => htmlspecialchars($created_at)
        ]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
