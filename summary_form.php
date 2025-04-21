<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Validate employee ID
$employee_id = $_GET['employee_id'] ?? null;
if (!$employee_id) {
    echo "<script>alert('Invalid employee selection!'); window.location.href = 'employee_list.php';</script>";
    exit;
}

// Fetch employee record
$sql = "SELECT last_name, first_name, middle_name, campus FROM employees WHERE id = :employee_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':employee_id' => $employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    echo "<script>alert('Employee record not found!'); window.location.href = 'employee_list.php';</script>";
    exit;
}

// Create the full name in "Last, First Middle" format
$accountable_officer_name = "{$employee['last_name']}, {$employee['first_name']} {$employee['middle_name']}";
$employee_campus = $employee['campus'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("
        INSERT INTO property_summaries 
        (accountable_officer, reference_no, qty, unit, article, description, property_inventory_no, date, unit_cost, total_cost, fund_cluster, remarks) 
        VALUES 
        (:accountable_officer, :reference_no, :qty, :unit, :article, :description, :property_inventory_no, :date, :unit_cost, :total_cost, :fund_cluster, :remarks)
    ");
    $stmt->execute([
        ':accountable_officer' => $employee_id,
        ':reference_no' => $_POST['reference_no'],
        ':qty' => $_POST['qty'],
        ':unit' => $_POST['unit'],
        ':article' => $_POST['article'],
        ':description' => $_POST['description'],
        ':property_inventory_no' => $_POST['property_inventory_no'],
        ':date' => $_POST['date'],
        ':unit_cost' => $_POST['unit_cost'],
        ':total_cost' => $_POST['total_cost'],
        ':fund_cluster' => $_POST['fund_cluster'],
        ':remarks' => $_POST['remarks'],
    ]);
    echo "<script>Swal.fire('Success', 'Data inserted successfully!', 'success');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'includes/header_nav.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Summary Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
        body { background-color: #f8f9fa; }
        .container { max-width: 1100px; }
        .card { border-radius: 12px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        .card-header { background: linear-gradient(135deg, #007bff, #6610f2); color: white; font-size: 1.2rem; padding: 15px 20px; text-align: center; font-weight: 600; }
        .form-control, .form-select { border-radius: 6px; }
        .btn { font-weight: 500; border-radius: 6px; }
        .btn-secondary { background-color: #6c757d; border: none; }
        .btn-secondary:hover { background-color: #5a6268; }
        .table { border-radius: 8px; overflow: hidden; }
        .table th { background-color: #343a40; color: white; text-align: center; }
        .table tbody tr:hover { background-color: #f1f1f1; transition: 0.3s; }
        /* Fix for number inputs */
        .table input[type="number"] {
            width: 100%;  /* Make sure inputs fill the column */
            min-width: 80px; /* Prevent inputs from shrinking too much */
            padding: 6px 10px; /* Better padding for visibility */
            text-align: center; /* Center text for consistency */
            font-size: 16px; /* Increase readability */
            -moz-appearance: textfield; /* Remove number spinner in Firefox */
        }

        /* Remove number input arrows in Chrome, Edge, Safari */
        .table input[type="number"]::-webkit-outer-spin-button,
        .table input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Responsive Table Container */
        .table-responsive {
            overflow-x: auto; /* Enables horizontal scrolling */
            max-width: 100%;
            white-space: nowrap; /* Prevents text wrapping */
        }

        /* Make table rows and columns resizable */
        .resizable-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px; /* Prevents shrinking too much */
        }

        /* Styling Table Header */
        .resizable-table th {
            position: sticky;
            top: 0;
            background: #343a40;
            color: white;
            text-align: center;
            padding: 10px;
            cursor: col-resize; /* Show resize cursor */
        }

        /* Add a small resizer handle */
        .resizer {
            display: inline-block;
            width: 5px;
            height: 100%;
            cursor: col-resize;
            position: absolute;
            right: 0;
            top: 0;
            z-index: 1;
        }

        /* Table Row Styling */
        .resizable-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            word-wrap: break-word;
        }
        @media (max-width: 768px) {
            .table { font-size: 14px; }
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
        #loadingOverlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(255, 255, 255, 0.6);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #007bff;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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
            <i class="bi bi-pencil-square"></i> Property Summary Form
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Entity Name:</label>
                        <input type="text" class="form-control" value="President Ramon Magsaysay State University" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Accountable Officer:</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($accountable_officer_name) ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Office/Department:</label>
                        <input type="text" name="office_department" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Campus:</label>
                        <select name="campus" class="form-select" required>
                            <option value="" disabled>Select Campus</option>
                            <option value="Iba" <?= $employee_campus == 'Iba' ? 'selected' : '' ?>>Iba</option>
                            <option value="Botolan" <?= $employee_campus == 'Botolan' ? 'selected' : '' ?>>Botolan</option>
                            <option value="Cabangan" <?= $employee_campus == 'Cabangan' ? 'selected' : '' ?>>Cabangan</option>
                            <option value="Masinloc" <?= $employee_campus == 'Masinloc' ? 'selected' : '' ?>>Masinloc</option>
                            <option value="San Antonio" <?= $employee_campus == 'San Antonio' ? 'selected' : '' ?>>San Antonio</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
    <table class="table resizable-table">
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
            <tr>
            <td>
            <input list="reference_options" name="reference_no" class="form-control">
            <datalist id="reference_options">
                <option value="PAR; ">
                <option value="ICS; ">
            </datalist>
        </td>
                <td><input type="number" name="qty"></td>
                <td><input type="text" name="unit"></td>
                <td><input type="text" name="article"></td>
                <td><textarea name="description"></textarea></td>
                <td><input type="text" name="property_inventory_no"></td>
                <td><input type="date" name="date"></td>
                <td><input type="number" name="unit_cost" step="0.01"></td>
                <td><input type="number" name="total_cost" step="0.01"></td>
                <td>
                <select name="fund_cluster" class="form-select">
                    <option value="" disabled selected>Select Fund Cluster</option>
                    <option value="01-1-01-101 (RAF-101)">01-1-01-101 (RAF-101)</option>
                    <option value="06-2-07-000 (BRF-161)">06-2-07-000 (BRF-161)</option>
                    <option value="07-2-08-601 (TRF-163)">07-2-08-601 (TRF-163)</option>
                    <option value="05-2-06-441 (IGF-164)">05-2-06-441 (IGF-164)</option>
                </select>
            </td>

                <td><input type="text" name="remarks"></td>
            </tr>
        </tbody>
    </table>
</div>


                <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-save"></i> Save Data</button>
            </form>
        </div>
    </div>
</div>
<!-- 🔄 Loading Overlay -->
<div id="loadingOverlay" style="display: none;">
    <div class="spinner"></div>
</div>
</body>
<?php require_once 'includes/admin_footer.php'; ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const table = document.querySelector(".resizable-table");
    const headers = table.querySelectorAll("th");

    headers.forEach((header) => {
        const resizer = document.createElement("div");
        resizer.classList.add("resizer");
        header.appendChild(resizer);

        let startX, startWidth;

        resizer.addEventListener("mousedown", function (event) {
            startX = event.pageX;
            startWidth = header.offsetWidth;
            document.addEventListener("mousemove", resizeColumn);
            document.addEventListener("mouseup", stopResize);
        });

        function resizeColumn(event) {
            const newWidth = startWidth + (event.pageX - startX);
            header.style.width = `${newWidth}px`;
        }

        function stopResize() {
            document.removeEventListener("mousemove", resizeColumn);
            document.removeEventListener("mouseup", stopResize);
        }
    });
});
// 🔁 Show loading overlay on form submission
document.querySelector("form").addEventListener("submit", function () {
    document.getElementById("loadingOverlay").style.display = "flex";
});
</script>
</html>
