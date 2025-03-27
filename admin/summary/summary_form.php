<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../db.php';  // Include your PDO-based db.php

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['employee_id'])) {
    echo "<script>alert('Invalid employee selection!'); window.location.href = 'employee_list.php';</script>";
    exit;
}
$employee_id = $_GET['employee_id'];

// Fetch employee record
$sql = "SELECT last_name, first_name, middle_name, campus FROM employees WHERE id = :employee_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':employee_id' => $employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    echo "<script>alert('Employee record not found!'); window.location.href = 'employee_list.php';</script>";
    exit;
}

// Create the full name in "Last, First Middle" format for Accountable Officer
$accountable_officer_name = $employee['last_name'] . ', ' . $employee['first_name'] . ' ' . $employee['middle_name'];
$employee_campus = $employee['campus'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference_no = $_POST['reference_no'];
    $qty = $_POST['qty'];
    $unit = $_POST['unit'];
    $article = $_POST['article'];
    $description = $_POST['description'];
    $property_inventory_no = $_POST['property_inventory_no'];
    $date = $_POST['date'];
    $unit_cost = $_POST['unit_cost'];
    $total_cost = $_POST['total_cost'];
    $fund_cluster = $_POST['fund_cluster'];
    $remarks = $_POST['remarks'];

    // Insert query
    $sql = "INSERT INTO property_summaries (accountable_officer, reference_no, qty, unit, article, description, property_inventory_no, date, unit_cost, total_cost, fund_cluster, remarks)
            VALUES (:accountable_officer, :reference_no, :qty, :unit, :article, :description, :property_inventory_no, :date, :unit_cost, :total_cost, :fund_cluster, :remarks)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':accountable_officer' => $employee_id,  // Saving employee_id properly
        ':reference_no' => $reference_no,
        ':qty' => $qty,
        ':unit' => $unit,
        ':article' => $article,
        ':description' => $description,
        ':property_inventory_no' => $property_inventory_no,
        ':date' => $date,
        ':unit_cost' => $unit_cost,
        ':total_cost' => $total_cost,
        ':fund_cluster' => $fund_cluster,
        ':remarks' => $remarks,
    ]);

    echo "Data inserted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary of Property Accountability Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        .main-container { max-width: 1200px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; outline: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        th { background: #007bff; color: white; font-size: 16px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn-submit { width: 100%; padding: 10px; background: #007bff; color: white; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body>

<div class="main-container">
    <h2>Summary of Property Accountabilities (Employee ID: <?= htmlspecialchars($employee_id) ?>)</h2>

    <form action="" method="POST">
        <label>Entity Name:</label>
        <input type="text" name="entity_name" value="President Ramon Magsaysay State University" readonly>

        <label>Accountable Officer:</label>
        <input type="text" name="accountable_officer" value="<?= htmlspecialchars($accountable_officer_name) ?>" readonly>

        <label>Office/Department:</label>
        <input type="text" name="office_department" required>

        <label>Campus:</label>
        <select name="campus" required class="custom-select">
            <option value="" disabled>Select Campus</option>
            <option value="Iba" <?= $employee_campus == 'Iba' ? 'selected' : '' ?>>Iba</option>
            <option value="Botolan" <?= $employee_campus == 'Botolan' ? 'selected' : '' ?>>Botolan</option>
            <option value="Cabangan" <?= $employee_campus == 'Cabangan' ? 'selected' : '' ?>>Cabangan</option>
            <option value="Masinloc" <?= $employee_campus == 'Masinloc' ? 'selected' : '' ?>>Masinloc</option>
            <option value="San Antonio" <?= $employee_campus == 'San Antonio' ? 'selected' : '' ?>>San Antonio</option>
        </select>

        <!-- Table for Property Records -->
        <table>
            <thead>
                <tr>
                    <th>Reference No./Date</th><th>Qty</th><th>Unit</th><th>Article</th><th>Description</th>
                    <th>Property/Inventory No.</th><th>Date</th><th>Unit Cost</th><th>Total Cost</th>
                    <th>Fund Cluster</th><th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="reference_no"></td>
                    <td><input type="number" name="qty"></td>
                    <td><input type="text" name="unit"></td>
                    <td><input type="text" name="article"></td>
                    <td><textarea name="description"></textarea></td>
                    <td><input type="text" name="property_inventory_no"></td>
                    <td><input type="date" name="date"></td>
                    <td><input type="number" name="unit_cost" step="0.01"></td>
                    <td><input type="number" name="total_cost" step="0.01"></td>
                    <td><input type="text" name="fund_cluster"></td>
                    <td><input type="text" name="remarks"></td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn-submit">Save Data</button>
    </form>
</div>

</body>
</html>

