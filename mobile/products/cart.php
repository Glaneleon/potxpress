<?php
include '../../config/config.php';

$cart_id = $_POST['cart_id'];
$product_id = $_POST['product_id'];
$user_id = $_POST['user_id'];
$product_quantity = $_POST['product_quantity'];
$product_price = $_POST['product_price'];
$product_color = $_POST['product_color'];

$product_price_total;

// $cart_id = 1;
// $product_id = 2;
// $user_id = 1;
// $product_quantity = 1;
// $product_price = 200;
// $product_color = 'Blue';

$findSimilarCartQuery = "SELECT * FROM add_to_cart WHERE product_id = '$product_id' AND user_id = '$user_id' AND product_color = '$product_color' ";
$resultOfQueryFind = $conn->query($findSimilarCartQuery );

if($resultOfQueryFind -> num_rows > 0){
    $cart = array();
    $currentProductQuantity;
    while($rowFound = $resultOfQueryFind->fetch_assoc()){
        $cart[] = $rowFound;
    }
    foreach($cart as $row){
        $currentProductQuantity = $row['product_quantity'];
    }
    $updateQuantity = $currentProductQuantity + $product_quantity;
    //calculate the price
    $product_price_total = $updateQuantity * $product_price;


    $updateCart = "UPDATE add_to_cart SET product_quantity = '".$updateQuantity."', product_price_total = '".$product_price_total."'  WHERE user_id = '".$user_id."' AND product_id = '".$product_id."' AND product_color = '".$product_color."'   ";
    $resultOfUpdateCart = $conn->query($updateCart);
    echo json_encode(array("success" => true));

}
else{
    $product_price_total = $product_quantity * $product_price;
    $sqlQuery = "INSERT INTO add_to_cart(product_id, user_id, product_quantity, product_price, product_price_total, product_color) VALUES ('".$product_id."', '".$user_id."', '".$product_quantity."', '".$product_price."', '".$product_price_total."', '".$product_color."')";
    $resultOfQuery = $conn->query($sqlQuery);

    if($resultOfQuery){
        echo json_encode(array("success" => true));
    }
    else{
        echo json_encode(array("success" => false));
    }
    
}





?>