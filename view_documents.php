<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

$selectedUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($selectedUserId > 0) {
    // Fetch the latest valid ID and Barangay Clearance for the user
    $stmt = $conn->prepare("
        SELECT document_type, file_path, status, remarks
        FROM documents
        WHERE user_id = ? AND (document_type = 'valid_id' OR document_type = 'barangay_clearance')
        ORDER BY document_type, uploaded_at DESC
    ");
    $stmt->bind_param("i", $selectedUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $documents = [];
    $seenTypes = [];
    
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['document_type'], $seenTypes)) {
            $documents[] = $row;
            $seenTypes[] = $row['document_type'];
        }
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
                        <h3>View Documents</h3>
                    </div>
                    <div class="box box-primary">
                        <div class="box-body">
                            <?php if (!empty($documents)): ?>
                                <?php foreach ($documents as $doc): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $doc['document_type']))) ?></h5>
                                            <p><a href="uploads/user_<?= $selectedUserId ?>/<?= htmlspecialchars($doc['document_type']) ?>/<?= htmlspecialchars(basename($doc['file_path'])) ?>" target="_blank">View Document</a></p>
                                            <p>Status: <?= htmlspecialchars($doc['status']) ?></p>
                                            <?php if (!empty($doc['remarks'])): ?>
                                                <p>Remarks: <?= htmlspecialchars($doc['remarks']) ?></p>
                                            <?php endif; ?>
                                            <form action="update_document_status.php" method="post">
                                                <div class="form-group">
                                                    <label for="remarks_<?= $doc['document_type'] ?>">Remarks:</label>
                                                    <textarea name="remarks" id="remarks_<?= $doc['document_type'] ?>" class="form-control"><?= htmlspecialchars($doc['remarks']) ?></textarea>
                                                </div>
                                                <input type="hidden" name="user_id" value="<?= $selectedUserId ?>">
                                                <input type="hidden" name="document_type" value="<?= $doc['document_type'] ?>">
                                                <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No documents found.</p>
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
