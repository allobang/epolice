<?php
session_start();
include('includes/init.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password, user_type FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Password is correct, store user details in session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['userid'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_type'] = $row['user_type'];

            // Redirect based on user type
            if ($row['user_type'] == 'admin') {
                header("Location: monitor_users.php");
            } else {
                header("Location: newClearance.php");
            }
            exit(); // Ensure script stops after redirection
        } else {
            // Password is not correct
            $_SESSION['login_error'] = "Invalid username or password";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Invalid username or password";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
}
$conn->close();
?>
