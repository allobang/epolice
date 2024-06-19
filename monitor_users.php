<?php
include 'includes/init.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

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
                                            echo "<a href=\"update_status.php?id=" . $row["ID"] . "&status=pending\" class=\"btn btn-outline-warning btn-rounded\">Pending</a>";
                                            echo "<a href=\"update_status.php?id=" . $row["ID"] . "&status=approved\" class=\"btn btn-outline-success btn-rounded\">Approve</a>";
                                            echo "<a href=\"update_status.php?id=" . $row["ID"] . "&status=rejected\" class=\"btn btn-outline-danger btn-rounded\">Reject</a>";
                                            echo "<button onclick=\"printUser('" . htmlspecialchars($row["name"]) . "', '" . htmlspecialchars($row["email"]) . "', '" . htmlspecialchars($row["user_type"]) . "')\" class=\"btn btn-outline-primary btn-rounded\">Print</button>";
                                            echo "</div>";
                                            echo "</td>";
                                            echo "</tr>";
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

    <div id="print-section" class="print-only" style="font-family: 'Times New Roman', serif; padding: 20px;">
        <div class="grid-container">
            <div class="grid-item">
                <img src="assets/img/pnp.png" style="width: 50%; height: auto;" alt="PNP Logo">
                <img src="person_image.png" style="width: 100%; height: auto; margin-top: 10px;" alt="Person Image">
                <p style="text-align: center;">PICTURE</p>

                <div class="grid-item signature-section">
                    <div style="text-align: center; margin-bottom: 10px;">
                        <p>SIGNATURE</p>
                        <img src="signature.png" alt="Signature" style="width: 100%;">
                    </div>
                    <div style="text-align: center;">
                        <p>THUMBMAR</p>
                        <img src="thumbmark.png" alt="Thumbmark" style="width: 100%;">
                    </div>

                    <div class="grid-item qr-code">
                        <img src="thumbmark.png" alt="QR Code" style="width: 100%; height: auto;">
                        <p>QR CODE</p>
                    </div>

                    <div class="grid-item">
                        <p><strong>DATE ISSUED:</strong> September 21, 2023</p>
                        <p><strong>VALID UNTIL:</strong> March 21, 2024</p>
                    </div>
                </div>
            </div>
            <div class="grid-item main-content">
                <div class="header" style="text-align: center; margin-bottom: 10px;">
                    <h1 style="font-size: 1.2em; margin-bottom: 0;">Republic of the Philippines</h1>
                    <h2 style="font-size: 1em; margin-bottom: 0;">NATIONAL POLICE COMMISSION</h2>
                    <h3 style="font-size: 0.9em; margin-bottom: 0;">PHILIPPINE NATIONAL POLICE</h3>
                    <p style="font-size: 0.8em;">Camp BGen Rafael T Crame, Quezon City</p>
                    <h2 style="font-size: 1em;">NATIONAL POLICE CLEARANCE</h2>
                </div>
                <p>THIS IS TO CERTIFY that the person whose name, photo, signature, and right thumbmark appear herein, has undergone routinary identification and verification of the Crime-Related Records and Identification of National Police Clearance System.</p>
                <div class="section">
                    <p><strong>NAME:</strong> <span id="print-name"></span></p>
                    <p><strong>ADDRESS:</strong> 1017 BANTUG (POB.), ROXAS ISABELA</p>
                    <p><strong>BIRTH DATE:</strong> December 31, 1996</p>
                    <p><strong>BIRTHPLACE:</strong> ROXAS, ISABELA</p>
                    <p><strong>CITIZENSHIP:</strong> FILIPINO</p>
                    <p><strong>GENDER:</strong> MALE</p>
                </div>
                <div class="no-record" style="text-align: center; padding: 10px; margin:auto;">
                    <p>NO RECORD ON FILE</p>
                </div>
            </div>
            <div class="grid-item">
                <div style="text-align: center; writing-mode: vertical-rl; transform: rotate(360deg); font-size: 50px  ">
                    <p>NO RECORD ON FILE</p>
                </div>
            </div>
        </div>
        <div class="grid-container footer-grid">
        </div>

        <div class="footer">
            <p>NOTE: To verify the authenticity of this Police Clearance, please visit https://pnpclearance.ph/ or use Q.R. code scanner</p>
        </div>
    </div>

    <style>
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 10px;
        }

        .footer-grid {
            grid-template-columns: 1fr 1fr 1fr;
            margin-top: 20px;
        }

        .grid-item {
            padding: 10px;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .print-only,
            .print-only * {
                visibility: visible;
            }

            .print-only {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
            }

            @page {
                size: auto;
                margin: 0;
            }
        }
    </style>

    <script>
        function printUser(name, email, userType) {
            document.getElementById('print-name').textContent = name;
            // Add additional data dynamically here
            window.print();
        }
    </script>
</body>
</html>
