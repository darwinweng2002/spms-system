<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];

    if (!isset($_FILES['excel_file']) || !is_array($_FILES['excel_file']['error']) || count($_FILES['excel_file']['error']) === 0) {
        die("No files selected or invalid upload.");
    }

    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $fileCount = count($_FILES['excel_file']['name']);
    $successfulUploads = 0;

    for ($i = 0; $i < $fileCount; $i++) {
        $file_tmp = $_FILES["excel_file"]["tmp_name"][$i];
        $original_name = basename($_FILES["excel_file"]["name"][$i]);
        $target_path = $upload_dir . time() . "_" . $original_name;

        // Check for file upload errors
        if ($_FILES['excel_file']['error'][$i] !== UPLOAD_ERR_OK) {
            echo "Error uploading file: " . $original_name . "<br>";
            continue;
        }

        // Check file extension
        $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, ['xlsx', 'xls'])) {
            echo "Invalid file type for file: " . $original_name . "<br>";
            continue;
        }

        // Move file to target directory
        if (move_uploaded_file($file_tmp, $target_path)) {
            // Insert file details into database
            $stmt = $pdo->prepare("INSERT INTO employee_files (employee_id, file_name, uploaded_at) VALUES (?, ?, NOW())");
            $stmt->execute([$employee_id, basename($target_path)]);
            $successfulUploads++;
            echo "Successfully uploaded: " . $original_name . "<br>";
        } else {
            echo "Failed to move uploaded file: " . $original_name . "<br>";
        }
    }

    // If at least one file was uploaded successfully, redirect
    if ($successfulUploads > 0) {
        header("Location: view_excel_files.php?id=$employee_id&upload=success");
        exit;
    } else {
        echo "No files were uploaded.";
    }
}
?>

<?php
$employee_id = $_GET["employee_id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/header_nav.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel for Employee</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
         * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
         .card {
            margin-top: 60px;
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
        .custom-loader {
        border: 5px solid #f3f3f3;        /* Light gray background */
        border-top: 5px solid #0056b3;    /* Bootstrap primary blue */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        margin: 20px auto;
        animation: spin 3s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>
</head>
<body>
<?php require_once 'includes/side_nav.php'; ?>
<br>
<br>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">Upload Excel File</div>
        <div class="card-body">
        <form action="upload_excel.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="employee_id" value="<?= htmlspecialchars($employee_id) ?>">

    <div class="mb-3">
        <label class="form-label">Select Excel Files</label>
        <input 
            type="file" 
            name="excel_file[]" 
            class="form-control" 
            accept=".xlsx,.xls" 
            multiple 
            required
            id="excelFileInput"
        >
    </div>

    <!-- 📄 File Name Preview Section -->
    <div id="fileNamePreview" class="mt-3" style="display:none;">
        <h6>Selected Files:</h6>
        <ul class="list-group" id="fileList"></ul>
    </div>
<!-- 🔄 Custom Loader UI -->
<div id="uploadLoader" style="display:none;" class="text-center mt-3">
    <div class="custom-loader"></div>
    <p class="mt-2 text-primary fw-semibold">Uploading files... Please wait.</p>
</div>
    <div class="mt-4">
        <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Upload</button>
        <a href="employee_list.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

        </div>
    </div>
</div>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
<script>
document.getElementById("excelFileInput").addEventListener("change", function(e) {
    const files = e.target.files;
    const fileListContainer = document.getElementById("fileList");
    const previewContainer = document.getElementById("fileNamePreview");

    fileListContainer.innerHTML = "";

    if (files.length === 0) {
        previewContainer.style.display = "none";
        return;
    }

    Array.from(files).forEach(file => {
        const li = document.createElement("li");
        li.className = "list-group-item d-flex align-items-center gap-2";
        
        // Add Excel icon
        const icon = document.createElement("i");
        icon.className = "bi bi-file-earmark-excel-fill text-success fs-5";

        // Add file name
        const span = document.createElement("span");
        span.textContent = file.name;

        li.appendChild(icon);
        li.appendChild(span);
        fileListContainer.appendChild(li);
    });

    previewContainer.style.display = "block";
});
// Handle form submission loading
document.querySelector("form").addEventListener("submit", function (e) {
    const submitBtn = this.querySelector("button[type='submit']");
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-clock-history"></i> Uploading...';
    
    document.getElementById("uploadLoader").style.display = "block";
});

</script>
</html>
