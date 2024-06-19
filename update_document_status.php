<?php
include 'connection.php';

$userId = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
$documentType = isset($_GET['document_type']) ? $_GET['document_type'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($userId && $documentType && ($action === 'approve' || $action === 'reject')) {
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';

    // Update document status in the database
    $stmt = $conn->prepare("UPDATE documents SET status = ? WHERE user_id = ? AND document_type = ?");
    if (!$stmt) {
        error_log("Preparation failed: (" . $conn->errno . ") " . $conn->error);
        die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("sis", $status, $userId, $documentType);
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
