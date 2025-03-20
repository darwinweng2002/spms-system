<?php
session_start();
require_once 'db.php';

header("Content-Type: application/json");

// ✅ Validate Employee ID
if (!isset($_GET['employee_id']) || !is_numeric($_GET['employee_id'])) {
    echo json_encode([]);
    exit;
}

$employee_id = (int) $_GET['employee_id'];

// ✅ Fetch employee spreadsheet data
$stmt = $pdo->prepare("SELECT * FROM property_accountabilities WHERE accountable_officer = :employee_id");
$stmt->execute(["employee_id" => $employee_id]);
$existing_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Convert database rows to JSON
$spreadsheet_data = [];
if ($existing_data) {
    foreach ($existing_data as $row) {
        $spreadsheet_data[] = [
            $row['reference_no'], $row['quantity'], $row['unit'], $row['article'],
            $row['description'], $row['inventory_no'], $row['date_acquired'],
            $row['unit_cost'], $row['total_cost'], $row['fund_cluster'], $row['remarks']
        ];
    }
}
echo json_encode($spreadsheet_data);
?>
