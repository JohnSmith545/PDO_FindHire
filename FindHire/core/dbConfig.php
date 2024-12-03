<?php  
$host = "localhost";
$user = "root";
$password = "";
$dbname = "findhire";

$dsn = "mysql:host={$host};dbname={$dbname}";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->exec("SET time_zone = '+08:00';");  // Set the time zone to Manila
    date_default_timezone_set('Asia/Manila'); // Set the default PHP time zone
} catch (PDOException $e) {
    // Handle connection errors
    die("Could not connect to the database: " . $e->getMessage());
}

require_once 'core/models.php';
?>