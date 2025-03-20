<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete file
    $stmt = $pdo->prepare("SELECT upload_letter FROM request_letters WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request && !empty($request['upload_letter']) && file_exists($request['upload_letter'])) {
        unlink($request['upload_letter']);
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM request_letters WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header("Location: manage_requests.php?deleted=1");
    exit;
}
?>
