<?php
include 'db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Terima data dari Android
$user_name = $_POST['user_name'];
$incident_type = $_POST['incident_type'];
$description = $_POST['description'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Kita set nilai default untuk user_agent sebab dalam table awak ada column ni
$user_agent = "Android App";

// INSERT data (Sekarang kita masukkan sekali user_agent)
$sql = "INSERT INTO incidents (user_name, incident_type, description, latitude, longitude, user_agent) 
        VALUES ('$user_name', '$incident_type', '$description', '$latitude', '$longitude', '$user_agent')";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    // Kalau error, dia akan print error tersebut
    echo "Error Database: " . $conn->error;
}

$conn->close();
?>