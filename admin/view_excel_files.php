<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$employee_id = $_GET["id"];
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM employee_files WHERE employee_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$employee_id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'includes/header_nav.php'; ?>
    <title>View Excel Files</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        /* Header and Navbar */
        nav.navbar {
            background-color: #2C3E50;
        }

        nav.navbar a {
            color: white;
            font-weight: bold;
        }

        /* Modal */
        #excelModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }

        #excelModalContent {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 900px;
            height: 75%;
            overflow-y: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Table Styling */
        .file-table th, .file-table td {
            padding: 12px;
            text-align: center;
        }

        .file-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Buttons */
        .btn-custom {
            min-width: 100px;
        }

        .btn-view {
            background-color: #3498db;
            color: white;
        }

        .btn-view:hover {
            background-color: #2980b9;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        /* Page Layout */
        .container {
            margin-top: 50px;
        }

        h3 {
            color: #2C3E50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .no-files-msg {
            font-size: 16px;
            color: #7f8c8d;
        }

        #spreadsheetContainer {
    overflow-x: scroll;  /* Always show horizontal scrollbar */
    overflow-y: auto;    /* Scroll vertically only when needed */
}



        #excelModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }

        #excelModalContent {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 1000px;
            height: 85%;
            overflow-y: auto;
        }

        #spreadsheetContainer table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        #spreadsheetContainer th, #spreadsheetContainer td {
            border: 1px solid #ccc;
            padding: 6px;
        }
    </style>
</head>
<body>
<?php require_once 'includes/side_nav.php'; ?>
<br>
<br>

<div class="container mt-5">
    <h3>Excel Files of <?= htmlspecialchars($employee["first_name"] . " " . $employee["last_name"]) ?></h3>

    <!-- Search bar -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search files..." onkeyup="searchFiles()">
    
    <?php if (!empty($files)): ?>
        <table class="table table-striped file-table">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Uploaded At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="fileTableBody">
                <?php foreach ($files as $file): ?>
                    <tr class="file-row">
                        <td><?= htmlspecialchars($file['file_name']) ?></td>
                        <td><?= date('F j, Y, g:i a', strtotime($file['uploaded_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-view btn-custom" onclick="viewExcel('<?= $file['file_name'] ?>')">View</button>
                            <button class="btn btn-sm btn-delete btn-custom" onclick="deleteExcel('<?= $file['id'] ?>', '<?= $file['file_name'] ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-files-msg">No Excel files uploaded.</p>
    <?php endif; ?>
</div>


<!-- Modal -->
<div class="modal" id="excelModal">
    <div id="excelModalContent">
        <button onclick="closeExcelModal()" class="btn btn-danger float-end mb-3">Close</button>
        <h4>Excel Preview</h4>
        <div id="spreadsheetContainer"></div>
    </div>
</div>

<script>
function viewExcel(fileName) {
    const filePath = `./uploads/${encodeURIComponent(fileName)}`;

    fetch(filePath)
        .then(res => {
            if (!res.ok) throw new Error("File not found.");
            return res.arrayBuffer();
        })
        .then(data => {
            const workbook = XLSX.read(data, { type: "array", cellStyles: true, cellDates: true });
            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];

            const html = XLSX.utils.sheet_to_html(sheet, {
                header: "", footer: "", editable: false
            });

            const container = document.getElementById("spreadsheetContainer");
            container.innerHTML = html;

            document.getElementById("excelModal").style.display = "flex";
        })
        .catch(err => {
            console.error(err);
            alert("Error: " + err.message);
        });
}

function closeExcelModal() {
    document.getElementById("excelModal").style.display = "none";
}

// Delete Function
function deleteExcel(fileId, fileName) {
    if (!confirm(`Delete file "${fileName}"? This cannot be undone.`)) return;

    fetch('delete_excel_file.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: fileId, file_name: fileName })
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            alert("File deleted successfully.");
            location.reload();
        } else {
            alert("Failed to delete file: " + result.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert("Error: " + err.message);
    });
}
function searchFiles() {
    const input = document.getElementById('searchInput').value.toLowerCase(); // Get the search term
    const rows = document.querySelectorAll('.file-row'); // Get all file rows

    rows.forEach(row => {
        const fileName = row.querySelector('td:first-child').textContent.toLowerCase(); // Get the file name of each row
        const uploadedAt = row.querySelector('td:nth-child(2)').textContent.toLowerCase(); // Get the uploaded date
        
        // Check if the search term matches file name or uploaded date
        if (fileName.includes(input) || uploadedAt.includes(input)) {
            row.style.display = ''; // Show row if it matches
        } else {
            row.style.display = 'none'; // Hide row if it doesn't match
        }
    });
}

</script>
</body>
</html>
