<?php
include '../../config/config.php';

$status = $_POST['user_status'];
$email = $_POST['user_email'];
// $status = 90;
// $email = "zoilojun38@gmail.com";

$sqlQuery = "UPDATE uers_test SET status = '$status' WHERE email='$email'";

$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery > 0){
   echo json_encode(array("success"=>true));
}
else{
    echo json_encode(array("success" => false));
}


?>
