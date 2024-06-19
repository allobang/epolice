<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>National Police Clearance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            width: 80%;  /* 80% of the parent width */
            height: 50vh;  /* 50% of the viewport height */
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            overflow: auto;  /* Ensure content doesn't overflow */
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
        }
        .header img {
            margin-right: 20px;
            width: 100px;
        }
        .header-text {
            text-align: center;
        }
        .header-text h2 {
            font-size: 18px;
            margin: 5px 0;
        }
        .header-text h3 {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .header-text h4 {
            font-size: 16px;
            margin: 5px 0;
        }
        .content {
            text-align: left;
        }
        .content .info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .content .info div {
            flex: 1 1 45%;
            padding: 10px;
            box-sizing: border-box;
        }
        .content .record {
            margin: 20px 0;
            font-weight: bold;
            color: red;
            text-align: center;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .signature-box {
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                width: 7in;
                height: 5in;
                border: none;
                box-shadow: none;
                page-break-inside: avoid;
            }
            .header, .content, .footer {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="path/to/logo.jpg" alt="PNP Logo">
            <div class="header-text">
                <h2>Republic of the Philippines</h2>
                <h3>NATIONAL POLICE COMMISSION</h3>
                <h3>PHILIPPINE NATIONAL POLICE</h3>
                <h4>Camp BGen Rafael T Crame, Quezon City</h4>
            </div>
        </div>

        <div class="content">
            <h2 style="text-align:center;">National Police Clearance</h2>
            <p style="text-align:center;">THIS IS TO CERTIFY that the person whose name, photo, signature, and right thumbmark appear herein, has undergone routinary identification and verification of the Crime-Related Records and Identification of National Police Clearance System.</p>

            <div class="info">
                <div>
                    <p><strong>NAME:</strong> SARMIENTO, HARDY OCAMPO</p>
                    <p><strong>ADDRESS:</strong> 1017 BANTUG (POB.), ROXAS ISABELA</p>
                    <p><strong>BIRTH DATE:</strong> December 31, 1996</p>
                    <p><strong>BIRTHPLACE:</strong> ROXAS, ISABELA</p>
                    <p><strong>CITIZENSHIP:</strong> FILIPINO</p>
                    <p><strong>GENDER:</strong> MALE</p>
                </div>
                <div style="text-align: center;">
                    <img src="path/to/image.jpg" alt="Person's Image" width="150">
                </div>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <p>Signature</p>
                </div>
                <div class="signature-box">
                    <p>Right Thumbmark</p>
                </div>
            </div>

            <div class="record">
                <p>NO RECORD ON FILE</p>
            </div>

            <div class="signature">
                <div>
                    <p><strong>DATE ISSUED:</strong> September 21, 2023</p>
                    <p><strong>VALID UNTIL:</strong> MARCH 21, 2024</p>
                </div>
                <div>
                    <img src="path/to/qr_code.jpg" alt="QR Code">
                    <p>QR Code 1</p>
                </div>
                <div>
                    <img src="path/to/qr_code.jpg" alt="QR Code">
                    <p>QR Code 2</p>
                </div>
            </div>

            <div class="footer">
                <p>NOTE: To verify the authenticity of this Police Clearance, please visit <a href="https://pnpclearance.ph/">https://pnpclearance.ph/</a> or use Q.R. code scanner</p>
            </div>
        </div>
    </div>
</body>
</html>
