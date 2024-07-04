<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Fetch user ID from the request
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    die("Invalid user ID.");
}

// Include your database connection
include 'includes/init.php';

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
    WHERE u.ID = $user_id
    GROUP BY u.ID
";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$user = $result->fetch_assoc();
if (!$user) {
    die("User not found.");
}

// Create a new DOMPDF instance
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Get base URL for absolute image paths
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/';

// Load HTML content
$html = "
<!doctype html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>User Details</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            padding: 20px;
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .section {
            margin-top: 20px;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 10px;
        }
        .grid-item {
            padding: 10px;
        }
        .main-content {
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class='grid-container'>
        <div class='grid-item'>
            <img src='" . $base_url . "assets/img/pnp.png' alt='PNP Logo'>
            <img src='" . $base_url . "person_image.png' alt='Person Image'>
            <p style='text-align: center;'>PICTURE</p>
            <div class='signature-section'>
                <div style='text-align: center; margin-bottom: 10px;'>
                    <p>SIGNATURE</p>
                    <img src='" . $base_url . "signature.png' alt='Signature'>
                </div>
                <div style='text-align: center;'>
                    <p>THUMBMAR</p>
                    <img src='" . $base_url . "thumbmark.png' alt='Thumbmark'>
                </div>
                <div class='qr-code'>
                    <img src='" . $base_url . "thumbmark.png' alt='QR Code'>
                    <p>QR CODE</p>
                </div>
                <div>
                    <p><strong>DATE ISSUED:</strong> September 21, 2023</p>
                    <p><strong>VALID UNTIL:</strong> March 21, 2024</p>
                </div>
            </div>
        </div>
        <div class='grid-item main-content'>
            <div class='header'>
                <h1 style='font-size: 1.2em; margin-bottom: 0;'>Republic of the Philippines</h1>
                <h2 style='font-size: 1em; margin-bottom: 0;'>NATIONAL POLICE COMMISSION</h2>
                <h3 style='font-size: 0.9em; margin-bottom: 0;'>PHILIPPINE NATIONAL POLICE</h3>
                <p style='font-size: 0.8em;'>Camp BGen Rafael T Crame, Quezon City</p>
                <h2 style='font-size: 1em;'>NATIONAL POLICE CLEARANCE</h2>
            </div>
            <p>THIS IS TO CERTIFY that the person whose name, photo, signature, and right thumbmark appear herein, has undergone routinary identification and verification of the Crime-Related Records and Identification of National Police Clearance System.</p>
            <div class='section'>
                <p><strong>NAME:</strong> " . htmlspecialchars($user['name']) . "</p>
                <p><strong>ADDRESS:</strong> 1017 BANTUG (POB.), ROXAS ISABELA</p>
                <p><strong>BIRTH DATE:</strong> December 31, 1996</p>
                <p><strong>BIRTHPLACE:</strong> ROXAS, ISABELA</p>
                <p><strong>CITIZENSHIP:</strong> FILIPINO</p>
                <p><strong>GENDER:</strong> MALE</p>
            </div>
            <div class='no-record' style='text-align: center; padding: 10px; margin:auto;'>
                <p>NO RECORD ON FILE</p>
            </div>
        </div>
        <div class='grid-item'>
            <div style='text-align: center; writing-mode: vertical-rl; transform: rotate(360deg); font-size: 50px;'>
                <p>NO RECORD ON FILE</p>
            </div>
        </div>
    </div>
    <div class='footer'>
        <p>NOTE: To verify the authenticity of this Police Clearance, please visit https://pnpclearance.ph/ or use Q.R. code scanner</p>
    </div>
</body>
</html>
";

// Load content into DOMPDF
$dompdf->loadHtml($html);

// Set up the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF (force download)
$dompdf->stream("user_details.pdf", array("Attachment" => 1));
?>
