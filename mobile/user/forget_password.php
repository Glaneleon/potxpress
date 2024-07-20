<?php
include '../../config/config.php';


$email = $_POST['user_email'];
$user_password = md5($_POST['user_password']);

// $email = 'zoilojun38@gmail.com';
// $user_password = 'pasado';

$sqlQuery = "UPDATE uers_test SET passwords = '".$user_password."' WHERE email = '".$email."' ";

$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery > 0){
   echo json_encode(array("success"=>true));
}
else{
    echo json_encode(array("success" => false));
}


?>