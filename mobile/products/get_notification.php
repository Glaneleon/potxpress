<?php

include '../../config/config.php';
$userId = $_POST['user_id'];
// $userId = 38;
$sqlQuery = "SELECT * FROM notification WHERE user_id = $userId";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $notifications = array();
    while($row_found = $resultOfQuery->fetch_assoc()){
        $notifications[] = $row_found;
    }
    echo json_encode(
        array(
            "success" => true,
            "notifications" => $notifications
        ));

}
else{
    echo json_encode(array("success" => false));
}

?>