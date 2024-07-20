<?php
include '../../config/config.php';

$user_id = $_POST['user_id'];
$ship_id = $_POST['ship_id'];

$sqlQuery = "DELETE FROM shipping_address WHERE user_id = '".$user_id."' AND ship_id = '".$ship_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true));

}
else{
    echo json_encode(array("success" => false));
}

?>