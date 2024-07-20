<?php

include '../../config/config.php';

$user_id = $_POST['user_id'];

$sqlQuery = "SELECT * FROM orders WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $user_orders = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $user_orders[] = $rowFound;
    }
    echo json_encode(array("success"=>true, 'user_orders' => $user_orders));  
}
else{
    echo json_encode(array("success"=>false)); 
}


?>