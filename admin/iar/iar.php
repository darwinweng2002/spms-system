<?php
session_start();
require_once '../db.php';  // Include PDO database connection

// Fetch all supplier records, sorted alphabetically
$query = "SELECT * FROM iar_suppliers ORDER BY supplier_name ASC";
$stmt = $pdo->query($query);
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IAR Supplier Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> <!-- jsPDF Library -->
    
    <style>
        table { margin-top: 20px; width: 100%; }
        th, td { text-align: center; padding: 10px; }
        .btn-export { margin-top: 10px; float: right; }

        /* For textarea styling and resizing */
        textarea {
            width: 100%;
            min-height: 35px;
            overflow: hidden;
            resize: none;
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 5px;
            text-align: left;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Inspection and Acceptance Report (IAR)</h2>

    <form method="POST">
        <table class="table table-bordered" id="supplierTable">
        <button type="submit" name="save" class="btn btn-primary">Save Data</button>
        <button type="button" class="btn btn-success btn-export" onclick="exportToCSV()">Export to CSV</button>
        
        <button type="button" class="btn btn-danger btn-export" onclick="exportToPDF()">Convert to PDF</button> <!-- PDF Export Button -->
            <thead class="table-dark">
            <tr>
                <th>Supplier Name</th>
                <th>Address</th>
                <th>Contact Information</th>
            </tr>
            <tr>
                <td><textarea name="supplier_name[]" oninput="autoResize(this)" onkeydown="insertLineBreak(event)" required></textarea></td>
                <td><textarea name="address[]" oninput="autoResize(this)" onkeydown="insertLineBreak(event)" required></textarea></td>
                <td><textarea name="contact_info[]" oninput="autoResize(this)" onkeydown="insertLineBreak(event)" required></textarea></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                    <td><?= htmlspecialchars($supplier['address']) ?></td>
                    <td><?= htmlspecialchars($supplier['contact_info']) ?></td>
                </tr>
            <?php endforeach; ?>
            
            </tbody>
        </table>

        
    </form>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $supplier_names = $_POST['supplier_name'];
    $addresses = $_POST['address'];
    $contacts = $_POST['contact_info'];

    $sql = "INSERT INTO iar_suppliers (supplier_name, address, contact_info) VALUES (:supplier_name, :address, :contact_info)";
    $stmt = $pdo->prepare($sql);

    for ($i = 0; $i < count($supplier_names); $i++) {
        $stmt->execute([
            ':supplier_name' => $supplier_names[$i],
            ':address' => $addresses[$i],
            ':contact_info' => $contacts[$i],
        ]);
    }
    echo "<script>alert('Data saved successfully!');</script>";
    header("Location: " . $_SERVER['PHP_SELF']);  // Redirect to prevent resubmission
    exit();
}
?>

<script>
    // Function to export table data to CSV (from previous solution)
    function exportToCSV() {
        let csv = [];
        let rows = document.querySelectorAll("#supplierTable tr");

        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) {
                row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
            }
            csv.push(row.join(","));
        }

        let csvContent = csv.join("\n");
        let blob = new Blob([csvContent], { type: 'text/csv' });
        let url = URL.createObjectURL(blob);

        let a = document.createElement("a");
        a.href = url;
        a.download = "iar_suppliers.csv";
        a.click();
        URL.revokeObjectURL(url);
    }

    // Function to export table data to PDF using jsPDF
    async function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        pdf.text("Inspection and Acceptance Report (IAR)", 10, 10);
        pdf.autoTable({ html: '#supplierTable' });  // Use table directly from HTML

        pdf.save("iar_suppliers.pdf");  // Save the file
    }

    // Automatically resize the textarea based on content height
    function autoResize(textarea) {
        textarea.style.height = 'auto';  // Reset the height
        textarea.style.height = textarea.scrollHeight + 'px';  // Adjust based on scrollHeight
    }

    // Insert line break when Shift + Enter is pressed
    function insertLineBreak(event) {
        if (event.key === "Enter" && event.shiftKey) {
            event.preventDefault();
            let textarea = event.target;
            textarea.value += '\n';
            autoResize(textarea);
        }
    }
    
</script>

<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.25/dist/jspdf.plugin.autotable.min.js"></script> <!-- jsPDF AutoTable Plugin -->
</body>
</html>
