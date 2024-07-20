<?php
include '../../config/config.php';

$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];
$product_price = $_POST['product_price'];
$product_color = $_POST['product_color'];
$product_quantity = $_POST['product_quantity'];

// $product_id = 2;
// $user_id = 1;
// $product_quantity = 1;
// $product_price = 200;
// $product_color = 'Blue';

$findSimilarCartQuery = "SELECT * FROM add_to_cart WHERE product_id = '$product_id' AND user_id = '$user_id' AND product_color = '$product_color' ";
$resultOfQueryFind = $conn->query($findSimilarCartQuery );


if($resultOfQueryFind -> num_rows > 0){
    
    $product_total_price = $product_quantity * $product_price;
    $updateCart = "UPDATE add_to_cart SET product_quantity = '".$product_quantity."', product_price_total = '".$product_total_price."'  WHERE user_id = '".$user_id."' AND product_id = '".$product_id."' AND product_color = '".$product_color."'   ";
    $resultOfUpdateCart = $conn->query($updateCart);
    echo json_encode(array("success" => true));

}
else{
    echo json_encode(array("success" => false));
}



?>