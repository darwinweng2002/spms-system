<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE property_summaries SET 
            reference_no = :reference_no,
            qty = :qty,
            unit = :unit,
            article = :article,
            description = :description,
            property_inventory_no = :property_inventory_no,
            date = :date,
            unit_cost = :unit_cost,
            total_cost = :total_cost,
            fund_cluster = :fund_cluster,
            remarks = :remarks
        WHERE id = :id
    ");
    
    $stmt->execute([
        ':reference_no' => $_POST['reference_no'],
        ':qty' => $_POST['qty'],
        ':unit' => $_POST['unit'],
        ':article' => $_POST['article'],
        ':description' => $_POST['description'],
        ':property_inventory_no' => $_POST['property_inventory_no'],
        ':date' => $_POST['date'],
        ':unit_cost' => $_POST['unit_cost'],
        ':total_cost' => $_POST['total_cost'],
        ':fund_cluster' => $_POST['fund_cluster'],
        ':remarks' => $_POST['remarks'],
        ':id' => $_POST['id']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
