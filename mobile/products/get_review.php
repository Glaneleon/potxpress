<?php
include '../../config/config.php';

$product_id = $_POST['product_id'];

$sqlQuery = "SELECT review_table.*, uers_test.firstname, uers_test.firstname, uers_test.lastname, uers_test.profile_img FROM review_table INNER JOIN uers_test ON review_table.user_id = uers_test.user_id WHERE review_table.product_id = '".$product_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $reviewList = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $reviewList[] = $rowFound;
    }
    echo json_encode(array("success"=>true,
    "reviews" => $reviewList
));
}
else{
    echo json_encode(array("success"=>false));
}

?>