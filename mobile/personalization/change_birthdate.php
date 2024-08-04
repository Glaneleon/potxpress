<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
$birthdate = $_POST['birthdate'];
$age = $_POST['age'];

// $user_id = 38;
// $gender = 'Female';

$sqlQuery = "UPDATE uers_test SET birthdate = '".$birthdate."', age = '".$age."' WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true));

}
else{
    echo json_encode(array("success" => false));
}

?>