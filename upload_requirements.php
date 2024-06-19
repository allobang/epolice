<?php
session_start();
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['userid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_dir = 'uploads/user_' . $user_id;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
        mkdir($upload_dir . '/valid_id', 0777, true);
        mkdir($upload_dir . '/barangay_clearance', 0777, true);
    }

    // Handle Valid ID Upload
    if (isset($_FILES['valid_id'])) {
        $valid_id_path = $upload_dir . '/valid_id/' . basename($_FILES['valid_id']['name']);
        if (move_uploaded_file($_FILES['valid_id']['tmp_name'], $valid_id_path)) {
            $stmt = $conn->prepare("INSERT INTO documents (user_id, document_type, file_path, uploaded_at) VALUES (?, 'valid_id', ?, NOW())");
            $stmt->bind_param('is', $user_id, $valid_id_path);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Handle Barangay Clearance Upload
    if (isset($_FILES['barangay_clearance'])) {
        $barangay_clearance_path = $upload_dir . '/barangay_clearance/' . basename($_FILES['barangay_clearance']['name']);
        if (move_uploaded_file($_FILES['barangay_clearance']['tmp_name'], $barangay_clearance_path)) {
            $stmt = $conn->prepare("INSERT INTO documents (user_id, document_type, file_path, uploaded_at) VALUES (?, 'barangay_clearance', ?, NOW())");
            $stmt->bind_param('is', $user_id, $barangay_clearance_path);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: newClearance.php"); // Redirect to a success page
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
                            <h2 class="page-title">Upload Requirements</h2>
                        </div>
                    </div>
                    <!-- end title -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="content">
                                    <div class="head">
                                        <h5 class="mb-0">Upload Your Requirements</h5>
                                        <p class="text-muted">Please upload your Valid ID and Barangay Clearance.</p>
                                    </div>
                                    <div class="canvas-wrapper">
                                        <form action="upload_requirements.php" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="valid_id">Upload Valid ID</label>
                                                <input type="file" class="form-control" id="valid_id" name="valid_id" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="barangay_clearance">Upload Barangay Clearance</label>
                                                <input type="file" class="form-control" id="barangay_clearance" name="barangay_clearance" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
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
