<?php
session_start();
require_once 'db.php';  // Include your PDO-based db.php

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
    <?php require_once 'includes/header_nav.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employee Summary</title>
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
            min-height: 100vh; 
        }

        .container {
            max-width: 1500px;
            flex: 1;
            padding-bottom: 80px; 
            margin-left: 17%;
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

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
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

        footer {
            width: 100%;
            text-align: center;
            padding: 10px;
            background: #2C3E50;
            color: #fff;
            font-size: 10px;
            position: relative; 
            margin-top: auto;
        }

        footer img.footer-logo {
            height: 60px;
            width: auto;
        }
/* 🔹 PRINT STYLES */
@media print {
    body {
        background-color: white;
    }

    .no-print {
        display: none !important; /* Hide buttons & unnecessary UI */
    }

    .container {
        max-width: 100%;
        margin: 0;
        padding: 0;
    }

    /* Only print the summary table with grid lines */
    .printable-table {
        width: 100%;
        border: 1px solid black; /* Border around the table */
        border-collapse: collapse; /* Collapse borders between cells */
    }

    /* Add borders and padding to the table headers and data cells */
    .printable-table th, .printable-table td {
        border: 1px solid black; /* Ensure borders are visible */
        padding: 10px;
        text-align: center;
    }

    .printable-table th {
        background-color: #343a40;
        color: white;
        font-weight: bold;
    }

    /* Adjustments for text alignment within table */
    .text-end {
        text-align: right;
    }
    
    /* Hide unnecessary elements */
    .card-header, .action-buttons, footer, .btn {
        display: none !important;
    }

    /* Add optional header for printed table (if required) */
    .print-header {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
    }
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
                <i class="bi bi-file-earmark-text"></i> Employee Summary Records
            </div>
            <div class="card-body">
                <?php if ($summaries): ?>
                    <div class="table-responsive" id="printable-content">
                        <table class="table table-bordered table-striped mt-3 printable-table">
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
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">No summary records found for this employee.</p>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="employee_list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Employee List</a>
                </div>

                <!-- Print Button -->
                <div class="text-center mt-4">
                    <button class="btn btn-primary no-print" onclick="printSummary()">Print Summary</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'includes/admin_footer.php'; ?>
</body>
<script>
function printSummary() {
    const content = document.querySelector("#printable-content").innerHTML; // Get summary content
    const originalContent = document.body.innerHTML; // Store original page content

    // Define the print header (from the image)
    const printHeader = `
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="upload/prmsu_logo.png" style="height: 80px;">
            <h3 style="margin: 5px 0;">Republic of the Philippines</h3>
            <h2 style="margin: 5px 0; font-weight: bold;">PRESIDENT RAMON MAGSAYSAY STATE UNIVERSITY</h2>
            <p style="margin: 5px 0;">Iba, Zambales, Philippines</p>
            <p style="margin: 5px 0;">Tel./Fax No. (047) 811-1683 | rmtupresident@yahoo.com | <a href="https://www.prmsu.edu.ph">www.prmsu.edu.ph</a></p>
            <hr style="border: 2px solid black;">
            <h4 style="margin-top: 10px; text-decoration: underline;">Employee Summary Records</h4>
        </div>
    `;

    // Add grid lines to the table
    const printStyles = `
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                color: black;
            }

            table {
                width: 100%;
                border: 2px solid black;
                border-collapse: collapse;
            }

            th, td {
                border: 2px solid black;
                padding: 8px;
                text-align: center;
            }

            th {
                background-color: #343a40;
                color: white;
                font-weight: bold;
            }

            .text-end {
                text-align: right;
            }

            @media print {
                .no-print { display: none; }
            }
        </style>
    `;

    // Create printable HTML layout
    const printWindow = window.open('', '', 'width=900,height=600');
    printWindow.document.write(`<html><head><title>Print Employee Summary</title>${printStyles}</head><body>`);
    printWindow.document.write(printHeader); // Insert header
    printWindow.document.write(content); // Insert summary content
    printWindow.document.write(`</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print(); // Trigger print dialog
    printWindow.close();
}
</script>
</html>
