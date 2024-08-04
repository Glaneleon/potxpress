<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];

$sqlQuery = "UPDATE uers_test SET firstname = '".$firstname."', lastname = '".$lastname."' WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true));

}
else{
    echo json_encode(array("success" => false));
}

?>