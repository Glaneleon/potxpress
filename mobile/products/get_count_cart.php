<?php
include '../../config/config.php';

$user_id = $_POST['user_id'];

$sqlQuery = "SELECT * FROM add_to_cart WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $count_cart = 0;
    while($row_found = $resultOfQuery->fetch_assoc()){
        $count_cart++;
    }

    echo json_encode(array(
        "success" => true,
        'countCart'=> $count_cart
));
}
else{
    echo json_encode(array("success" => false));
}

?>