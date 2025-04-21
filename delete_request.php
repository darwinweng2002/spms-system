<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Optional: Delete related items first
    $pdo->prepare("DELETE FROM request_items WHERE request_id = ?")->execute([$id]);

    // Delete the request
    $stmt = $pdo->prepare("DELETE FROM request_letters WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: manage_request.php?success=1");
    exit();
}
?>
