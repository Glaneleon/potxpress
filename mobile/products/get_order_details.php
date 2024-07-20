<?php

include '../../config/config.php';
$order_id = $_POST['order_id'];

// $order_id = 941;
// $sqlQuery = "SELECT add_to_cart.*, products.name, products.category, products.imagefilepath FROM add_to_cart INNER JOIN products ON add_to_cart.product_id = products.product_id WHERE add_to_cart.user_id = '$user_id'";

$sqlQuery = "SELECT order_details.*, products.name, products.category, products.imagefilepath FROM order_details INNER JOIN products ON order_details.product_id = products.product_id WHERE order_details.order_id = '$order_id'  ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $order_details = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $order_details[] = $rowFound;
    }
    echo json_encode(array("success"=>true, 'order_details' =>  $order_details));  
}
else{
    echo json_encode(array("success"=>false)); 
}


?>