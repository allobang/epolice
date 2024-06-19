<?php
include('connection.php');
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : (isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : null);
if ($userId === null) {
    echo "User ID not set.";
    exit;
}

$hasProfile = isset($_POST['hasProfile']) && $_POST['hasProfile'] == '1';
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$middlename = $_POST['middlename'];
$address = $_POST['address'];
$city = $_POST['city'];
$province = $_POST['province'];
$zipcode = $_POST['zip'];
$birthplace = $_POST['birthplace'];
$birthdate = $_POST['birthdate'] ? date('Y-m-d', strtotime($_POST['birthdate'])) : null;
$citizenship = $_POST['citizenship'];
$gender = $_POST['gender'];

if ($hasProfile) {
    $stmt = $conn->prepare("UPDATE profile SET firstname=?, lastname=?, middlename=?, address=?, city=?, province=?, zipcode=?, birthplace=?, birthdate=?, citizenship=?, gender=? WHERE user_id=?");
    $stmt->bind_param("sssssssssssi", $firstname, $lastname, $middlename, $address, $city, $province, $zipcode, $birthplace, $birthdate, $citizenship, $gender, $userId);
} else {
    $stmt = $conn->prepare("INSERT INTO profile (user_id, firstname, lastname, middlename, address, city, province, zipcode, birthplace, birthdate, citizenship, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssss", $userId, $firstname, $lastname, $middlename, $address, $city, $province, $zipcode, $birthplace, $birthdate, $citizenship, $gender);
}

if ($stmt->execute()) {
    header('Location: profileDisplay.php');
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
