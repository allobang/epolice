<?php
include 'includes/init.php';
require_once 'vendor/autoload.php'; // Ensure this path is correct

use Twilio\Rest\Client;

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Twilio credentials from environment variables
$account_sid = getenv('TWILIO_ACCOUNT_SID');
$auth_token = getenv('TWILIO_AUTH_TOKEN');
$twilio_number = getenv('TWILIO_NUMBER');
$client = new Client($account_sid, $auth_token);

// Fetch user data and their progress
$sql = "
    SELECT u.ID, u.name, u.email, u.user_type, p.phone_number,
           IF(p.status = 'Approved', 'Completed', 'Pending') AS profile_status,
           IF(d_valid_id.user_id IS NOT NULL AND d_barangay_clearance.user_id IS NOT NULL, 'Completed', 'Pending') AS requirements_status,
           COALESCE(c.status, 'Pending') AS clearance_status
    FROM users u
    LEFT JOIN profile p ON u.ID = p.user_id
    LEFT JOIN documents d_valid_id ON u.ID = d_valid_id.user_id AND d_valid_id.document_type = 'valid_id'
    LEFT JOIN documents d_barangay_clearance ON u.ID = d_barangay_clearance.user_id AND d_barangay_clearance.document_type = 'barangay_clearance'
    LEFT JOIN clearances c ON u.ID = c.user_id
    GROUP BY u.ID
";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Function to send SMS
function send_sms($client, $phone_number, $message) {
    global $twilio_number;
    try {
        $client->messages->create(
            $phone_number,
            array(
                'from' => $twilio_number,
                'body' => $message
            )
        );
    } catch (Exception $e) {
        error_log("Failed to send SMS: " . $e->getMessage());
    }
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
                        <h3>User Monitoring</h3>
                    </div>
                    <div class="box box-primary">
                        <div class="box-body">
                            <table width="100%" class="table table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>Profile Status</th>
                                        <th>Requirements Status</th>
                                        <th>Clearance Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["user_type"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["profile_status"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["requirements_status"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["clearance_status"]) . "</td>";
                                            echo "<td class=\"text-end\">";
                                            echo "<div class='btn-group' role='group'>";
                                            echo "<a href=\"view_profile.php?user_id=" . $row["ID"] . "\" class=\"btn btn-outline-info btn-rounded\">View Profile</a>";
                                            echo "<a href=\"view_documents.php?user_id=" . $row["ID"] . "\" class=\"btn btn-outline-info btn-rounded\">View Documents</a>";
                                            echo "<a href=\"view_payments.php?user_id=" . $row["ID"] . "\" class=\"btn btn-outline-info btn-rounded\">View Payments</a>";
                                            echo "<a href=\"update_status.php?id=" . $row["ID"] . "&status=pending\" class=\"btn btn-outline-warning btn-rounded\" onclick=\"send_sms('Pending', " . $row["phone_number"] . ")\">Pending</a>";
                                            echo "<a href=\"update_status.php?id=" . $row["ID"] . "&status=approved\" class=\"btn btn-outline-success btn-rounded\" onclick=\"send_sms('Approved', " . $row["phone_number"] . ")\">Approve</a>";
                                            echo "<a href=\"update_status.php?id=" . $row["ID"] . "&status=rejected\" class=\"btn btn-outline-danger btn-rounded\" onclick=\"send_sms('Rejected', " . $row["phone_number"] . ")\">Reject</a>";
                                            echo "</div>";
                                            echo "</td>";
                                            echo "</tr>";

                                            // Send SMS based on status
                                            if ($_GET['id'] == $row["ID"] && isset($_GET['status'])) {
                                                $status = $_GET['status'];
                                                $phone_number = $row["phone_number"];
                                                $message = "Your application status has been updated to: " . $status;
                                                send_sms($client, $phone_number, $message);
                                            }
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>0 results</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layout/foot.php'; ?>

    <style>
        .btn-group .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
    </style>
</body>
</html>
