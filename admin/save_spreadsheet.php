<?php
session_start();
require_once 'db.php';

header("Content-Type: application/json");

// ✅ Ensure user is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

// ✅ Debugging: Log received data
file_put_contents("debug_log.txt", "Received Employee ID: " . ($_GET['employee_id'] ?? "Not Set") . PHP_EOL, FILE_APPEND);
file_put_contents("debug_log.txt", "Raw Data: " . file_get_contents("php://input") . PHP_EOL, FILE_APPEND);

// ✅ Validate Employee ID
if (!isset($_GET['employee_id']) || !is_numeric($_GET['employee_id'])) {
    echo json_encode(["error" => "Invalid Employee ID"]);
    exit;
}

$employee_id = (int) $_GET['employee_id']; // Ensure integer
$data = json_decode(file_get_contents("php://input"), true);

// ✅ Validate received data
if (!$data || !isset($data["data"]) || empty($data["data"])) {
    echo json_encode(["error" => "Invalid or empty data received"]);
    exit;
}

// ✅ Check if Employee ID exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE id = :employee_id");
$stmt->execute(["employee_id" => $employee_id]);
$employeeExists = $stmt->fetchColumn();

if (!$employeeExists) {
    echo json_encode(["error" => "Employee ID does not exist in the database."]);
    exit;
}

// ✅ Loop through and insert each row into database
try {
    $pdo->beginTransaction();

    foreach ($data["data"] as $row) {
        if (count($row) < 11) continue; // Ensure correct data format

        $stmt = $pdo->prepare("
            INSERT INTO property_accountabilities 
            (accountable_officer, reference_no, quantity, unit, article, description, inventory_no, date_acquired, unit_cost, total_cost, fund_cluster, remarks)
            VALUES 
            (:employee_id, :reference_no, :quantity, :unit, :article, :description, :inventory_no, :date_acquired, :unit_cost, :total_cost, :fund_cluster, :remarks)
        ");

        $stmt->execute([
            "employee_id" => $employee_id,
            "reference_no" => $row[0],
            "quantity" => $row[1],
            "unit" => $row[2],
            "article" => $row[3],
            "description" => $row[4],
            "inventory_no" => $row[5],
            "date_acquired" => $row[6],
            "unit_cost" => $row[7],
            "total_cost" => $row[8],
            "fund_cluster" => $row[9],
            "remarks" => $row[10]
        ]);
    }

    $pdo->commit();
    echo json_encode(["message" => "Spreadsheet saved successfully!"]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
