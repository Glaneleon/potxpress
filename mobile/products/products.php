<?php
include '../../config/config.php';


$sqlQuery = "SELECT * FROM products";

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