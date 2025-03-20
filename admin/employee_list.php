<?php
session_start();
require_once 'db.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// ✅ Capture Search Input (if provided)
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// ✅ Determine Sort Order (Default: ASC)
$sort_order = isset($_GET['sort']) && $_GET['sort'] === 'desc' ? 'DESC' : 'ASC';
$next_sort_order = $sort_order === 'ASC' ? 'desc' : 'asc';

// ✅ Pagination Settings
$limit = 20; // Show 20 records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ✅ Construct SQL Query for Search, Sort & Pagination
$sql = "SELECT * FROM employees WHERE 
            last_name LIKE :search 
         OR first_name LIKE :search 
         OR middle_name LIKE :search 
         OR position LIKE :search 
         OR campus LIKE :search 
         ORDER BY last_name $sort_order, created_at DESC 
         LIMIT :limit OFFSET :offset";

// ✅ Prepare Statement
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', "%$search_query%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Get Total Employees Count (for Pagination)
$total_employees_stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE 
         last_name LIKE :search 
      OR first_name LIKE :search 
      OR middle_name LIKE :search 
      OR position LIKE :search 
      OR campus LIKE :search");
$total_employees_stmt->bindValue(':search', "%$search_query%", PDO::PARAM_STR);
$total_employees_stmt->execute();
$total_employees = $total_employees_stmt->fetchColumn();
$total_pages = ceil($total_employees / $limit);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
    <?php require_once 'includes/header_nav.php'?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * { 
            font-family: 'Poppins', sans-serif; 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        /* ✅ Main Page Layout */
        .main-container {
            max-width: 1600px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
        }

        /* ✅ Align Search Input and CSV Button */
.search-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px; /* Adds spacing between elements */
    margin-bottom: 20px;
}

/* ✅ Ensure Search Bar Adjusts Width */
.search-container input {
    flex: 1; /* Takes available space */
    max-width: 900px; /* Limits input width */
    padding: 10px;
    border: 2px solid #007bff;
    border-radius: 5px;
    font-size: 16px;
    outline: none;
}

/* ✅ Style the Export Button */
.btn-export {
    background: #28a745; /* Yellow */
    color: black;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 16px;
    border: none;
    cursor: pointer;
    transition: 0.3s ease-in-out;
    white-space: nowrap; /* Prevents text wrapping */
    margin-left: 90px;
}

.btn-export:hover {
    background: #218838;
}


        /* ✅ Employee Table */
        table {
            width: 1200px;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
        }

        th, td {
            padding: 14px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        th a {
            text-decoration: none;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* ✅ Buttons Styling */
        .btn {
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            border: none;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        .btn-create {
            background: #28a745; /* Green */
        }

        .btn-create:hover {
            background: #218838;
        }

        .btn-open {
            background: #007bff; /* Blue */
        }

        .btn-open:hover {
            background: #0056b3;
        }

        /* ✅ Pagination */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a {
            padding: 10px 15px;
            border: 1px solid #007bff;
            color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s ease-in-out;
        }

        .pagination a:hover {
            background: #007bff;
            color: white;
        }

        .pagination .active {
            background: #007bff;
            color: white;
            pointer-events: none;
        }

        /* ✅ Responsive Fix */
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
        footer {
            width: 100%;
            text-align: center;
            padding: 2px;
            background: #2C3E50;
            color: #fff;
            font-size: 10px;
            position: absolute;
            bottom: 0;
        }

        footer img.footer-logo {
            height: 60px;
            width: auto;
        }

    </style>
</head>
<body>
<?php require_once 'includes/side_nav.php'; ?>

<div class="main-container">
    <h2>PRMSU Employee Records</h2>

    <!-- ✅ Search Input -->
    <div class="search-container">
    <input type="text" id="searchInput" placeholder="Search Employee..." onkeyup="filterEmployees()">
    <a href="export_employee.php" class="btn btn-export">Export to Excel</a>
</div>


    <table id="employeeTable">
        <thead>
            <tr>
                <th>No.</th> <!-- ✅ Numbering -->
                <th>
                    <a href="?sort=<?= $next_sort_order ?>">Employee Name 
                        <?= $sort_order === 'ASC' ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up"><path d="m5 12 7-7 7 7"/><path d="M12 19V5"/></svg>' : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>' ?> 
                    </a>
                </th> <!-- ✅ Clickable Column for Sorting -->
                <th>Position</th>
                <th>Campus</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php 
    $counter = $offset + 1; // Start numbering from offset
    foreach ($employees as $employee): 
        $employee_id = (int) $employee['id']; // Ensure it's an integer
        $employee_name = urlencode($employee['first_name'] . ' ' . $employee['middle_name'] . ' ' . $employee['last_name']);
    ?>
        <tr>
            <td><?= $counter++ ?></td> <!-- ✅ Display Row Number -->
            <td class="employee-name">
                <?= htmlspecialchars($employee['last_name'] . ', ' . $employee['first_name'] . ' ' . $employee['middle_name']) ?>
            </td>
            <td class="position"><?= htmlspecialchars($employee['position']) ?></td>
            <td class="campus"><?= htmlspecialchars($employee['campus']) ?></td>
            <td class="created_at"><?= htmlspecialchars($employee['created_at']) ?></td>
            <td>
            <a href="spreadsheet_editor.php?employee_id=<?= urlencode($employee['id']) ?>&name=<?= urlencode($employee['first_name'] . ' ' . $employee['middle_name'] . ' ' . $employee['last_name']) ?>" 
            class="btn btn-create" style="background: #28a745; color: white;">
            Create
            </a>

            <a href="view_spreadsheet.php?employee_id=<?= $employee_id ?>&name=<?= $employee_name ?>" 
            class="btn btn-view" style="background: #007bff; color: white;">View</a>
        </td>

        </tr>
    <?php endforeach; ?>
</tbody>


    </table>

    <!-- ✅ Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&sort=<?= $sort_order ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&sort=<?= $sort_order ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&sort=<?= $sort_order ?>">Next</a>
        <?php endif; ?>
    </div>
</div>
<br>
<br>
<script>
function filterEmployees() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let tableRows = document.querySelectorAll("#employeeTable tbody tr");

    tableRows.forEach(row => {
        let matchFound = false;
        row.querySelectorAll("td").forEach(cell => {
            if (cell.textContent.toLowerCase().includes(input)) {
                matchFound = true;
            }
        });

        // ✅ Show row if a match is found, otherwise hide it
        row.style.display = matchFound ? "" : "none";
    });
}
function performSearch() {
    let input = document.getElementById("searchInput").value;
    window.location.href = "?search=" + encodeURIComponent(input);
}
</script>


<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
