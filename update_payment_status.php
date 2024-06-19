<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

$receiptId = isset($_GET['receipt_id']) ? (int)$_GET['receipt_id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($receiptId > 0 && ($action == 'approve' || $action == 'reject')) {
    $status = ($action == 'approve') ? 'Approved' : 'Rejected';
    $stmt = $conn->prepare("UPDATE payment_receipts SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $receiptId);
    $stmt->execute();
    $stmt->close();
}

header("Location: monitor_users.php");
exit;
