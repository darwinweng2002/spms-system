<?php
session_start();
require_once 'db.php';  // Include PDO-based db connection

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
    <?php require_once 'includes/header_nav.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
            font-size: 1.2rem;
            padding: 15px 20px;
            text-align: center;
            font-weight: 600;
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #343a40;
            color: white;
            text-align: center;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-sm {
            padding: 5px 12px;
        }

        .action-buttons a {
            margin: 2px;
        }

        @media (max-width: 768px) {
            .table {
                font-size: 14px;
            }

            .btn-sm {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<?php require_once 'includes/side_nav.php'; ?>
    <br>
    <br>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people-fill"></i> Employee Records
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                               
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
                                  
                                    <td><?= $employee['last_name'] ?></td>
                                    <td><?= $employee['first_name'] ?></td>
                                    <td><?= $employee['middle_name'] ?></td>
                                    <td><?= $employee['position'] ?></td>
                                    <td><?= $employee['campus'] ?></td>
                                    <td class="text-center action-buttons">
                                        <a href="http://localhost/spms_system/admin/summary_form.php?employee_id=<?= $employee['id'] ?>" 
                                        class="btn btn-success btn-sm"><i class="bi bi-pencil-square"></i> Create</a>
                                        
                                        <a href="http://localhost/spms_system/admin/view_summary.php?employee_id=<?= $employee['id'] ?>" 
                                        class="btn btn-primary btn-sm"><i class="bi bi-eye-fill"></i> View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>  
            </div>
        </div>
    </div>
</body>
</html>
