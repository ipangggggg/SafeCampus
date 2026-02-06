<?php
$con = mysqli_connect("localhost", "root", "", "safecampus");

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = mysqli_query($con, $sql);

$response = array();

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $response["status"] = "success";
    $response["name"] = $row['name'];
} else {
    $response["status"] = "error";
    $response["message"] = "Invalid email or password";
}
echo json_encode($response);
?>