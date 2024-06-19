<?php
$userId = $_SESSION['userid'];
$userProfile = fetchUserProfile($conn, $userId);
$hasProfile = $userProfile !== null;
$link = $hasProfile ? 'profileDisplay.php' : 'profile.php';
?>

<nav id="sidebar" class="active">
    <div class="sidebar-header">
        <img src="assets/img/bootstraper-logo.png" alt="bootraper logo" class="app-logo">
    </div>
    <ul class="list-unstyled components text-secondary">
        <li>
            <a href="newClearance.php"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <!-- <li>
            <a href="#uielementsmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle no-caret-down"><i class="fas fa-file-alt"></i> Transaction</a>
            <ul class="collapse list-unstyled" id="uielementsmenu">
                <li>
                    <a href="newClearance.php"><i class="fas fa-angle-right"></i> New Clearance</a>
                </li>
                <li>
                    <a href="displayQuestions.php"><i class="fas fa-angle-right"></i> Renew</a>
                </li>
            </ul>
        </li> -->
        <li>
            <a href="<?php echo $link; ?>"><i class="fas fa-file-invoice"></i> Profile</a>
        </li>
        </li>
        <li>
            <a href="monitor_users.php"><i class="fas fa-user-friends"></i>Applicants</a>
        </li>
        <li>
            <a href="users.php"><i class="fas fa-user-friends"></i>Users</a>
        </li>
        <!-- <li>
                    <a href="settings.html"><i class="fas fa-cog"></i>Settings</a>
                </li> -->
    </ul>
</nav>