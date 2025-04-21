<?php
session_start();
require_once 'db.php';  // Include your PDO-based db.php

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity_name = $_POST['entity_name'];
    $accountable_officer = $_POST['accountable_officer'];
    $office_department = $_POST['office_department'];
    $campus = $_POST['campus'];

    $reference_no = $_POST['reference_no'];
    $qty = $_POST['qty'];
    $unit = $_POST['unit'];
    $article = $_POST['article'];
    $description = $_POST['description'];
    $property_inventory_no = $_POST['property_inventory_no'];
    $date = $_POST['date'];
    $unit_cost = $_POST['unit_cost'];
    $total_cost = $_POST['total_cost'];
    $fund_cluster = $_POST['fund_cluster'];
    $remarks = $_POST['remarks'];

    try {
        // PDO query with named placeholders
        $stmt = $pdo->prepare("INSERT INTO property_summaries 
            (entity_name, accountable_officer, office_department, campus, reference_no, qty, unit, article, description, property_inventory_no, date, unit_cost, total_cost, fund_cluster, remarks) 
            VALUES (:entity_name, :accountable_officer, :office_department, :campus, :reference_no, :qty, :unit, :article, :description, :property_inventory_no, :date, :unit_cost, :total_cost, :fund_cluster, :remarks)");

        // Execute the prepared statement with actual values
        $stmt->execute([
            ':entity_name' => $entity_name,
            ':accountable_officer' => $accountable_officer,
            ':office_department' => $office_department,
            ':campus' => $campus,
            ':reference_no' => $reference_no,
            ':qty' => $qty,
            ':unit' => $unit,
            ':article' => $article,
            ':description' => $description,
            ':property_inventory_no' => $property_inventory_no,
            ':date' => $date,
            ':unit_cost' => $unit_cost,
            ':total_cost' => $total_cost,
            ':fund_cluster' => $fund_cluster,
            ':remarks' => $remarks
        ]);

        echo "<script>alert('Data saved successfully!');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Failed to save data: " . $e->getMessage() . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary of Property Accountability Records</title>
    <?php require_once 'includes/header_nav.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
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
        .main-container {
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form label {
            font-weight: 500;
            display: block;
            margin: 10px 0 5px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
            font-size: 16px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn-submit {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #0056b3;
        }
        .custom-select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: white;
    font-size: 16px;
    cursor: pointer;
    outline: none;
    transition: border 0.3s ease-in-out;
}

.custom-select:focus {
    border-color: #007bff;
    box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
}

    </style>
</head>
<body>
<?php require_once 'includes/side_nav.php'; ?>
<br>
<br>

<div class="main-container">
    <h2>Summary of Property Accountabilities</h2>
    <form action="" method="POST">
        <label>Entity Name:</label>
        <input type="text" name="entity_name" required>

        <label>Accountable Officer:</label>
        <input type="text" name="accountable_officer" required>

        <label>Office/Department:</label>
        <input type="text" name="office_department" required>

        <label>Campus:</label>
<select name="campus" required class="custom-select">
    <option value="" disabled selected>Select Campus</option>
    <option value="Botolan">Botolan</option>
    <option value="Cabangan">Cabangan</option>
    <option value="Castillejos">Castillejos</option>
    <option value="Iba">Iba</option>
    <option value="Masinloc">Masinloc</option>
    <option value="San Antonio">San Antonio</option>
    <option value="San Felipe">San Felipe</option>
    <option value="San Marcelino">San Marcelino</option>
    <option value="San Narciso">San Narciso</option>
    <option value="Sta. Cruz">Sta. Cruz</option>
    <option value="Subic">Subic</option>
</select>



        <table>
            <thead>
                <tr>
                    <th>Reference No./Date</th><th>Qty</th><th>Unit</th><th>Article</th><th>Description</th>
                    <th>Property/Inventory No.</th><th>Date</th><th>Unit Cost</th><th>Total Cost</th>
                    <th>Fund Cluster</th><th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="reference_no"></td>
                    <td><input type="number" name="qty"></td>
                    <td><input type="text" name="unit"></td>
                    <td><input type="text" name="article"></td>
                    <td><textarea name="description"></textarea></td>
                    <td><input type="text" name="property_inventory_no"></td>
                    <td><input type="date" name="date"></td>
                    <td><input type="number" name="unit_cost" step="0.01"></td>
                    <td><input type="number" name="total_cost" step="0.01"></td>
                    <td><input type="text" name="fund_cluster"></td>
                    <td><input type="text" name="remarks"></td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn-submit">Save Data</button>
    </form>
</div>
<script>
    // Column resizing logic for all fields
    document.querySelectorAll('th').forEach(th => {
        const resizer = document.createElement('div');
        resizer.classList.add('resizer');
        th.appendChild(resizer);

        resizer.addEventListener('mousedown', function (e) {
            const startX = e.clientX;
            const startWidth = th.offsetWidth;

            function resizeHandler(e) {
                const newWidth = startWidth + (e.clientX - startX);
                th.style.width = `${newWidth}px`;
                th.querySelectorAll('input, textarea').forEach(input => {
                    input.style.width = `${newWidth}px`;
                });
            }

            function stopResize() {
                window.removeEventListener('mousemove', resizeHandler);
                window.removeEventListener('mouseup', stopResize);
            }

            window.addEventListener('mousemove', resizeHandler);
            window.addEventListener('mouseup', stopResize);
        });
    });

    // Arrow key navigation logic
    document.addEventListener('keydown', function (e) {
        const inputs = Array.from(document.querySelectorAll('input, textarea'));
        const currentIndex = inputs.indexOf(document.activeElement);

        switch (e.key) {
            case 'ArrowRight':
                if (currentIndex !== -1 && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                }
                break;
            case 'ArrowLeft':
                if (currentIndex !== -1 && currentIndex > 0) {
                    inputs[currentIndex - 1].focus();
                }
                break;
            case 'ArrowDown':
                if (currentIndex !== -1 && currentIndex + 11 < inputs.length) {
                    inputs[currentIndex + 11].focus();
                }
                break;
            case 'ArrowUp':
                if (currentIndex !== -1 && currentIndex - 11 >= 0) {
                    inputs[currentIndex - 11].focus();
                }
                break;
        }
    });
</script>

</body>
</html>
