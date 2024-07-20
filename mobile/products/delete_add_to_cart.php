<?php

include '../../config/config.php';
$user_id = $_POST['user_id'];

$sqlQuery = "DELETE FROM add_to_cart WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery){
    echo json_encode(array("success"=>true)); 
}
else{
    echo json_encode(array("success"=>false)); 
}


?>