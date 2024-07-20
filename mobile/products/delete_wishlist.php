<?php
include '../../config/config.php';
// $wishlist_id = $_POST['wishlist_id'];
$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];

// $user_id = 1;
// $product_id = 1;



$sqlQuery = "DELETE FROM wishlist WHERE user_id = '".$user_id."' AND product_id = '".$product_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success"=>true));
}
else{
    echo json_encode(array("success"=>false));
}


?>