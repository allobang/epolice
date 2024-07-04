<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];
    $remarks = $_POST['remarks'];

    $status = ($action == 'approve') ? 'Approved' : 'Rejected';

    // Ensure remarks are not empty when rejecting
    if ($action == 'reject' && empty($remarks)) {
        $_SESSION['error_message'] = "Remarks are required when rejecting a profile.";
        header("Location: view_profile.php?user_id=$userId");
        exit;
    }

    $stmt = $conn->prepare("UPDATE profile SET status = ?, remarks = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $status, $remarks, $userId);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile status updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update profile status.";
    }
    $stmt->close();

    header("Location: view_profile.php?user_id=$userId");
    exit;
}
?>
