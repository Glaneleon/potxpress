<?php
include '../../config/config.php';
date_default_timezone_set('Asia/Manila');
$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];
$rating = $_POST['rating'];
$review = $_POST['review'];


// $user_id = 38;
// $rating = 1.5;
// $review = 'sjflkasjflksjfksd';

$current_time = date("Y-m-d H:i:s");

$sqlQuery = "INSERT INTO review_table(user_id, product_id, user_review, rating) VALUES ('".$user_id."','".$product_id."', '".$review."', '".$rating."')";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success"=>true));
}
else{
    echo json_encode(array("success"=>false));
}

?>