<?php
include '../../config/config.php';
//delete the otp code

$user_id = $_POST['user_id'];
// $status = 90;
// $email = "zoilojun38@gmail.com";

$deleteQuery = "DELETE users_otp FROM users_otp INNER JOIN uers_test ON users_otp.user_id = uers_test.user_id WHERE uers_test.user_id = '$user_id'";
$resultOfQuery = $conn->query($deleteQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true));
}
else{
    echo json_encode(array("success" => false));
}

?>