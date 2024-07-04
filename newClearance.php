<?php
session_start();
include 'layout/head.php';
require_once 'connection.php';
require_once 'functions.php';
require_once 'includes/auth.php'; // Use require_once to ensure it's included only once

// Check if user is logged in
checkUserLoggedIn();

$userId = $_SESSION['userid'];

// Fetch user profile
$userProfile = fetchUserProfile($conn, $userId);

// Check if the profile is filled
$hasPersonalInfo = !empty($userProfile['firstname']) && !empty($userProfile['lastname']) && !empty($userProfile['address']) && !empty($userProfile['city']) && !empty($userProfile['province']) && !empty($userProfile['zipcode']) && !empty($userProfile['birthplace']) && !empty($userProfile['birthdate']) && !empty($userProfile['citizenship']) && !empty($userProfile['gender']);
$hasProfilePicture = !empty($userProfile['profilepicture']);

// Get profile status

if ($userProfile) {
    $profileStatus = $userProfile['status'];
}


// Check if user has uploaded valid ID and barangay clearance
$validIdStatus = getDocumentStatus($conn, $userId, 'valid_id');
$barangayClearanceStatus = getDocumentStatus($conn, $userId, 'barangay_clearance');

$validIdUploaded = $validIdStatus !== null;
$barangayClearanceUploaded = $barangayClearanceStatus !== null;

$requirementsUploaded = $validIdUploaded && $barangayClearanceUploaded;

// Check if user has uploaded payment receipt
$paymentReceiptStatus = getPaymentReceiptStatus($conn, $userId);
$paymentReceiptUploaded = $paymentReceiptStatus !== null;

// Fetch clearance status
$clearanceStatus = fetchClearanceStatus($conn, $userId);
if (empty($clearanceStatus['hit_status'])) {
    $clearanceStatus['hit_status'] = 'Pending';
}

$status = $clearanceStatus['status'];
$hitStatus = $clearanceStatus['hit_status'];

// Check if user has booked an appointment
$appointmentBooked = checkAppointmentBooked($conn, $userId);

