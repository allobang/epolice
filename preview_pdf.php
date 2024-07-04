<?php
include 'includes/init.php';

// Fetch user ID from the request
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    die("Invalid user ID.");
}

// Fetch user profile data
$sql = "
    SELECT p.firstname, p.lastname, p.middlename, p.address, p.city, p.province, p.zipcode, 
           p.phone_number, p.birthplace, p.birthdate, p.citizenship, p.gender, 
           p.profilepicture, p.status AS profile_status,
           IF(d_valid_id.user_id IS NOT NULL AND d_barangay_clearance.user_id IS NOT NULL, 'Completed', 'Pending') AS requirements_status,
           COALESCE(c.status, 'Pending') AS clearance_status
    FROM profile p
    LEFT JOIN documents d_valid_id ON p.user_id = d_valid_id.user_id AND d_valid_id.document_type = 'valid_id'
    LEFT JOIN documents d_barangay_clearance ON p.user_id = d_barangay_clearance.user_id AND d_barangay_clearance.document_type = 'barangay_clearance'
    LEFT JOIN clearances c ON p.user_id = c.user_id
    WHERE p.user_id = $user_id
";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$user = $result->fetch_assoc();
if (!$user) {
    die("User not found.");
}

// Ensure the profile picture path is relative to the web root
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview PDF</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .print-button {
                display: none;
            }

            .header img {
                width: 50%;
                height: auto;
            }

            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .row {
                display: flex;
                justify-content: center;
                align-items: center;
            }
        }

        body {
            padding: 20px;
            font-size: 14px;
        }

        .header,
        .footer {
            margin-bottom: 20px;
        }

        .header h1,
        .header h2,
        .header h3 {
            margin: 0;
        }

        .header h1 {
            font-size: 24px;
        }

        .header h2 {
            font-size: 20px;
        }

        .header h3 {
            font-size: 18px;
        }

        .content p {
            margin: 5px 0;
        }

        .signature-section,
        .footer {
            margin-top: 40px;
        }

        .signature-section p {
            margin: 0;
        }

        .header img {
            width: 50%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="header text-center"><img src="assets/img/pnp.png" alt="PNP Logo" style="max-width: 100px;"></div>
            </div>
            <div class="col-md-6">
                <div class="header text-center">
                    <h1>Republic of the Philippines</h1>
                    <h2>NATIONAL POLICE COMMISSION</h2>
                    <h3>PHILIPPINE NATIONAL POLICE</h3>
                    <p>Camp BGen Rafael T Crame, Quezon City</p>
                    <h2>NATIONAL POLICE CLEARANCE</h2>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
        <div class="text-center mb-4">
            <img src="assets/img/profile/<?php echo htmlspecialchars($user['profilepicture']); ?>" alt='Person Image' class="img-fluid rounded mb-3" style="max-width: 150px;">
            <p><strong>DATE ISSUED:</strong> September 21, 2023</p>
            <p><strong>VALID UNTIL:</strong> March 21, 2024</p>
        </div>
        <div class="content">
            <p><strong>NAME:</strong> <?php echo htmlspecialchars($user['lastname']) . ', ' . htmlspecialchars($user['firstname'] . ' ' . $user['middlename']); ?></p>
            <p><strong>ADDRESS:</strong> <?php echo htmlspecialchars($user['address'] . ', ' . $user['city'] . ', ' . $user['province'] . ' ' . $user['zipcode']); ?></p>
            <p><strong>BIRTH DATE:</strong> <?php echo htmlspecialchars($user['birthdate']); ?></p>
            <p><strong>BIRTHPLACE:</strong> <?php echo htmlspecialchars($user['birthplace']); ?></p>
            <p><strong>CITIZENSHIP:</strong> <?php echo htmlspecialchars($user['citizenship']); ?></p>
            <p><strong>GENDER:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        </div>
        <div class="content mt-4">
            <p>This is to certify that the above person whose picture and thumb marks appear hereunder requests for clearance with the findings below:</p>
            <p>No derogatory record on file as of this date.</p>
        </div>
        <div class="signature-section text-center mt-4">
            <div class="d-flex justify-content-around mt-3">
                <div>
                    <p>____________________</p>
                    <p>Signature</p>
                </div>
                <div>
                    <p>____________________</p>
                    <p>Thumb Mark</p>
                </div>
            </div>
        </div>
        <div class="signature-section text-center mt-4">
            <div class="d-flex justify-content-around mt-3">
                <div>
                <p><strong>Prepared by:</strong></p>
                <br>
                    <p>____________________</p>
                </div>
                <div>
                <p><strong>Approved by:</strong></p>
                <br>
                    <p>____________________</p>
                </div>
            </div>
        </div>
        <div class="footer text-center mt-4">
            <p>NOTE: To verify the authenticity of this Police Clearance, please visit <a href="https://pnpclearance.ph/">https://pnpclearance.ph/</a> or use Q.R. code scanner</p>
        </div>
        <div class="text-center mt-4">
            <a href="javascript:window.print()" class="print-button btn btn-outline-primary">Print</a>
        </div>
    </div>
</body>

</html>