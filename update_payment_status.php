<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

$receiptId = isset($_POST['receipt_id']) ? (int)$_POST['receipt_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';
$remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';

if ($receiptId > 0 && ($action == 'approve' || $action == 'reject')) {
    $status = ($action == 'approve') ? 'Approved' : 'Rejected';

    // Update receipt status and remarks in the database
    $stmt = $conn->prepare("UPDATE payment_receipts SET status = ?, remarks = ? WHERE id = ?");
    if (!$stmt) {
        error_log("Preparation failed: (" . $conn->errno . ") " . $conn->error);
        die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ssi", $status, $remarks, $receiptId);
    if (!$stmt->execute()) {
        error_log("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        die("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
    }
    $stmt->close();

    // Redirect back to the view payments page
    header("Location: monitor_users.php");
    exit;
} else {
    die("Invalid receipt ID, action, or remarks.");
}
