<?php
function fuck(){
    var_dump('haha');
    die();
}
function fetchUserProfile($conn, $userId) {
    // var_dump('haha');
    $stmt = $conn->prepare("SELECT firstname, lastname, middlename, address, city, province, zipcode, birthplace, birthdate, citizenship, gender, profilepicture FROM profile WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}
