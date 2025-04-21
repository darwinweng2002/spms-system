<?php
require_once 'db.php';

$sql = "SELECT id, name, username, position, campus, avatar FROM admins";
$result = $conn->query($sql);

$admins = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($admins);
?>
