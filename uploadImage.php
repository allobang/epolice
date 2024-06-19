<?php
session_start();
include('connection.php');
$hasProfile = isset($_POST['hasProfile']) && $_POST['hasProfile'] == '1';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['userid'];

if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] == 0) {
    // Fetch the current profile picture filename
    $stmt = $conn->prepare("SELECT profilepicture FROM profile WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentProfilePicture = $row['profilepicture'];
        // Check if there's an existing profile picture and delete it
        if (!empty($currentProfilePicture)) {
            $fileToDelete = 'assets/img/profile/' . $currentProfilePicture;
            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            }
        }
    }

    // Continue with your file upload process
    $originalFileName = $_FILES['uploadedFile']['name'];
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $fileExtension;
    $uploadDirectory = 'assets/img/profile/';
    move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $uploadDirectory . $newFileName);
    $profilepicture = $newFileName;

    // Update the database with the new profile picture filename
    $stmt = $conn->prepare("UPDATE profile SET profilepicture = ? WHERE user_id = ?");
    $stmt->bind_param("si", $profilepicture, $userId);
    if ($stmt->execute()) {
        header('location: profileDisplay.php');
    } else {
        // Handle error, maybe log it or notify the user
        header('location: profileDisplay.php');
    }
} else {
    // Handle file upload error, maybe log it or notify the user
    header('location: profileDisplay.php');
}
