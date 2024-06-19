<?php
// connection.php

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'epoliceclearance';

// Create a new mysqli connection instance
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
