<?php
session_start();
require_once 'db.php'; 

// ✅ Check if Employee ID & Name are Set
if (!isset($_GET['id']) || !isset($_GET['name'])) {
    header("Location: employee_list.php");
    exit;
}

// ✅ Get Employee ID & Name from URL
$employee_id = $_GET['id'];
$employee_name = urldecode($_GET['name']); // Decode URL encoding

?>
<form method="POST">
    <label for="entity_name">Entity Name:</label>
    <input type="text" name="entity_name" value="<?= htmlspecialchars($employee_name) ?>" readonly>
    
    <label for="document_type">Document Type:</label>
    <select name="document_type">
        <option value="PAR">Property Acknowledgement Receipt (PAR)</option>
        <option value="ICS">Inventory Custodian Slip (ICS)</option>
    </select>

    <button type="submit">Submit</button>
</form>
