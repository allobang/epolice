<?php
include 'includes/init.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['hit_status'])) {
    $id = intval($_GET['id']);
    $hit_status = $conn->real_escape_string($_GET['hit_status']);
    
    // Update the hit status in the database
    $sql = "UPDATE clearances SET hit_status = '$hit_status' WHERE user_id = $id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: monitor_users.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    header("Location: monitor_users.php");
    exit;
}
?>
