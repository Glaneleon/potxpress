<?php
include '../../config/config.php';

//check the duplicate fo the wishlist 

$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];

$sqlQuery = "SELECT * FROM wishlist WHERE user_id = '".$user_id."' AND product_id = '".$product_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery->num_rows > 0){
    echo json_encode(array("success" => true));
}
else{
    echo json_encode(array("success" => false));
}
?>