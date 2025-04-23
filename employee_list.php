<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Pagination Setup
$recordsPerPage = 20;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

// Optional Search Query
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchParam = '%' . $searchQuery . '%';

// Count total records
if (!empty($searchQuery)) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE 
        last_name LIKE :search OR 
        first_name LIKE :search OR 
        middle_name LIKE :search OR 
        position LIKE :search OR 
        campus LIKE :search");
    $countStmt->bindValue(':search', $searchParam);
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM employees WHERE 
        last_name LIKE :search OR 
        first_name LIKE :search OR 
        middle_name LIKE :search OR 
        position LIKE :search OR 
        campus LIKE :search
        LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':search', $searchParam);
} else {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM employees");
    $totalRecords = $countStmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM employees LIMIT :limit OFFSET :offset");
}

// Bind limit & offset
$stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total pages
$totalPages = ceil($totalRecords / $recordsPerPage);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
            max-width: 1600px;
            padding-bottom: 80px;
            margin-left: 250px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card-header {
            background: #0080ff;
            font-size: 1.2rem;
            padding: 15px 20px;
            text-align: center;
            font-weight: 600;
            color:  #f8f9fa;
        }

        .table {    
            overflow: hidden;
        }

        .table th {
            background-color: #0080ff;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 12px;
            border: 1px solid #d0d0d0;
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

        .search-container {
            margin-bottom: 15px;
            text-align: right;
        }

        .search-container input {
            width: 100%;
            border-radius: 8px;
            padding: 8px;
            border: 1px solid #ccc;
            transition: 0.3s;
        }

        .search-container input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        @media (max-width: 768px) {
            .table {
                font-size: 14px;
            }

            .btn-sm {
                padding: 4px 8px;
                font-size: 12px;
            }

            .search-container {
                text-align: center;
            }

            .search-container input {
                width: 100%;
            }
        }
        footer {
            width: 100%;
            text-align: center;
            padding: 10px;
            background: #2C3E50;
            color: #fff;
            font-size: 10px;
            position: relative; /* Change from absolute to relative */
            margin-top: auto;
        }

        footer img.footer-logo {
            height: 60px;
            width: auto;
        }
    </style>
</head>
<body>
<?php require_once 'includes/side_nav.php'; ?>
    
    <div class="container mt-5">
        <br>
        <br>  
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people-fill"></i> Employee Records
            </div>
            <div class="card-body">
                <!-- Search Box -->
                <div class="search-container">
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="search" class="form-control" placeholder="Search Employee...">
                </div>
            </div>


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
                        <tbody id="employee-table">
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?= htmlspecialchars($employee['last_name']) ?></td>
                                    <td><?= htmlspecialchars($employee['first_name']) ?></td>
                                    <td><?= htmlspecialchars($employee['middle_name']) ?></td>
                                    <td><?= htmlspecialchars($employee['position']) ?></td>
                                    <td><?= htmlspecialchars($employee['campus']) ?></td>
                                    <td class="text-center action-buttons">
                                    <div class="dropup">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="summary_form.php?employee_id=<?= $employee['id'] ?>">
                                                    <i class="bi bi-pencil-square text-success"></i> Create Summary
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="view_summary.php?employee_id=<?= $employee['id'] ?>">
                                                    <i class="bi bi-eye-fill text-primary"></i> View Summary
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="upload_excel.php?employee_id=<?= $employee['id'] ?>">
                                                    <i class="bi bi-upload text-warning"></i> Upload Excel
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="view_excel_files.php?id=<?= $employee['id'] ?>">
                                                    <i class="bi bi-table text-info"></i> View Excel
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination Controls -->
<nav aria-label="Employee table pagination" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($searchQuery) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
            <li class="page-item <?= $page == $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $page ?>&search=<?= urlencode($searchQuery) ?>"><?= $page ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($searchQuery) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
                </div>  
            </div>
        </div>
    </div>

<script>
document.getElementById("search").addEventListener("input", function() {
    let searchValue = this.value.trim();
    let tableBody = document.getElementById("employee-table");

    fetch(`employee_list.php?search=${searchValue}`)
        .then(response => response.text())
        .then(data => {
            let parser = new DOMParser();
            let newDoc = parser.parseFromString(data, "text/html");
            let newTableBody = newDoc.getElementById("employee-table");

            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
        });
});

</script>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
