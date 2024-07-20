<?php
include '../../config/config.php';
// get all the cart list for the user

 $user_id = $_POST['user_id'];
//  $user_id = 1;

$sqlQuery = "SELECT add_to_cart.*, products.name, products.category, products.imagefilepath FROM add_to_cart INNER JOIN products ON add_to_cart.product_id = products.product_id WHERE add_to_cart.user_id = '$user_id'";
$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery ){
    $cart = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $cart[] = $rowFound;
    }
   
    echo json_encode(array(
        "success" => true,
        "cart" => $cart
    ));
}
else{
    echo json_encode(array("success" => false));
}


?>