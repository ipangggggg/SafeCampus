<?php
include 'db.php';

$response = array();

// 1. Ambil data LOCATIONS (Klinik, Security)
$sql_loc = "SELECT location_name, location_type, latitude, longitude FROM locations";
$result_loc = $conn->query($sql_loc);

while($row = $result_loc->fetch_assoc()) {
    $row['category'] = 'static'; // Penanda ini lokasi tetap
    array_push($response, $row);
}

// 2. Ambil data INCIDENTS (Report student tadi)
$sql_inc = "SELECT incident_type, description, latitude, longitude FROM incidents";
$result_inc = $conn->query($sql_inc);

while($row = $result_inc->fetch_assoc()) {
    // Tukar nama field supaya standard
    $data['location_name'] = $row['incident_type'];
    $data['location_type'] = $row['description'];
    $data['latitude'] = $row['latitude'];
    $data['longitude'] = $row['longitude'];
    $data['category'] = 'incident'; // Penanda ini report kemalangan
    array_push($response, $data);
}

// Hantar semua data dalam bentuk JSON ke Android
echo json_encode($response);
?>