<?php
if (!function_exists('fetchUserProfile')) {
    function fetchUserProfile($conn, $userId)
    {
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
    function checkDocumentUploaded($conn, $userId, $documentType)
    {
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
    function fetchClearanceStatus($conn, $userId)
    {
        $sql = "SELECT status, hit_status FROM clearances WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return ['status' => 'Pending', 'hit_status' => 'Pending']; // Default values if no clearance found
        }
    }
}

if (!function_exists('checkPaymentReceiptUploaded')) {
    function checkPaymentReceiptUploaded($conn, $userId)
    {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM payment_receipts WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }
}

if (!function_exists('checkAppointmentBooked')) {
    function checkAppointmentBooked($conn, $userId)
    {
        $sql = "SELECT COUNT(*) FROM appointments WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }
}

if (!function_exists('getDocumentStatus')) {
    function getDocumentStatus($conn, $userId, $documentType)
    {
        $sql = "SELECT status FROM documents WHERE user_id = ? AND document_type = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $userId, $documentType);
        $stmt->execute();
        $stmt->bind_result($status);
        if ($stmt->fetch()) {
            return $status;
        } else {
            return null;
        }
    }
}

if (!function_exists('getPaymentReceiptStatus')) {
    function getPaymentReceiptStatus($conn, $userId)
    {
        $sql = "SELECT status FROM payment_receipts WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($status);
        if ($stmt->fetch()) {
            return $status;
        } else {
            return null;
        }
    }
}
?>
