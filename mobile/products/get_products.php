<?php
include '../../config/config.php';
// Get the specific products in the database

 $product_category = $_POST['category'];
 


$sqlQuery = "SELECT * FROM products WHERE category = '$product_category'";

$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery->num_rows > 0){
    $products = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $products[] = $rowFound;
       
    }
    echo json_encode(
        array(
            "success" => true,
            "products" => $products
        ));
 
}
else{
    echo json_encode(array("success" => false));
}


?>