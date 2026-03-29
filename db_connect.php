<?php
// Database connection file
// This file establishes a connection to the MySQL database using mysqli

$servername = "localhost"; // Database server
$username = "root"; // Database username (default for XAMPP)
$password = ""; // Database password (default for XAMPP)
$dbname = "insurance_db"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to utf8 for better character support
$conn->set_charset("utf8");
?>