// Fetch appointment details if booked
$appointmentDetails = [];
if ($appointmentBooked) {
    $stmt = $conn->prepare("SELECT appointment_date, time_slot FROM appointments WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $appointmentDetails = $stmt->get_result()->fetch_assoc();
}
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
            flex: 1;
            margin: 10px;
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

        .status-hit {
            background-color: #dc3545;
        }

        .status-no-hit {
            background-color: #28a745;
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
            flex: 1;
            margin: 5px;
        }

        .icon-success {
            color: #28a745;
        }

        .icon-fail {
            color: #dc3545;
        }

        .icon-pending {
            color: #ffc107;
        }

        .step-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
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
                                    <p class="card-text">Your current application status is: <span class="status-badge status-<?= strtolower($status); ?>"><?= ucfirst($status); ?></span></p>

                                    <p class="mt-2">HIT Status: <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $hitStatus)); ?>"><?= ucfirst($hitStatus); ?></span></p>
                                    <?php if ($appointmentBooked) : ?>
                                        <p class="mt-2">Appointment Date: <?= date('F j, Y', strtotime($appointmentDetails['appointment_date'])); ?></p>
                                        <p>Time Slot: <?= $appointmentDetails['time_slot']; ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="progress mt-4">
                        <?php
                        $progress = 0;
                        if ($appointmentBooked) $progress += 25;
                        if ($hasPersonalInfo && $hasProfilePicture) $progress += 25;
                        if ($requirementsUploaded) $progress += 25;
                        if ($paymentReceiptUploaded) $progress += 25;
                        ?>
                        <div class="progress-bar" role="progressbar" style="width: <?= $progress; ?>%;" aria-valuenow="<?= $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="step-container">
                        <!-- Step 1: Appointment -->
                        <div class="card text-center step-card">
                            <div class="card-header">Step 1</div>
                            <div class="card-body step-card-body">
                                <h5 class="card-title">Book Appointment</h5>
                                <p class="card-text">
                                    <?php if ($appointmentBooked) : ?>
                                        <i class="fas fa-check-circle icon-success"></i> Appointment Booked
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Book an Appointment
                                    <?php endif; ?>
                                </p>
                                <div class="btn-container">
                                    <?php if ($appointmentBooked) : ?>
                                        <span class="check-mark"><i class="fas fa-check-circle icon-success"></i> Completed</span>
                                        <a href="book_appointment.php?action=edit" class="btn btn-primary">Edit Appointment</a>
                                    <?php else : ?>
                                        <a href="book_appointment.php" class="btn btn-primary">Go to Step 1</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Step 2: Profile -->
                        <div class="card text-center step-card">
                            <div class="card-header">Step 2</div>
                            <div class="card-body step-card-body">
                                <h5 class="card-title">Personal Information</h5>
                                <p class="card-text">
                                    <?php if (isset($profileStatus)) : ?>
                                        <?php if ($profileStatus == 'Approved') : ?>
                                            <i class="fas fa-check-circle icon-success"></i> Fill up Personal Information form
                                        <?php elseif ($profileStatus == 'Rejected') : ?>
                                            <i class="fas fa-times-circle icon-fail"></i> Fill up Personal Information form
                                        <?php else : ?>
                                            <i class="fas fa-hourglass-half icon-pending"></i> Fill up Personal Information form
                                        <?php endif; ?>
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
                                    <?php if ($appointmentBooked) : ?>
                                        <?php if ($hasPersonalInfo && $hasProfilePicture) : ?>
                                            <span class="check-mark"><i class="fas fa-check-circle icon-success"></i> Completed</span>
                                            <a href="profileDisplay.php?action=edit" class="btn btn-primary">Edit Personal Info</a>
                                        <?php else : ?>
                                            <a href="profileDisplay.php" class="btn btn-primary">Go to Step 2</a>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <button class="btn btn-primary" disabled>Go to Step 2</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Step 3: Document -->
                        <div class="card text-center step-card">
                            <div class="card-header">Step 3</div>
                            <div class="card-body step-card-body">
                                <h5 class="card-title">Upload Requirements</h5>
                                <p class="card-text">
                                    <?php if ($validIdStatus) : ?>
                                        <?php if ($validIdStatus == 'Approved') : ?>
                                            <i class="fas fa-check-circle icon-success"></i> Valid ID
                                        <?php elseif ($validIdStatus == 'Rejected') : ?>
                                            <i class="fas fa-times-circle icon-fail"></i> Valid ID
                                        <?php else : ?>
                                            <i class="fas fa-hourglass-half icon-pending"></i> Valid ID
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Valid ID
                                    <?php endif; ?><br>
                                    <?php if ($barangayClearanceStatus) : ?>
                                        <?php if ($barangayClearanceStatus == 'Approved') : ?>
                                            <i class="fas fa-check-circle icon-success"></i> Barangay Clearance
                                        <?php elseif ($barangayClearanceStatus == 'Rejected') : ?>
                                            <i class="fas fa-times-circle icon-fail"></i> Barangay Clearance
                                        <?php else : ?>
                                            <i class="fas fa-hourglass-half icon-pending"></i> Barangay Clearance
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Barangay Clearance
                                    <?php endif; ?>
                                </p>
                                <div class="btn-container">
                                    <?php if ($hasPersonalInfo && $hasProfilePicture) : ?>
                                        <?php if ($appointmentBooked) : ?>
                                            <?php if ($requirementsUploaded) : ?>
                                                <span class="check-mark"><i class="fas fa-check-circle icon-success"></i> Completed</span>
                                                <a href="upload_requirements.php?action=edit" class="btn btn-primary">Edit Requirements</a>
                                            <?php else : ?>
                                                <a href="upload_requirements.php" class="btn btn-primary">Go to Step 3</a>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <button class="btn btn-primary" disabled>Go to Step 3</button>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <button class="btn btn-primary" disabled>Go to Step 3</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Step 4: Payment -->
                        <div class="card text-center step-card">
                            <div class="card-header">Step 4</div>
                            <div class="card-body step-card-body">
                                <h5 class="card-title">Payment</h5>
                                <p class="card-text">
                                    <?php if ($paymentReceiptStatus) : ?>
                                        <?php if ($paymentReceiptStatus == 'Approved') : ?>
                                            <i class="fas fa-check-circle icon-success"></i> Receipt Uploaded
                                        <?php elseif ($paymentReceiptStatus == 'Rejected') : ?>
                                            <i class="fas fa-times-circle icon-fail"></i> Receipt Uploaded
                                        <?php else : ?>
                                            <i class="fas fa-hourglass-half icon-pending"></i> Receipt Uploaded
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <i class="fas fa-times-circle icon-fail"></i> Receipt Uploaded
                                    <?php endif; ?>
                                </p>
                                <div class="btn-container">
                                    <?php if ($requirementsUploaded) : ?>
                                        <?php if ($paymentReceiptUploaded) : ?>
                                            <span class="check-mark"><i class="fas fa-check-circle icon-success"></i> Completed</span>
                                            <a href="payment.php?action=edit" class="btn btn-primary">Edit Payment</a>
                                        <?php else : ?>
                                            <a href="payment.php" class="btn btn-primary">Go to Step 4</a>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <button class="btn btn-primary" disabled>Go to Step 4</button>
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