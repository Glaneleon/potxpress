<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
// $user_id = 1;
$onSelected = 1;

$sqlQuery = "SELECT * FROM shipping_address WHERE user_id = '".$user_id."' AND onSelected = '".$onSelected."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $addressOnSelect = array();
    while($row_found = $resultOfQuery->fetch_assoc()){
        $addressOnSelect[] = $row_found;
    }
    echo json_encode(array("success" => true,
    "addressOnSelect" => $addressOnSelect
));

}
else{
    echo json_encode(array("success" => false));
}

?>