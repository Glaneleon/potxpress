<?php
include '../../config/config.php';
$user_email = $_POST['user_email'];
// $user_email = "zoilojun38@gmail.com";

$deleteQuery = "DELETE user_verification_code FROM user_verification_code INNER JOIN uers_test ON user_verification_code.user_id = uers_test.user_id WHERE uers_test.email = '$user_email'";
$resultOfQuery = $conn->query($deleteQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true));
}
else{
    echo json_encode(array("success" => false));
}
?>