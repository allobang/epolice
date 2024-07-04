<?php
session_start();
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['userid'];
$remarks = '';

// Fetch remarks for the payment receipt
$stmt = $conn->prepare("SELECT remarks FROM payment_receipts WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($remarks);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_dir = 'uploads/user_' . $user_id . '/payment_receipt';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Handle Receipt Upload
    if (isset($_FILES['payment_receipt'])) {
        $receipt_path = $upload_dir . '/' . basename($_FILES['payment_receipt']['name']);
        if (move_uploaded_file($_FILES['payment_receipt']['tmp_name'], $receipt_path)) {
            // Check if a receipt already exists for this user
            $stmt = $conn->prepare("SELECT COUNT(*) FROM payment_receipts WHERE user_id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                // Update existing receipt
                $stmt = $conn->prepare("UPDATE payment_receipts SET file_path = ?, uploaded_at = NOW() WHERE user_id = ?");
                $stmt->bind_param('si', $receipt_path, $user_id);
            } else {
                // Insert new receipt
                $stmt = $conn->prepare("INSERT INTO payment_receipts (user_id, file_path, uploaded_at) VALUES (?, ?, NOW())");
                $stmt->bind_param('is', $user_id, $receipt_path);
            }
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: payment.php"); // Redirect to reload the page and see remarks
    exit;
}
?>

<!doctype html>
<html lang="en">
<?php include 'layout/head.php'; ?>
<body>
    <div class="wrapper">
        <?php include 'layout/side.php'; ?>
        <div id="body" class="active">
            <?php include 'layout/nav.php'; ?>
            <div class="content">
                <div class="container">
                    <!-- title -->
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">New</div>
                            <h2 class="page-title">Payment Upload</h2>
                        </div>
                    </div>
                    <!-- end title -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="content">
                                    <div class="head">
                                        <h5 class="mb-0">Upload Your Payment Receipt</h5>
                                        <p class="text-muted">Please pay 150 PHP to 09476761927 via GCash and upload your receipt.</p>
                                    </div>
                                    <div class="canvas-wrapper">
                                        <form action="payment.php" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="payment_receipt">Upload Payment Receipt</label>
                                                <input type="file" class="form-control" id="payment_receipt" name="payment_receipt" required>
                                                <?php if ($remarks): ?>
                                                    <div class="mt-2">
                                                        <p class="card-text"><b>Remarks:</b> <?= htmlspecialchars($remarks) ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="newClearance.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'layout/foot.php'; ?>
</body>
</html>
