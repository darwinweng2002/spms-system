<?php
include 'config.php'; // Ensure this file connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity_name = $_POST['entity_name'];
    $accountable_officer = $_POST['accountable_officer'];
    $office_department = $_POST['office_department'];
    $campus = $_POST['campus'];

    // Loop through each property entry
    $references = $_POST['reference'];
    $quantities = $_POST['quantity'];
    $units = $_POST['unit'];
    $descriptions = $_POST['description'];
    $inventory_nos = $_POST['inventory_no'];
    $dates = $_POST['date'];
    $unit_costs = $_POST['unit_cost'];
    $total_costs = $_POST['total_cost'];
    $fund_clusters = $_POST['fund_cluster'];
    $remarks = $_POST['remarks'];

    // Insert query for the summary details
    $stmt = $conn->prepare("INSERT INTO property_summary (entity_name, accountable_officer, office_department, campus) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $entity_name, $accountable_officer, $office_department, $campus);
    $stmt->execute();

    // Get the last inserted ID to link property details to this summary
    $summary_id = $conn->insert_id;

    // Insert query for property details
    $stmt_detail = $conn->prepare("INSERT INTO property_details (summary_id, reference_no, quantity, unit, description, inventory_no, date, unit_cost, total_cost, fund_cluster, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($references); $i++) {
        $stmt_detail->bind_param("issssssssss",
            $summary_id,
            $references[$i],
            $quantities[$i],
            $units[$i],
            $descriptions[$i],
            $inventory_nos[$i],
            $dates[$i],
            $unit_costs[$i],
            $total_costs[$i],
            $fund_clusters[$i],
            $remarks[$i]
        );
        $stmt_detail->execute();
    }

    echo "<script>alert('Property Summary Successfully Saved!'); window.location.href='index.php';</script>";
} else {
    echo "<script>alert('Invalid request. Please submit the form correctly.'); window.location.href='index.php';</script>";
}
?>