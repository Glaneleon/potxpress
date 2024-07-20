<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
$ship_id = $_POST['ship_id'];

// $user_id = 1;
// $ship_id = 1;
// $user_id = 1;

$sqlQuery = "SELECT * FROM shipping_address WHERE user_id = '".$user_id."' AND ship_id = '".$ship_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0 ){
    $addressInfo = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $addressInfo[] = $rowFound;
       
    }
    echo json_encode(
        array(
            "success" => true,
            "address" => $addressInfo
        ));
 
}
else{
    echo json_encode(array("success" => false));
}

?>