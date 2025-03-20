<?php
session_start();
require_once 'db.php';

// ✅ Validate Employee ID
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    die("Invalid employee ID.");
}

$employee_id = $_GET["id"];

// ✅ Fetch Employee Details
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = :id");
$stmt->execute(["id" => $employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    die("Employee not found.");
}

// ✅ Construct Employee Full Name
$employee_full_name = htmlspecialchars($employee['first_name'] . ' ' . $employee['middle_name'] . ' ' . $employee['last_name']);

// ✅ Fetch Uploaded Files
$stmt = $pdo->prepare("SELECT * FROM employee_files WHERE employee_id = :employee_id ORDER BY uploaded_at DESC");
$stmt->execute(["employee_id" => $employee_id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee Files of <?= $employee_full_name ?></title>
    <?php require_once 'includes/header_nav.php'; ?>

    <!-- ✅ Include Spreadsheet Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.js"></script>

    <style>
 * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ✅ Main Container */
        .main-container {
            max-width: 1600px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* ✅ Header */
        .main-container h2 {
            font-size: 28px;
            font-weight: bold;
            color: #002855;
            margin-bottom: 20px;
        }

        /* ✅ Search Bar */
        .search-container {
            margin-bottom: 20px;
        }

        .search-container input {
            width: 50%;
            padding: 10px;
            border: 2px solid #007bff;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }

        /* ✅ File Table */
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
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* ✅ No Files Message */
        .no-files {
            padding: 15px;
            font-weight: bold;
            color: #d9534f;
        }

        /* ✅ Print Button */
        .btn-view {
            background: #4CAF50;
            color: white;
            padding: 10px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            border: none;
        }

        .btn-print:hover {
            background: #388E3C;
        }

        /* ✅ Back Button */
        .btn-back {
            background: #007bff;
            color: white;
            padding: 12px 18px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }

        .btn-back:hover {
            background: #0056b3;
        }

        /* ✅ Modal Viewer */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 90%;
            max-width: 900px;
            height: 80%;
            overflow-y: auto;
            text-align: center;
        }

        .close-btn {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }

        #spreadsheetContainer {
            width: 100%;
            height: 500px;
            overflow: auto;
            border: 1px solid #ccc;
            margin-top: 20px;
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
    <h2>Property Record Files of <?= $employee_full_name ?></h2>

    <table>
        <thead>
            <tr>
                <th>File Name</th>
                <th>Uploaded Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($files)): ?>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td class="file-name"><?= htmlspecialchars($file["file_name"]) ?></td>
                        <td><?= date("F d, Y h:i A", strtotime($file["uploaded_at"])) ?></td>
                        <td>
                            <button class="btn btn-view" onclick="viewExcel('<?= htmlspecialchars($file["file_name"]) ?>')">
                                View
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No files uploaded for this employee.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button onclick="window.location.href='add_employee.php'" class="btn-back">Back to Employees</button>
</div>

<!-- ✅ Modal for Viewing Excel Files -->
<div id="excelModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Excel Spreadsheet Viewer</h2>
        <div id="spreadsheetContainer"></div>
    </div>
</div>

<script>
function viewExcel(fileName) {
    let filePath = `./uploads/${encodeURIComponent(fileName)}`;
    console.log("Attempting to load file from path:", filePath);

    fetch(filePath)
        .then(response => {
            if (!response.ok) {
                throw new Error(`File not found: ${filePath} (Status: ${response.status})`);
            }
            return response.arrayBuffer();
        })
        .then(data => {
            let workbook = XLSX.read(data, { type: "array" });
            let sheetName = workbook.SheetNames[0];
            let sheet = workbook.Sheets[sheetName];

            let jsonData = XLSX.utils.sheet_to_json(sheet, {
                header: 1,
                defval: "",
                raw: true
            });

            let container = document.getElementById('spreadsheetContainer');
            container.innerHTML = "";

            let hot = new Handsontable(container, {
                data: jsonData,
                rowHeaders: true,
                colHeaders: true,
                licenseKey: "non-commercial-and-evaluation"
            });

            document.getElementById("excelModal").style.display = "flex";
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error", `Unable to open the file: ${fileName}`, "error");
        });
}

</script>

<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
