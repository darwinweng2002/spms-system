<?php
session_start();
require_once 'db.php';  // Include your PDO-based db.php

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity_name = $_POST['entity_name'];
    $accountable_officer = $_POST['accountable_officer'];
    $office_department = $_POST['office_department'];
    $campus = $_POST['campus'];

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

    try {
        // PDO query with named placeholders
        $stmt = $pdo->prepare("INSERT INTO property_summaries 
            (entity_name, accountable_officer, office_department, campus, reference_no, qty, unit, article, description, property_inventory_no, date, unit_cost, total_cost, fund_cluster, remarks) 
            VALUES (:entity_name, :accountable_officer, :office_department, :campus, :reference_no, :qty, :unit, :article, :description, :property_inventory_no, :date, :unit_cost, :total_cost, :fund_cluster, :remarks)");

        // Execute the prepared statement with actual values
        $stmt->execute([
            ':entity_name' => $entity_name,
            ':accountable_officer' => $accountable_officer,
            ':office_department' => $office_department,
            ':campus' => $campus,
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
            ':remarks' => $remarks
        ]);

        echo "<script>alert('Data saved successfully!');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Failed to save data: " . $e->getMessage() . "');</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary of Property Accountability Records</title>
    <style>
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; position: relative; }
        th { background-color: #2C3E50; color: #fff; }
        input, textarea { width: 100%; min-height: 30px; box-sizing: border-box; }
        .resizer { position: absolute; top: 0; right: -5px; width: 10px; height: 100%; cursor: col-resize; }
    </style>
</head>
<body>

<h2>Summary of Property Accountabilities</h2>
<form action="" method="POST">
    <label>Entity Name:</label>
    <input type="text" name="entity_name" required>

    <label>Accountable Officer:</label>
    <input type="text" name="accountable_officer" required>

    <label>Office/Department:</label>
    <input type="text" name="office_department" required>

    <label>Campus:</label>
    <input type="text" name="campus" required>

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

    <input type="submit" value="Save Data">
</form>

<script>
    // Column resizing logic for all fields
    document.querySelectorAll('th').forEach(th => {
        const resizer = document.createElement('div');
        resizer.classList.add('resizer');
        th.appendChild(resizer);

        resizer.addEventListener('mousedown', function (e) {
            const startX = e.clientX;
            const startWidth = th.offsetWidth;

            function resizeHandler(e) {
                const newWidth = startWidth + (e.clientX - startX);
                th.style.width = `${newWidth}px`;
                th.querySelectorAll('input, textarea').forEach(input => {
                    input.style.width = `${newWidth}px`;
                });
            }

            function stopResize() {
                window.removeEventListener('mousemove', resizeHandler);
                window.removeEventListener('mouseup', stopResize);
            }

            window.addEventListener('mousemove', resizeHandler);
            window.addEventListener('mouseup', stopResize);
        });
    });

    // Arrow key navigation logic
    document.addEventListener('keydown', function (e) {
        const inputs = Array.from(document.querySelectorAll('input, textarea'));
        const currentIndex = inputs.indexOf(document.activeElement);

        switch (e.key) {
            case 'ArrowRight':
                if (currentIndex !== -1 && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                }
                break;
            case 'ArrowLeft':
                if (currentIndex !== -1 && currentIndex > 0) {
                    inputs[currentIndex - 1].focus();
                }
                break;
            case 'ArrowDown':
                if (currentIndex !== -1 && currentIndex + 11 < inputs.length) {
                    inputs[currentIndex + 11].focus();
                }
                break;
            case 'ArrowUp':
                if (currentIndex !== -1 && currentIndex - 11 >= 0) {
                    inputs[currentIndex - 11].focus();
                }
                break;
        }
    });
</script>

</body>
</html>
