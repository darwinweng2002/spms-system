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

$employee_id = (int) $_GET['employee_id']; // Ensure it's an integer
$employee_name = htmlspecialchars($_GET['name']); // Sanitize name

// ✅ Fetch employee's existing data
$stmt = $pdo->prepare("SELECT * FROM property_accountabilities WHERE accountable_officer = :employee_id");
$stmt->execute(["employee_id" => $employee_id]);
$existing_data = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Convert to JSON for JavaScript
$spreadsheet_data = $existing_data ? json_encode([
    [$existing_data['reference_no'], $existing_data['quantity'], $existing_data['unit'], $existing_data['article'], 
    $existing_data['description'], $existing_data['inventory_no'], $existing_data['date_acquired'], 
    $existing_data['unit_cost'], $existing_data['total_cost'], $existing_data['fund_cluster'], $existing_data['remarks']]
]) : "null";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spreadsheet Editor</title>
    <?php require_once 'includes/header_nav.php'; ?>

    <!-- ✅ Handsontable for spreadsheet editing -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.css">
    <script src="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.js"></script>

    <!-- ✅ SheetJS for Excel file handling -->
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
    display: flex;              /* Use flexbox for alignment */
    justify-content: center;    /* Center all buttons horizontally */
    align-items: center;        /* Align buttons vertically */
    flex-wrap: wrap;            /* Prevent wrapping to the next line */
    gap: 15px;                  /* Add space between buttons */
    margin-top: 20px;
}

.btn {
    padding: 12px 20px;         /* Make buttons visually balanced */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: 0.3s ease-in-out;
    display: inline-flex;       /* Ensure inline display */
    align-items: center;
    justify-content: center;
    min-width: 180px;           /* Ensure all buttons have equal width */
}

.btn-save { background: #28a745; color: white; }
.btn-save:hover { background: #218838; }

.btn-load { background: #007bff; color: white; }
.btn-load:hover { background: #0056b3; }

.btn-export { background: #ffc107; color: black; }
.btn-export:hover { background: #e0a800; }

.btn-back {
    background: #dc3545;
    color: white;
}
.btn-back:hover { background: #b02a37; }
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

<div class="container">
    <h2>Spreadsheet Editor - <?= htmlspecialchars($employee_name) ?></h2>

    <div id="spreadsheet"></div>

    <div class="button-container">
    <button class="btn btn-load" onclick="loadSpreadsheet()">Load Existing</button>
    <button class="btn btn-save" onclick="saveSpreadsheet()">Save</button>
    <button class="btn btn-export" onclick="exportToExcel()">Export as Excel</button>
    <button onclick="window.location.href='employee_list.php'" class="btn btn-back">Back to Employees</button>
</div>


<script>
// ✅ Default Spreadsheet Data
let spreadsheetData = [
    ["Reference No./Date", "Qty", "Unit", "Article", "Description", "Property/Inventory No.", "Date Acquired", "Unit Cost", "Total Cost", "Fund Cluster", "Remarks"],
    ["", "", "", "", "", "", "", "0.00", "0.00", "", ""],
    ["", "", "", "", "", "", "", "0.00", "0.00", "", ""], 
    ["", "", "", "", "", "", "", "0.00", "0.00", "", ""]
];

// ✅ Dropdown for "Reference No./Date"
const referenceOptions = ["ICS", "PAR"];

// ✅ Dropdown for "Fund Cluster"
const fundClusterOptions = [
    "01-1-01-101 (RAF-101)", 
    "06-2-07-000 (BRF-161)", 
    "07-2-08-601 (TRF-163)", 
    "05-2-06-441 (IGF-164)"
];

// ✅ Initialize Handsontable with Custom Context Menu
const container = document.getElementById('spreadsheet');
const hot = new Handsontable(container, {
    data: spreadsheetData,
    rowHeaders: true,
    colHeaders: true,
    manualColumnResize: true,
    manualRowResize: true,
    licenseKey: "non-commercial-and-evaluation",
    stretchH: "all",
    minRows: 10,
    minCols: 10,
    contextMenu: {
        items: {
            "row_above": { name: "Insert row above" },
            "row_below": { name: "Insert row below" },
            "remove_row": { 
                name: "Delete selected row(s)", 
                callback: function() {
                    let selectedRows = hot.getSelected(); // Get selected row indexes
                    if (selectedRows) {
                        let rowIndexes = [...new Set(selectedRows.map(selection => selection[0]))]; // Get unique row indexes
                        rowIndexes.sort((a, b) => b - a); // Sort in descending order to prevent shifting issues

                        rowIndexes.forEach(rowIndex => {
                            hot.alter("remove_row", rowIndex); //  Completely remove row
                        });
                    }
                }
            },
            "separator": Handsontable.plugins.ContextMenu.SEPARATOR,
            "copy": { name: "Copy" },
            "cut": { name: "Cut" }
        }
    },
    columns: [
        { type: "dropdown", source: referenceOptions },  // Reference No./Date Dropdown
        { type: "numeric" }, { type: "text" }, { type: "text" },
        { type: "text" }, { type: "text" }, { type: "date", dateFormat: "YYYY-MM-DD" },
        { type: "numeric", numericFormat: { pattern: "0.00" } },
        { type: "numeric", numericFormat: { pattern: "0.00" } },
        { type: "dropdown", source: fundClusterOptions },  // Fund Cluster Dropdown
        { type: "text" }
    ]
});



// ✅ Load Existing Data
function loadSpreadsheet() {
    fetch("load_spreadsheet.php")
        .then(response => response.json())
        .then(data => {
            hot.loadData(data);
        })
        .catch(error => console.error("Error loading spreadsheet:", error));
}

// ✅ Save Data
function saveSpreadsheet() {
    let jsonData = hot.getData();
    let employeeId = new URLSearchParams(window.location.search).get("employee_id");

    if (!employeeId) {
        alert("❌ Employee ID is missing from URL!");
        return;
    }

    fetch("save_spreadsheet.php?employee_id=" + employeeId, {  
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ data: jsonData }) 
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert("❌ Error: " + data.error);
        } else {
            alert("✅ Spreadsheet saved successfully!");
        }
    })
    .catch(error => console.error("❌ Error saving spreadsheet:", error));
}

// ✅ Export to Excel
function exportToExcel() {
    let data = hot.getData();
    let ws = XLSX.utils.aoa_to_sheet(data);
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Property Records");

    XLSX.writeFile(wb, "Property_Records.xlsx");
}
</script>

<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
