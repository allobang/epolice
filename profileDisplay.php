<?php
include 'layout/head.php';
$firstname = '';
$lastname = '';
$middlename = '';
$address = '';
$city = '';
$province = '';
$zipcode = '';
$birthplace = '';
$formattedBirthdate = '';
$citizenship = '';
$gender = '';
$profilepicture = '';
if (isset($_SESSION['userid'])) {
    $userId = $_SESSION['userid'];
    $stmt = $conn->prepare("SELECT firstname, lastname, middlename, address, city, province, zipcode, birthplace, birthdate, citizenship, gender, profilepicture FROM profile WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $middlename = $row['middlename'];
        $address = $row['address'];
        $city = $row['city'];
        $province = $row['province'];
        $zipcode = $row['zipcode'];
        $birthplace = $row['birthplace'];
        $birthdate = $row['birthdate']; // Format this as needed
        $formattedBirthdate = date('m/d/Y', strtotime($birthdate));
        $citizenship = $row['citizenship'];
        $gender = $row['gender'];
        $profilepicture = $row['profilepicture'];
    }
    $stmt->close();
}
?>

<!doctype html>
<!-- 
* Bootstrap Simple Admin Template
* Version: 2.1
* Author: Alexis Luna
* Website: https://github.com/alexis-luna/bootstrap-simple-admin-template
-->
<html lang="en">

<body>
    <div class="wrapper">
        <?php include 'layout/side.php'; ?>
        <div id="body" class="active">
            <?php include 'layout/nav.php'; ?>
            <div class="content">
                <div class="container">
                    <div class="row">
                        <!-- First Column -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <img class="card-img-top" src="assets/img/profile/<?php echo htmlspecialchars($profilepicture); ?>" alt="Card image cap">
                                <div class="card-body text-center">
                                    <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        <i class="fas fa-file-upload"></i> Upload Image
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- modal part -->

                        <div class="modal fade" id="exampleModal" role="dialog" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Image</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <form class="needs-validation" method="post" action="uploadImage.php" enctype="multipart/form-data" novalidate>
                                            <div class="mb-3">
                                                <input class="form-control" type="file" id="formFile" name="uploadedFile">
                                            </div>
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                                Save</button>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- end modal part -->

                        <!-- Second Column -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <p class="card-text"><b>First Name:</b>
                                                <?php echo htmlspecialchars($firstname); ?>
                                            </p>
                                            <p class="card-text"><b>Last Name:</b>
                                                <?php echo htmlspecialchars($lastname); ?>
                                            </p>
                                            <p class="card-text"><b>Middle Name:</b>
                                                <?php echo htmlspecialchars($middlename); ?>
                                            </p>
                                            <p class="card-text"><b>Birthdate:</b>
                                                <?php echo htmlspecialchars($formattedBirthdate); ?>
                                            </p>
                                            <p class="card-text"><b>Gender:</b>
                                                <?php echo htmlspecialchars($gender); ?>
                                            </p>

                                            <a href="profile.php?user_id=<?php echo $userId; ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </div>
                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <p class="card-text"><b>Address:</b>
                                                <?php echo htmlspecialchars($address); ?>
                                            </p>
                                            <p class="card-text"><b>City:</b>
                                                <?php echo htmlspecialchars($city); ?>
                                            </p>
                                            <p class="card-text"><b>Province:</b>
                                                <?php echo htmlspecialchars($province); ?>
                                            </p>
                                            <p class="card-text"><b>ZIP:</b>
                                                <?php echo htmlspecialchars($zipcode); ?>
                                            </p>
                                            <p class="card-text"><b>Citizenship:</b>
                                                <?php echo htmlspecialchars($citizenship); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile card -->

                    <!-- End of Profile card -->
                </div>
            </div>
        </div>
    </div>

    <!-- Custom styles for this page -->
    <style>
        .profile-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .profile-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .profile-img {
            width: 200px;
            /* Set both width and height to the same value for a square */
            height: 200px;
            border-radius: 0;
            /* Explicitly set to 0 to ensure no rounding */
            border: 3px solid #007bff;
            /* Bootstrap primary color */
            object-fit: cover;
            /* Ensures the image covers the area without stretching */
        }

        .card-title {
            color: #007bff;
            /* Bootstrap primary color */
            margin-bottom: 1rem;
        }

        .card-text {
            font-size: 1rem;
        }
    </style>



    <?php include 'layout/foot.php'; ?>