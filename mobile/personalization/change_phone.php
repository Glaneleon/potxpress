<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
$phoneNO = $_POST['phoneNo'];


$sqlQuery = "UPDATE uers_test SET phone_no = '".$phoneNO."' WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true));

}
else{
    echo json_encode(array("success" => false));
}

?>