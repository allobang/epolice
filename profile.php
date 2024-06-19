<?php
include('connection.php');
session_start();
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

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : null);

if ($userId !== null) {
    $stmt = $conn->prepare("SELECT firstname, lastname, middlename, address, city, province, zipcode, birthplace, birthdate, citizenship, gender, profilepicture FROM profile WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $hasProfile = false;
    if ($result->num_rows > 0) {
        $hasProfile = true;
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
} else {
    echo "User ID not set.";
    exit;
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
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">Profile</div>
                                <div class="card-body">
                                    <form class="needs-validation" method="post" action="profileAction.php" enctype="multipart/form-data" novalidate>
                                        <input type="hidden" name="hasProfile" value="<?= $hasProfile ? '1' : '0' ?>">
                                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">
                                        <div class="row g-2">
                                            <div class="mb-3 col-md-4">
                                                <label for="firstname" class="form-label">First Name</label>
                                                <input type="text" class="form-control" name="firstname" placeholder="First Name" required value="<?= htmlspecialchars($firstname) ?>">
                                                <small class="form-text text-muted">Enter a valid First Name.</small>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter your first name.</div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label for="lastname" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" name="lastname" placeholder="Last Name" required value="<?= htmlspecialchars($lastname) ?>">
                                                <small class="form-text text-muted">Enter a valid Last Name.</small>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter your last name.</div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label for="middlename" class="form-label">Middle Name</label>
                                                <input type="text" class="form-control" name="middlename" placeholder="Middle Name" required value="<?= htmlspecialchars($middlename) ?>">
                                                <small class="form-text text-muted">Enter a valid Middle Name.</small>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter your middle name.</div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" name="address" placeholder="1234 Main St, Unit, Building, or Floor" required value="<?= htmlspecialchars($address) ?>">
                                            <div class="valid-feedback">Looks good!</div>
                                            <div class="invalid-feedback">Please enter your address.</div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="mb-3 col-md-6">
                                                <label for="city" class="form-label">City</label>
                                                <input type="text" readonly class="form-control" name="city" placeholder="City" required value="Roxas">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter your City.</div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label for="province" class="form-label">Province</label>
                                                <select name="province" class="form-select" required readonly>
                                                    <option value="Isabela" selected>Isabela</option>
                                                    <option value="1">New York</option>
                                                    <option value="2">Los Angeles</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please select a State.</div>
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label for="zip" class="form-label">Zip code</label>
                                                <input type="text" class="form-control" name="zip" readonly value="3320" placeholder="00000" required>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter a Zip code.</div>
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="mb-3 col-md-6">
                                                <label for="birthplace" class="form-label">Birth Place</label>
                                                <input type="text" class="form-control" name="birthplace" placeholder="Town, City, Province" required value="<?= htmlspecialchars($birthplace) ?>">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter your birth place.</div>
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label for="birtdate" class="form-label">Birth Date</label>
                                                <input type="text" class="form-control datepicker-here" data-language="en" aria-describedby="datepicker" placeholder="Birth Date" required name="birthdate" value="<?= htmlspecialchars($formattedBirthdate) ?>">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter a birth date.</div>
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label for="citizenship" class="form-label">Citizenship</label>
                                                <input type="text" class="form-control" value="Filipino" placeholder="00000" required name="citizenship">
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter a citizenship.</div>
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label for="gender" class="form-label">Gender</label>
                                                <select class="form-select" id="gender" name="gender" required>
                                                    <option value=""></option>
                                                    <option value="male" <?= ($gender == 'male' ? 'selected' : '') ?>>Male</option>
                                                    <option value="female" <?= ($gender == 'female' ? 'selected' : '') ?>>Female</option>
                                                </select>
                                                <div class="valid-feedback">Looks good!</div>
                                                <div class="invalid-feedback">Please enter a gender.</div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                        </div>
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'layout/foot.php'; ?>
</body>
</html>
