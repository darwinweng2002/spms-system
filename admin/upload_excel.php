<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];

    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        die("File upload failed.");
    }

    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $file_tmp = $_FILES["excel_file"]["tmp_name"];
    $original_name = basename($_FILES["excel_file"]["name"]);
    $target_path = $upload_dir . time() . "_" . $original_name;

    $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    if (!in_array($file_ext, ['xlsx', 'xls'])) {
        die("Only Excel files are allowed.");
    }

    if (move_uploaded_file($file_tmp, $target_path)) {
        $stmt = $pdo->prepare("INSERT INTO employee_files (employee_id, file_name, uploaded_at) VALUES (?, ?, NOW())");
        $stmt->execute([$employee_id, basename($target_path)]);
        header("Location: view_excel_files.php?id=$employee_id&upload=success");
        exit;
    } else {
        die("Failed to move uploaded file.");
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
                    <label class="form-label">Select Excel File</label>
                    <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                </div>
                <button type="submit" class="btn btn-success">Upload</button>
                <a href="employee_list.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
