<?php

include '../../config/config.php';

$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];


// $user_id = 39;
// $product_id = 3;

$findSqlQuery = "SELECT * FROM wishlist WHERE user_id = '$user_id' AND product_id = '$product_id' ";
$resultOfFindSqlQuery = $conn->query($findSqlQuery);
if($resultOfFindSqlQuery -> num_rows > 0){
    echo json_encode(array("match"=>true));
}
else{
    $sqlQuery = "INSERT INTO wishlist (user_id, product_id) VALUES ('".$user_id."', '".$product_id."')";
    $resultOfQuery = $conn->query($sqlQuery);
    if($resultOfQuery){
        echo json_encode(array("success"=>true));
    }
    else{
        echo json_encode(array("success"=>false));
    }
}


?>