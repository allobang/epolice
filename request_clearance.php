<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['userid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $clearance_type = $_POST['clearance_type'];
    $stmt = $conn->prepare("INSERT INTO clearances (user_id, type, status, date_applied, hit_status) VALUES (?, ?, 'Pending', NOW(), 'Pending')");
    $stmt->bind_param('is', $user_id, $clearance_type);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Your clearance application has been submitted.";
    } else {
        $_SESSION['error_message'] = "There was an error processing your request.";
    }
    $stmt->close();

    header("Location: newClearance.php");
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
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">Request</div>
                            <h2 class="page-title">New Clearance</h2>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="content">
                                    <div class="head">
                                        <h5 class="mb-0">Request a New Clearance</h5>
                                        <p class="text-muted">Fill in the form below to request a new clearance.</p>
                                    </div>
                                    <div class="canvas-wrapper">
                                        <form action="request_clearance.php" method="POST">
                                            <div class="form-group">
                                                <label for="clearance_type">Clearance Type</label>
                                                <input type="text" class="form-control" id="clearance_type" name="clearance_type" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit Request</button>
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
