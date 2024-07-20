<?php
include '../../config/config.php';

$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];
$product_color = $_POST['product_color'];

// $user_id = 1;
// $product_id = 2;
// $product_color = "Blue";

$sqlQuery = "DELETE FROM add_to_cart WHERE user_id='".$user_id."' AND product_id= '".$product_id."' AND product_color='".$product_color."' ";
$resultQuery = $conn->query($sqlQuery);

if($resultQuery){
    echo json_encode(array("successRemove" => true));
}
else{
    echo json_encode(array("successRemove" => false));
}

?>