<?php
session_start();
require_once '../db.php';  // Include your PDO-based db.php

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

// Get employee_id from the URL
$employee_id = $_GET['employee_id'] ?? null;

if (!$employee_id) {
    echo "<div class='alert alert-danger text-center'>No employee ID specified!</div>";
    exit;
}

// Fetch employee summary records
$stmt = $pdo->prepare("SELECT * FROM property_summaries WHERE accountable_officer = :employee_id");
$stmt->execute([':employee_id' => $employee_id]);
$summaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employee Summary</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }
        h2 {
            color: #2C3E50;
            text-align: center;
            font-weight: bold;
        }
        .table th {
            background-color: #2C3E50;
            color: white;
            text-align: center;
        }
        .btn-secondary {
            background-color: #5A6268;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Employee Summary Records</h2>

        <?php if ($summaries): ?>
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>Reference No./Date</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Article</th>
                        <th>Description</th>
                        <th>Property/Inventory No.</th>
                        <th>Date</th>
                        <th>Unit Cost</th>
                        <th>Total Cost</th>
                        <th>Fund Cluster</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summaries as $summary): ?>
                        <tr>
                            <td><?= htmlspecialchars($summary['reference_no']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($summary['qty']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($summary['unit']) ?></td>
                            <td><?= htmlspecialchars($summary['article']) ?></td>
                            <td><?= htmlspecialchars($summary['description']) ?></td>
                            <td><?= htmlspecialchars($summary['property_inventory_no']) ?></td>
                            <td><?= htmlspecialchars($summary['date']) ?></td>
                            <td class="text-end"><?= number_format($summary['unit_cost'], 2) ?></td>
                            <td class="text-end"><?= number_format($summary['total_cost'], 2) ?></td>
                            <td><?= htmlspecialchars($summary['fund_cluster']) ?></td>
                            <td><?= htmlspecialchars($summary['remarks']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted">No summary records found for this employee.</p>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="employee_list.php" class="btn btn-secondary">Back to Employee List</a>
        </div>
    </div>

</body>
</html>
