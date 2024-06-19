<?php
include 'layout/head.php';
include 'connection.php';
include_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['userid'];

// Fetch user profile
$userProfile = fetchUserProfile($conn, $userId);

// Check if the profile is filled
$hasPersonalInfo = !empty($userProfile['firstname']) && !empty($userProfile['lastname']) && !empty($userProfile['address']) && !empty($userProfile['city']) && !empty($userProfile['province']) && !empty($userProfile['zipcode']) && !empty($userProfile['birthplace']) && !empty($userProfile['birthdate']) && !empty($userProfile['citizenship']) && !empty($userProfile['gender']);
$hasProfilePicture = !empty($userProfile['profilepicture']);

// Check if user has uploaded valid ID and barangay clearance
$validIdUploaded = checkDocumentUploaded($conn, $userId, 'valid_id');
$barangayClearanceUploaded = checkDocumentUploaded($conn, $userId, 'barangay_clearance');

$requirementsUploaded = $validIdUploaded && $barangayClearanceUploaded;

// Check if user has uploaded payment receipt
$paymentReceiptUploaded = checkPaymentReceiptUploaded($conn, $userId);

// Fetch clearance status
$clearanceStatus = fetchClearanceStatus($conn, $userId);
?>

<!doctype html>
<html lang="en">

<head>
    <link rel="stylesheet" href="assets/css/custom-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            background-color: #ffffff;
            color: #343a40;
        }

        .step-card-body {
            padding: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-container {
            margin-top: 15px;
        }

        .check-mark {
            color: green;
            font-weight: bold;
        }

        .progress {
            height: 30px;
        }

        .progress-bar {
            background-color: #28a745;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
        }

        .status-pending {
            background-color: #ffc107;
        }

        .status-approved {
            background-color: #28a745;
        }

        .status-rejected {
            background-color: #dc3545;
        }

        .page-title {
            color: #007bff;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .step-card {
            width: 250px;
        }

        .icon-success {
            color: #28a745;
        }

        .icon-fail {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layout/side.php'; ?>
        <div id="body" class="active">
            <?php include 'layout/nav.php'; ?>
            <div class="content">
                <div class="container text-center">
                    <!-- title -->
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">New</div>
                            <h2 class="page-title">Police Clearance</h2>
                        </div>
                    </div>
                    <!-- end title -->
                    <!-- Application Status -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Application Status</h5>
                                    <p class="card-text">Your current application status is:</p>
                                    <span class="status-badge status-<?= strtolower($clearanceStatus); ?>"><?= ucfirst($clearanceStatus); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="progress mt-4">
                        <?php
                        $progress = 0;
                        if ($hasPersonalInfo && $hasProfilePicture) $progress += 33;
                        if ($requirementsUploaded) $progress += 33;
                        if ($paymentReceiptUploaded) $progress += 34;
                        ?>
                        <div class="progress-bar" role="progressbar" style="width: <?= $progress; ?>%;" aria-valuenow="<?= $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-center flex-wrap">
                        <!-- Step 1 -->
                        <div class="card text-center step-card m-3">
                            <div class="card-header">Step 1</div>
                            <div class="card-body step-card-body">
                                <h5 class="card-title">Personal Information</h5>
                                <p class="card-text">
                                    <?php if ($hasPersonalInfo) : ?>
                                        <i class="fas fa-check-circle icon-success"></i> Fill up Personal Information form
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Fill up Personal Information form
                                    <?php endif; ?><br>
                                    <?php if ($hasProfilePicture) : ?>
                                        <i class="fas fa-check-circle icon-success"></i> Upload 2x2 photo
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Upload 2x2 photo
                                    <?php endif; ?>
                                </p>
                                <div class="btn-container">
                                    <?php if ($hasPersonalInfo && $hasProfilePicture) : ?>
                                        <span class="check-mark"><i class="fas fa-check-circle icon-success"></i> Completed</span>
                                    <?php else : ?>
                                        <a href="profileDisplay.php" class="btn btn-primary">Go to Step 1</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div class="card text-center step-card m-3">
                            <div class="card-header">Step 2</div>
                            <div class="card-body step-card-body">
                                <h5 class="card-title">Upload Requirements</h5>
                                <p class="card-text">
                                    <?php if ($validIdUploaded) : ?>
                                        <i class="fas fa-check-circle icon-success"></i> Valid ID
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Valid ID
                                    <?php endif; ?><br>
                                    <?php if ($barangayClearanceUploaded) : ?>
                                        <i class="fas fa-check-circle icon-success"></i> Barangay Clearance
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Barangay Clearance
                                    <?php endif; ?>
                                </p>
                                <div class="btn-container">
                                    <?php if ($hasPersonalInfo && $hasProfilePicture) : ?>
                                        <?php if ($requirementsUploaded) : ?>
                                            <span class="check-mark"><i class="fas fa-check-circle icon-success"></i> Completed</span>
                                        <?php else : ?>
                                            <a href="upload_requirements.php" class="btn btn-primary">Go to Step 2</a>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <button class="btn btn-primary" disabled>Go to Step 2</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div class="card text-center step-card m-3">
                            <div class="card-header">Step 3</div>
                            <div class="card-body step-card-body">
                                <h5 class="card-title">Payment</h5>
                                <p class="card-text">
                                    <?php if ($paymentReceiptUploaded) : ?>
                                        <i class="fas fa-check-circle icon-success"></i> Receipt Uploaded
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Upload Receipt
                                    <?php endif; ?>
                                </p>
                                <div class="btn-container">
                                    <?php if ($requirementsUploaded) : ?>
                                        <?php if ($paymentReceiptUploaded) : ?>
                                            <span class="check-mark"><i class="fas fa-check-circle icon-success"></i> Completed</span>
                                        <?php else : ?>
                                            <a href="payment.php" class="btn btn-primary">Go to Step 3</a>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <button class="btn btn-primary" disabled>Go to Step 3</button>
                                    <?php endif; ?>
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
