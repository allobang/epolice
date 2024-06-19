<?php
include 'includes/init.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $userId = intval($_GET['id']);
    $status = $_GET['status'];

    // Check if the clearance record exists
    $checkSql = "SELECT * FROM clearances WHERE user_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the existing record
        $sql = "UPDATE clearances SET status = ? WHERE user_id = ?";
    } else {
        // Insert a new record
        $sql = "INSERT INTO clearances (user_id, type, status, date_applied) VALUES (?, 'new', ?, NOW())";
    }

    $stmt = $conn->prepare($sql);
    if ($result->num_rows > 0) {
        $stmt->bind_param('si', $status, $userId);
    } else {
        $stmt->bind_param('is', $userId, $status);
    }

    if ($stmt->execute()) {
        header("Location: monitor_users.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
