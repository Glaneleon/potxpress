<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
$gender = $_POST['gender'];

// $user_id = 38;
// $gender = 'Female';

$sqlQuery = "UPDATE uers_test SET gender = '".$gender."' WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true));

}
else{
    echo json_encode(array("success" => false));
}

?>