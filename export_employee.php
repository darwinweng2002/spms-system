<?php
session_start();
require_once 'db.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access!");
}

// ✅ Retrieve employee data
$sql = "SELECT id, last_name, first_name, middle_name, position, campus, created_at FROM employees ORDER BY last_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Check if there are employees
if (!$employees) {
    die("No employee records found.");
}

// ✅ Set headers to download file as Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Employee_List_" . date("Y-m-d") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ Output column headers
echo "Employee ID\tLast Name\tFirst Name\tMiddle Name\tPosition\tCampus\tDate Created\n";

// ✅ Output employee data
foreach ($employees as $employee) {
    echo "{$employee['id']}\t";
    echo "{$employee['last_name']}\t";
    echo "{$employee['first_name']}\t";
    echo "{$employee['middle_name']}\t";
    echo "{$employee['position']}\t";
    echo "{$employee['campus']}\t";
    echo "{$employee['created_at']}\n";
}
?>
