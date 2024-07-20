<?php
include '../../config/config.php';

$orderIdDetail = $_POST['order_detail_id'];
$orderId = $_POST['order_id'];
$productId = $_POST['product_id'];
$productColor = $_POST['product_color'];
$productPrice = $_POST['price'];
$productQuantity = $_POST['quantity'];

// $orderIdDetail = $_POST['order_detail_id'];
// $orderId = rand();
// $productId = 2;
// $productPrice = 200;
// $productQuantity = 4;

$selectProduct = "SELECT stock_quantity, sold FROM products WHERE product_id = '$productId' ";
$resultOfSelectProduct = $conn->query($selectProduct);

if($resultOfSelectProduct -> num_rows > 0){
    $stock_quantity = array();
    while($row_found = $resultOfSelectProduct-> fetch_assoc()){
        
        $stock_quantity[] = $row_found;
    }
    
    $updated_stock = $stock_quantity[0]['stock_quantity'] - $productQuantity;
    $updated_sold = $stock_quantity[0]['sold'] + $productQuantity;

    $updateStockQuery = "UPDATE products SET stock_quantity = '$updated_stock', sold = '$updated_sold' WHERE product_id = '$productId'";
    $resultUpdateStock = $conn->query($updateStockQuery);

    if($resultUpdateStock){

        $sqlQuery = "INSERT INTO order_details(order_id, product_id, product_color, quantity, price) VALUES ('".$orderId."', '".$productId."', '".$productColor."','".$productQuantity."', '".$productPrice."')";
        $resultOfQuery = $conn->query($sqlQuery);

            if($resultOfQuery){
                echo json_encode(array("ok"=>true));
            }
            else{
                echo json_encode(array("error"=> false));
            }

    }
    else{
        echo json_encode(array("ok"=> false));
    }
    
}
else{
    echo json_encode(array("ok"=>false));
}




?>