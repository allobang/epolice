<?php
include 'connection.php';

$userId = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($userId && ($action === 'approve' || $action === 'reject')) {
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';

    // Update profile status in the database
    $stmt = $conn->prepare("UPDATE profile SET status = ? WHERE user_id = ?");
    if (!$stmt) {
        error_log("Preparation failed: (" . $conn->errno . ") " . $conn->error);
        die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("si", $status, $userId);
    if (!$stmt->execute()) {
        error_log("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        die("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
    }
    $stmt->close();

    // Redirect back to the user monitoring page
    header("Location: monitor_users.php");
    exit;
} else {
    die("Invalid user ID or action.");
}
?>
