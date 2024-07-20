<?php
include '../../config/config.php';

$user_id = $_POST['user_id'];
$name = $_POST['name'];
$phone_no = $_POST['phone_no'];
$street = $_POST['street'];
$baranggay = $_POST['baranggay'];
$city = $_POST['city'];
$province = $_POST['province'];
$country = $_POST['country'];


// $user_id = 1;
// $name = 'sdfafas';
// $phone_no = 'sdfafas';
// $street = 'sdfafas';
// $baranggay = 'sdfafas';
// $city = 'sdfafas';
// $province = 'sdfafas';
// $country = 'sdfafas';

$sqlQuery = "INSERT INTO shipping_address(user_id, receiver_name, phone_number, street_no, baranggay, city, province, country) VALUES ('".$user_id."', '".$name."', '".$phone_no."', '".$street."', '".$baranggay."', '".$city."', '".$province."', '".$country."')";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success"=>true));
}
else{
    echo json_encode(array("success"=>false));
}

?>