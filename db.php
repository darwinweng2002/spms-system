<?php
$host = 'localhost';
$dbname = 'u450897284_spms'; // Your database name
$username = 'u450897284_spmsuser'; // Your MySQL username
$password = 'Spmsprmsuiba1234'; // Your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]));
}
?>
