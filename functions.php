<?php
if (!function_exists('fetchUserProfile')) {
    function fetchUserProfile($conn, $userId) {
        $sql = "SELECT * FROM profile WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        } else {
            return null;
        }
    }
}

if (!function_exists('checkDocumentUploaded')) {
    function checkDocumentUploaded($conn, $userId, $documentType) {
        $sql = "SELECT document_type FROM documents WHERE user_id = ? AND document_type = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $userId, $documentType);
        if ($stmt->execute()) {
            return $stmt->get_result()->num_rows > 0;
        } else {
            return false;
        }
    }
}

if (!function_exists('fetchClearanceStatus')) {
    function fetchClearanceStatus($conn, $userId) {
        $sql = "SELECT status FROM clearances WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->num_rows > 0 ? $result->fetch_assoc()['status'] : 'Pending';
        } else {
            return 'Pending';
        }
    }
}

function checkPaymentReceiptUploaded($conn, $userId) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM payment_receipts WHERE user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

?>
