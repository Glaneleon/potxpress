<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];

$sqlQuery = "SELECT * FROM uers_test WHERE user_id = '$user_id' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery->num_rows > 0){
    $userRecord = array();
      
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $userRecord = $rowFound;
    }

    echo json_encode(array("success" => true,
    "userData" => $userRecord));

}
else{
    echo json_encode(array("success" => false));
}

?>