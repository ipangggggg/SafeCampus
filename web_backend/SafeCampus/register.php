<?php
$con = mysqli_connect("localhost", "root", "", "safecampus");
if (!$con) { die("Connection failed: " . mysqli_connect_error()); }

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

$sql_check = "SELECT * FROM users WHERE email='$email'";
$result_check = mysqli_query($con, $sql_check);

$response = array();

if (mysqli_num_rows($result_check) > 0) {
    $response["status"] = "error";
    $response["message"] = "Email already exists";
} else {
    $sql_insert = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if (mysqli_query($con, $sql_insert)) {
        $response["status"] = "success";
        $response["message"] = "Registration successful";
    } else {
        $response["status"] = "error";
        $response["message"] = "Database error";
    }
}
echo json_encode($response);
?>