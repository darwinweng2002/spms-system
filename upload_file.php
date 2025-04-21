<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];

    // ✅ Ensure files are uploaded
    if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
        die("No files uploaded.");
    }

    $uploadDirectory = "uploads/";
    $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];

    foreach ($_FILES['files']['name'] as $key => $fileName) {
        $fileTmpName = $_FILES['files']['tmp_name'][$key];
        $fileType = $_FILES['files']['type'][$key];

        // ✅ Validate file type (only Excel files allowed)
        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Invalid file format: $fileName');</script>";
            continue;
        }

        // ✅ Create unique file name to prevent overwrites
        $filePath = $uploadDirectory . time() . "_" . basename($fileName);

        // ✅ Move file to server directory
        if (move_uploaded_file($fileTmpName, $filePath)) {
            // ✅ Insert file record into database
            $stmt = $pdo->prepare("INSERT INTO employee_files (employee_id, file_name, file_path) VALUES (:employee_id, :file_name, :file_path)");
            $stmt->execute([
                'employee_id' => $employee_id,
                'file_name' => $fileName,
                'file_path' => $filePath
            ]);
        }
    }

    echo "<script>
        alert('Files uploaded successfully.');
        window.location.href = 'view_employee.php?id=$employee_id';
    </script>";
}
?>
