<?php
include '../../config/config.php';
// get the stock of the product

$product_id= $_POST['product_id'];

// $product_id= 1;

$sqlQuery = "SELECT stock_quantity FROM products WHERE product_id = '$product_id' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $stock = array();
    while($row_found = $resultOfQuery-> fetch_assoc()){
        $stock[] = $row_found;
    }
    echo json_encode(array("success"=>true,
    "stock" => $stock
));
}
else{
    echo json_encode(array("success"=>false));
}


?>