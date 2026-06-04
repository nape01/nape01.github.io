<?php
// Database Configuration
define('DB_HOST', 'sql108.infinityfree.com');
define('DB_USER', 'if0_42098834');
define('DB_PASS', 'MoKtXMDKGR');
define('DB_NAME', 'if0_42098834_NBConnect_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?>
