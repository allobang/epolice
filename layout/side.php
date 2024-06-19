<?php
$userId = $_SESSION['userid'];
$userProfile = fetchUserProfile($conn, $userId);
$hasProfile = $userProfile !== null;
$link = $hasProfile ? 'profileDisplay.php' : 'profile.php';

// Fetch user type from the session
$userType = $_SESSION['user_type'];
?>

<nav id="sidebar" class="active">
    <div class="sidebar-header">
        <img src="assets/img/bootstraper-logo.png" alt="bootraper logo" class="app-logo">
    </div>
    <ul class="list-unstyled components text-secondary">
        <?php if ($userType !== 'admin'): ?>
            <li>
                <a href="newClearance.php"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li>
                <a href="<?php echo $link; ?>"><i class="fas fa-file-invoice"></i> Profile</a>
            </li>
        <?php endif; ?>
        <?php if ($userType === 'admin'): ?>
            <li>
                <a href="monitor_users.php"><i class="fas fa-user-friends"></i> Applicants</a>
            </li>
            <!-- <li>
                <a href="users.php"><i class="fas fa-user-friends"></i> Users</a>
            </li> -->
        <?php endif; ?>
    </ul>
</nav>
