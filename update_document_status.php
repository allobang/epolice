<?php
include 'connection.php';

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$documentType = isset($_POST['document_type']) ? $_POST['document_type'] : '';
$action = isset($_POST['action']) ? $_POST['action'] : '';
$remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';

if ($userId && $documentType && ($action === 'approve' || $action === 'reject')) {
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';

    // Update document status and remarks in the database
    $stmt = $conn->prepare("UPDATE documents SET status = ?, remarks = ? WHERE user_id = ? AND document_type = ?");
    if (!$stmt) {
        error_log("Preparation failed: (" . $conn->errno . ") " . $conn->error);
        die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ssis", $status, $remarks, $userId, $documentType);
    if (!$stmt->execute()) {
        error_log("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
        die("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
    }
    $stmt->close();

    // Redirect back to the view documents page
    header("Location: view_documents.php?user_id=" . $userId);
    exit;
} else {
    die("Invalid user ID, document type, or action.");
}
