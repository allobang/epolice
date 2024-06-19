<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

$userId = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
if ($userId) {
    $stmt = $conn->prepare("SELECT * FROM profile WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();
    } else {
        die("User profile not found.");
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
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <img class="card-img-top" src="assets/img/profile/<?php echo htmlspecialchars($profile['profilepicture']); ?>" alt="Profile picture">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">User Profile</h5>
                                    <p><b>First Name:</b> <?php echo htmlspecialchars($profile['firstname']); ?></p>
                                    <p><b>Last Name:</b> <?php echo htmlspecialchars($profile['lastname']); ?></p>
                                    <p><b>Address:</b> <?php echo htmlspecialchars($profile['address']); ?></p>
                                    <p><b>City:</b> <?php echo htmlspecialchars($profile['city']); ?></p>
                                    <!-- Add other fields as needed -->
                                    <a href="update_profile_status.php?user_id=<?php echo $userId; ?>&action=approve" class="btn btn-success">Approve Profile</a>
                                    <a href="update_profile_status.php?user_id=<?php echo $userId; ?>&action=reject" class="btn btn-danger">Reject Profile</a>
                                </div>
                                
                            </div>
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
