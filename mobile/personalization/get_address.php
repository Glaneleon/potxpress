<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
// $user_id = 1;

$sqlQuery = "SELECT ship_id FROM shipping_address WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0 ){
    $listOfAddress = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $listOfAddress[] = $rowFound;
       
    }
    echo json_encode(
        array(
            "success" => true,
            "address" => $listOfAddress
        ));
 
}
else{
    echo json_encode(array("success" => false));
}

?>