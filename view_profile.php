<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

$profileUserId = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
echo "Initial profileUserId: " . $profileUserId . "<br>"; // Debugging statement
$profile = null;
$error_message = '';

if ($profileUserId) {
    $stmt = $conn->prepare("SELECT firstname, lastname, middlename, address, city, province, zipcode, phone_number, birthplace, birthdate, citizenship, gender, profilepicture FROM profile WHERE user_id = ?");
    $stmt->bind_param("i", $profileUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();
        $formattedBirthdate = date('m/d/Y', strtotime($profile['birthdate']));
    } else {
        $error_message = "User profile not found.";
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
                        <h3>User Profile</h3>
                    </div>
                    <div class="box box-primary">
                        <div class="box-body">
                            <?php if ($error_message): ?>
                                <p><?php echo $error_message; ?></p>
                                <a href="monitor_users.php" class="btn btn-secondary">Back to User Monitoring</a>
                            <?php else: ?>
                                <div class="row">
                                    <!-- First Column -->
                                    <div class="col-md-4">
                                        <div class="card mb-3">
                                            <img class="card-img-top" src="assets/img/profile/<?php echo htmlspecialchars($profile['profilepicture']); ?>" alt="Profile picture">
                                        </div>
                                    </div>
                                    <!-- Second Column -->
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">User Profile</h5>
                                                <div class="row">
                                                    <!-- Left Column -->
                                                    <div class="col-md-6">
                                                        <p class="card-text"><b>First Name:</b> <?php echo htmlspecialchars($profile['firstname']); ?></p>
                                                        <p class="card-text"><b>Last Name:</b> <?php echo htmlspecialchars($profile['lastname']); ?></p>
                                                        <p class="card-text"><b>Middle Name:</b> <?php echo htmlspecialchars($profile['middlename']); ?></p>
                                                        <p class="card-text"><b>Birthdate:</b> <?php echo htmlspecialchars($formattedBirthdate); ?></p>
                                                        <p class="card-text"><b>Gender:</b> <?php echo htmlspecialchars($profile['gender']); ?></p>
                                                    </div>
                                                    <!-- Right Column -->
                                                    <div class="col-md-6">
                                                        <p class="card-text"><b>Address:</b> <?php echo htmlspecialchars($profile['address']); ?></p>
                                                        <p class="card-text"><b>City:</b> <?php echo htmlspecialchars($profile['city']); ?></p>
                                                        <p class="card-text"><b>Province:</b> <?php echo htmlspecialchars($profile['province']); ?></p>
                                                        <p class="card-text"><b>ZIP:</b> <?php echo htmlspecialchars($profile['zipcode']); ?></p>
                                                        <p class="card-text"><b>Phone Number:</b> <?php echo htmlspecialchars($profile['phone_number']); ?></p>
                                                        <p class="card-text"><b>Birthplace:</b> <?php echo htmlspecialchars($profile['birthplace']); ?></p>
                                                        <p class="card-text"><b>Citizenship:</b> <?php echo htmlspecialchars($profile['citizenship']); ?></p>
                                                    </div>
                                                </div>
                                                <?= $profileUserId ?>
                                                <a href="update_profile_status.php?user_id=<?php echo $profileUserId; ?>&action=approve" class="btn btn-success">Approve Profile</a>
                                                <a href="update_profile_status.php?user_id=<?php echo $profileUserId; ?>&action=reject" class="btn btn-danger">Reject Profile</a>
                                                <a href="monitor_users.php" class="btn btn-secondary">Back to User Monitoring</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layout/foot.php'; ?>
</body>
</html>
