<?php
include '../../config/config.php';
// get all the cart list for the user

 $user_id = $_POST['user_id'];
 $onSelected = 1;
//  $user_id = 1;

// $user_id = 38;
// $onSelected = 1;

$sqlQuery = "SELECT ship_id FROM shipping_address  WHERE user_id = '$user_id' AND onSelected = '$onSelected'";
$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery){
    $address = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $address[] = $rowFound;
    }
   
    echo json_encode(array(
        "success" => true,
        "address" => $address
    ));
}
else{
    echo json_encode(array("success" => false));
}


?>