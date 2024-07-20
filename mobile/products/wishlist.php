<?php

include '../../config/config.php';

$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];


// $user_id = 46;
// $product_id = 27;

$sqlQuery = "INSERT INTO wishlist (user_id, product_id) VALUES ('".$user_id."', '".$product_id."')";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success"=>true));
}
else{
    echo json_encode(array("success"=>false));
}


?>