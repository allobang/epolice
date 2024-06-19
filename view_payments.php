<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

$selectedUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($selectedUserId > 0) {
    $stmt = $conn->prepare("SELECT id, file_path, uploaded_at, status FROM payment_receipts WHERE user_id = ?");
    $stmt->bind_param("i", $selectedUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $receipts = [];
    while ($row = $result->fetch_assoc()) {
        $receipts[] = $row;
    }
    $stmt->close();
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
                    <div class="page-title">
                        <h3>View Payment Receipts</h3>
                    </div>
                    <div class="box box-primary">
                        <div class="box-body">
                            <?php if (!empty($receipts)): ?>
                                <?php foreach ($receipts as $receipt): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Payment Receipt</h5>
                                            <p><a href="<?= htmlspecialchars($receipt['file_path']) ?>" target="_blank">View Receipt</a></p>
                                            <p>Uploaded At: <?= htmlspecialchars($receipt['uploaded_at']) ?></p>
                                            <p>Status: <?= htmlspecialchars($receipt['status']) ?></p>
                                            <a href="update_payment_status.php?receipt_id=<?= $receipt['id'] ?>&action=approve" class="btn btn-success">Approve</a>
                                            <a href="update_payment_status.php?receipt_id=<?= $receipt['id'] ?>&action=reject" class="btn btn-danger">Reject</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No payment receipts found.</p>
                            <?php endif; ?>
                            <a href="monitor_users.php" class="btn btn-secondary">Back to User Monitoring</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layout/foot.php'; ?>
</body>
</html>
