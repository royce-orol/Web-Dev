<?php
$host = 'localhost';       // Hostname (XAMPP defaults to localhost)
$username = 'root';        // Default username for XAMPP
$password = '';            // Default password for XAMPP (empty)
$database = 'web_dev';     // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>