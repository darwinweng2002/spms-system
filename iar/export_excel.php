<?php
require_once '../db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=IAR_Supplier_Records.xls");

$query = "SELECT * FROM iar_suppliers";
$stmt = $pdo->query($query);

echo "Supplier Name\tAddress\tContact Information\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "{$row['supplier_name']}\t{$row['address']}\t{$row['contact_info']}\n";
}
?>
