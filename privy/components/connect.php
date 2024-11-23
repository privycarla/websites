<?php

$db_host = 'localhost';
$db_name = 'blog';
$db_user = 'root';
$db_password = '';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

try {
    $conn = new PDO($dsn, $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // echo "Connection successful"; // Debug message
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
