<?php

include '../../config/config.php';
date_default_timezone_set('Asia/Manila');
$orderId = $_POST['order_id'];
$productId = $_POST['product_id'];
$userId = $_POST['user_id'];
//$productQuantity = $_POST['product_quantity'];
$ship_id = $_POST['ship_id'];
$productTotalPrice = $_POST['total_amount'];
$paymentMode = $_POST['payment_mode'];

// $orderId = 1;
// $productId = 3;
// $userId = 38;
// //$productQuantity = $_POST['product_quantity'];
// $productTotalPrice = 90.50;
// $ship_id = 15;
$payment_img = './assets/payment/default.png' ;

if($paymentMode == "cod"){
    $payment_img = './assets/payment/cod.png';
}

// date
$orderDate = date("Y/m/d H:i:s");
$order_id_no =  abs(crc32(uniqid()));
// print($order_id_no);



$sqlQueryOrder = "INSERT INTO orders(order_id_no, user_id, product_id, ship_id, order_date, total_amount, payment_mode, payment_img, status) VALUES ('".$order_id_no."', '".$userId."','".$productId."', '".$ship_id."', '".$orderDate."', '".$productTotalPrice."', '".$paymentMode."', '".$payment_img."', '1')";
$resultOfQuery = $conn->query($sqlQueryOrder);
$order_id = $conn->insert_id;



if($resultOfQuery){
    $sqlInsertOrderStatus = "INSERT INTO order_status(order_id, order_placed) VALUES ('".$order_id."', '".$orderDate."') ";
    $resultOfSqlInsertOrderStatus = $conn->query($sqlInsertOrderStatus);
    if($resultOfSqlInsertOrderStatus){
        echo json_encode(array("success"=>true,
            "order_id" => $order_id
         ));  
    }
    else{
        echo json_encode(array("success"=>false)); 
    }
  
}
else{
    echo json_encode(array("success"=>false)); 
}





?>