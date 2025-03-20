<?php
require __DIR__ . 'vendor/autoload.php';  // ✅ If inside admin/

// Ensure the correct path

use PhpOffice\PhpSpreadsheet\IOFactory;

// ✅ Validate File
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("Invalid file.");
}

$file_name = urldecode($_GET['file']); 
$file_path = __DIR__ . '/uploads/' . $file_name; // Ensure full path

if (!file_exists($file_path)) {
    die("File not found: " . $file_path); // Print full path for debugging
}


// ✅ Load the Excel File
$spreadsheet = IOFactory::load($file_path);
$worksheet = $spreadsheet->getActiveSheet();
$data = $worksheet->toArray();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Print Excel File</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        table { width: 80%; margin: auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid black; text-align: center; }
        th { background: #007bff; color: white; }
        .print-btn {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin: 20px;
        }
        .print-btn:hover { background: #218838; }
    </style>
</head>
<body>

<h2>Print Preview - <?= basename($file_path) ?></h2>

<table>
    <?php foreach ($data as $row): ?>
        <tr>
            <?php foreach ($row as $cell): ?>
                <td><?= htmlspecialchars($cell) ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>

<button class="print-btn" onclick="window.print()">Print</button>

</body>
</html>
