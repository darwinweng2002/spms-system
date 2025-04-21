<?php
session_start();
require_once 'db.php';

// ✅ Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// ✅ Validate employee_id
if (!isset($_GET['employee_id']) || !is_numeric($_GET['employee_id'])) {
    die("❌ Invalid Employee ID");
}

$employee_id = (int) $_GET['employee_id'];
$employee_name = htmlspecialchars($_GET['name']);

// ✅ Fetch the saved spreadsheet data
$stmt = $pdo->prepare("SELECT * FROM property_accountabilities WHERE accountable_officer = :employee_id");
$stmt->execute(["employee_id" => $employee_id]);
$existing_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet_data = [];
if ($existing_data) {
    foreach ($existing_data as $row) {
        $spreadsheet_data[] = [
            $row['reference_no'], $row['quantity'], $row['unit'], $row['article'],
            $row['description'], $row['inventory_no'], $row['date_acquired'],
            $row['unit_cost'], $row['total_cost'], $row['fund_cluster'], $row['remarks']
        ];
    }
}
$spreadsheet_json = json_encode($spreadsheet_data);

// ✅ Update logic
// ✅ Update logic with deletion handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedData = json_decode($_POST['updatedData'], true);

    try {
        $pdo->beginTransaction();

        // Fetch all existing reference numbers for this employee
        $stmt = $pdo->prepare("SELECT reference_no FROM property_accountabilities WHERE accountable_officer = ?");
        $stmt->execute([$employee_id]);
        $existingRefs = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'reference_no');

        // Extract updated reference numbers
        $updatedRefs = array_column($updatedData, 0);

        // Identify deleted entries
        $deletedRefs = array_diff($existingRefs, $updatedRefs);

        // Delete missing entries from the database
        if (!empty($deletedRefs)) {
            $deleteStmt = $pdo->prepare("DELETE FROM property_accountabilities WHERE reference_no = ? AND accountable_officer = ?");
            foreach ($deletedRefs as $deletedRef) {
                $deleteStmt->execute([$deletedRef, $employee_id]);
            }
        }

        // Update existing data or insert new data
        foreach ($updatedData as $row) {
            $stmt = $pdo->prepare("INSERT INTO property_accountabilities (
                reference_no, quantity, unit, article, description, inventory_no, 
                date_acquired, unit_cost, total_cost, fund_cluster, remarks, accountable_officer
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                quantity = VALUES(quantity),
                unit = VALUES(unit),
                article = VALUES(article),
                description = VALUES(description),
                inventory_no = VALUES(inventory_no),
                date_acquired = VALUES(date_acquired),
                unit_cost = VALUES(unit_cost),
                total_cost = VALUES(total_cost),
                fund_cluster = VALUES(fund_cluster),
                remarks = VALUES(remarks)");

            $stmt->execute([
                $row[0], $row[1], $row[2], $row[3], $row[4],
                $row[5], $row[6], $row[7], $row[8], $row[9],
                $row[10], $employee_id
            ]);
        }

        $pdo->commit();
        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Spreadsheet</title>
    <?php require_once 'includes/header_nav.php'; ?>

    <!-- ✅ Handsontable for spreadsheet viewing -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.css">
    <script src="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.js"></script>

    <!-- ✅ SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            max-width: 1200px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 26px;
            margin-bottom: 20px;
            color: #002855;
        }
        
        #spreadsheet {
            width: 100%;
            height: 500px;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s ease-in-out;
        }

        .btn-export { background: #ffc107; color: black; }
        .btn-export:hover { background: #e0a800; }

        .btn-back {
            background: #dc3545;
            color: white;
        }
        .btn-back:hover { background: #b02a37; }
    </style>
</head>
<body>

<?php require_once 'includes/side_nav.php'; ?>

<div class="container">
    <h2>Summary of Property Accountabilities of <?= htmlspecialchars($employee_name) ?></h2>

    <!-- ✅ Search Input Box -->
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search..." 
           style="width: 100%; padding: 10px; border: 2px solid #007bff; border-radius: 5px; margin-bottom: 15px; font-size: 16px;">

    <div id="spreadsheet"></div>

    <div class="button-container">
        <button onclick="saveChanges()" class="btn btn-save">Save Changes</button>
        <button onclick="exportToExcel()" class="btn btn-export">Export as Excel</button>
        <button onclick="window.location.href='employee_list.php'" class="btn btn-back">Back to Employees</button>
    </div>
</div>

<script>
let spreadsheetData = <?= $spreadsheet_json ?: "[]" ?>;
const container = document.getElementById('spreadsheet');
const hot = new Handsontable(container, {
    data: spreadsheetData,
    rowHeaders: true,
    colHeaders: ["Reference No./Date", "Qty", "Unit", "Article", "Description", "Property/Inventory No.", "Date Acquired", "Unit Cost", "Total Cost", "Fund Cluster", "Remarks"],
    manualColumnResize: true,
    manualRowResize: true,
    licenseKey: "non-commercial-and-evaluation",
});

// ✅ Save Changes Function
function saveChanges() {
    const updatedData = hot.getData();

    fetch("view_spreadsheet.php?employee_id=<?= $employee_id ?>&name=<?= urlencode($employee_name) ?>", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `updatedData=${encodeURIComponent(JSON.stringify(updatedData))}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("✅ Data successfully updated.");
        } else {
            alert(`❌ Error: ${data.message}`);
        }
    })
    .catch(error => console.error("❌ AJAX Error:", error));
}
</script>

<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
