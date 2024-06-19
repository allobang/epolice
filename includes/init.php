<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php'; // Adjust path if needed

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Include the database connection
include('connection.php');
?>
