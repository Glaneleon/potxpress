<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
$address_id = $_POST['address_id'];

// $user_id = 1;
// $address_id = 2;

$findOnSelected = 1;

$sqlQuery = "SELECT ship_id, onSelected FROM shipping_address WHERE user_id = '".$user_id."' AND onSelected = '".$findOnSelected."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0 ){
    $address = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $address[] = $rowFound;
    
    }
    $ship_address = $address[0]['ship_id'];
    $updateSqlQuery = "UPDATE shipping_address SET onSelected = 0 WHERE ship_id = $ship_address ";
    $resultOfUpdateQuery = $conn->query($updateSqlQuery);
    
    if($resultOfUpdateQuery){
       $onSelectedQuery = "UPDATE shipping_address SET onSelected = 1 WHERE ship_id = $address_id AND user_id = $user_id ";
       $resultSelectedQuery = $conn->query($onSelectedQuery);
       echo json_encode(array("success" => true ));
    }
    else{
        echo json_encode(array("success" => false ));
    }
    // echo json_encode(
    //     array(
    //         "success" => true,
    //         // "address" => $address[0]['ship_id']
    //     ));
 
}
// Default if there's no selected address
else{
    $defaultQuery = "UPDATE shipping_address SET onSelected = 1 WHERE ship_id = $address_id AND user_id = $user_id ";
    $resultDefaulQuery = $conn->query($defaultQuery);
    if($resultDefaulQuery){
        echo json_encode(array("success" => true ));
    }
    
}
?>