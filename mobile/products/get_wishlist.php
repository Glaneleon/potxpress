<?php

include '../../config/config.php';

$user_id = $_POST['user_id'];

// $user_id = 46;


$sqlQuery = "SELECT wishlist.*, products.name, products.category,products.description, products.imagefilepath, products.price, products.stock_quantity, products.sold FROM wishlist INNER JOIN products ON wishlist.product_id = products.product_id WHERE wishlist.user_id = '$user_id' ";

$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $wishlistList = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $wishlistList[] = $rowFound;
       
    }
    echo json_encode(array(
        "success" => true,
        "wishlist" => $wishlistList
    ));
}
else{
    echo json_encode(array("success" => false));
}

?>