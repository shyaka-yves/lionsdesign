<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'lionsdes_devmoise');
define('DB_PASS', '%9Xgay9]pLMl');
define('DB_NAME', 'lionsdes_liondesign_db');

// Create connection
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
