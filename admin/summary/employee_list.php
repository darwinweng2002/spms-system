<?php
session_start();
require_once '../db.php';  // Include PDO-based db connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch all employees
$stmt = $pdo->prepare("SELECT * FROM employees");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Employee Records</h2>
        <table class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Position</th>
                    <th>Campus</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?= $employee['id'] ?></td>
                        <td><?= $employee['last_name'] ?></td>
                        <td><?= $employee['first_name'] ?></td>
                        <td><?= $employee['middle_name'] ?></td>
                        <td><?= $employee['position'] ?></td>
                        <td><?= $employee['campus'] ?></td>
                        <td>
                        <!-- Create button (Fixed URL issue) -->
                        <a href="http://localhost/spms_system/admin/summary/summary_form.php?employee_id=<?= $employee['id'] ?>" 
                        class="btn btn-success btn-sm">Create</a>
                        
                        <!-- View button (kept unchanged) -->
                        <a href="http://localhost/spms_system/admin/summary/view_summary.php?employee_id=<?= $employee['id'] ?>" 
                        class="btn btn-primary btn-sm">View</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
