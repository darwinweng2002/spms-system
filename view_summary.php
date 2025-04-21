<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$employee_id = $_GET['employee_id'] ?? null;

if (!$employee_id) {
    echo "<div class='alert alert-danger text-center'>No employee ID specified!</div>";
    exit;
}

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
/* 🔒 Scrollable table with sticky header */
.table-responsive {
    max-height: 500px; /* Adjust as needed */
    overflow-y: auto;
}

/* Sticky thead */
.table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #0080ff; /* Match your header theme */
    color: white;
    text-align: center;
    font-weight: bold;
}

/* Optional: scroll bar styling (modern browsers) */
.table-responsive::-webkit-scrollbar {
    width: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-responsive::-webkit-scrollbar-thumb {
    background-color: #6c757d;
    border-radius: 4px;
}

    </style>
</head>
<body>
<?php require_once 'includes/side_nav.php'; ?>
<div class="container mt-5">
    <br>
    <br>

    <div class="card">
        <div class="card-header bg-primary text-white text-center fw-bold">
            <i class="bi bi-file-earmark-text"></i> Employee Summary Records
        </div>
        <div class="card-body">
            <?php if ($summaries): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>Ref No.</th><th>Qty</th><th>Unit</th><th>Article</th><th>Description</th>
                                <th>Inventory No.</th><th>Date</th><th>Unit Cost</th><th>Total Cost</th>
                                <th>Fund Cluster</th><th>Remarks</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($summaries as $summary): ?>
                            <tr>
                                <td><?= htmlspecialchars($summary['reference_no']) ?></td>
                                <td><?= htmlspecialchars($summary['qty']) ?></td>
                                <td><?= htmlspecialchars($summary['unit']) ?></td>
                                <td><?= htmlspecialchars($summary['article']) ?></td>
                                <td><?= htmlspecialchars($summary['description']) ?></td>
                                <td><?= htmlspecialchars($summary['property_inventory_no']) ?></td>
                                <td><?= htmlspecialchars($summary['date']) ?></td>
                                <td><?= number_format($summary['unit_cost'], 2) ?></td>
                                <td><?= number_format($summary['total_cost'], 2) ?></td>
                                <td><?= htmlspecialchars($summary['fund_cluster']) ?></td>
                                <td><?= htmlspecialchars($summary['remarks']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick='openEditModal(<?= json_encode($summary) ?>)'>
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">No summary records found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ✏️ Edit Modal -->
<div class="modal fade" id="editSummaryModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="editSummaryForm">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Edit Summary</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3 px-4">
          <input type="hidden" name="id" id="edit_id">
          <input type="hidden" name="employee_id" value="<?= htmlspecialchars($employee_id) ?>">

          <div class="col-md-4">
            <label>Reference No.</label>
            <input type="text" class="form-control" name="reference_no" id="edit_reference_no">
          </div>
          <div class="col-md-2">
            <label>Qty</label>
            <input type="number" class="form-control" name="qty" id="edit_qty">
          </div>
          <div class="col-md-2">
            <label>Unit</label>
            <input type="text" class="form-control" name="unit" id="edit_unit">
          </div>
          <div class="col-md-4">
            <label>Article</label>
            <input type="text" class="form-control" name="article" id="edit_article">
          </div>
          <div class="col-md-6">
            <label>Description</label>
            <input type="text" class="form-control" name="description" id="edit_description">
          </div>
          <div class="col-md-6">
            <label>Inventory No.</label>
            <input type="text" class="form-control" name="property_inventory_no" id="edit_property_inventory_no">
          </div>
          <div class="col-md-3">
            <label>Date</label>
            <input type="date" class="form-control" name="date" id="edit_date">
          </div>
          <div class="col-md-3">
            <label>Unit Cost</label>
            <input type="number" step="0.01" class="form-control" name="unit_cost" id="edit_unit_cost">
          </div>
          <div class="col-md-3">
            <label>Total Cost</label>
            <input type="number" step="0.01" class="form-control" name="total_cost" id="edit_total_cost">
          </div>
          <div class="col-md-3">
            <label>Fund Cluster</label>
            <input type="text" class="form-control" name="fund_cluster" id="edit_fund_cluster">
          </div>
          <div class="col-md-12">
            <label>Remarks</label>
            <textarea class="form-control" name="remarks" id="edit_remarks"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Open modal and fill fields
function openEditModal(data) {
    for (const key in data) {
        const field = document.getElementById("edit_" + key);
        if (field) field.value = data[key];
    }
    const modal = new bootstrap.Modal(document.getElementById('editSummaryModal'));
    modal.show();
}

// Handle form submission
document.getElementById("editSummaryForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("update_summary.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire("Updated!", "Record successfully updated.", "success")
                .then(() => window.location.reload());
        } else {
            Swal.fire("Error!", data.message || "Update failed.", "error");
        }
    })
    .catch(() => {
        Swal.fire("Error!", "An unexpected error occurred.", "error");
    });
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
