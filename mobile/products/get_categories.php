<?php
include '../../config/config.php';


$sqlQuery = "SELECT * FROM category";

$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery->num_rows > 0){
    $category = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $category[] = $rowFound;
       
    }
    echo json_encode(
        array(
            "success" => true,
            "category" => $category
        ));
 
}
else{
    echo json_encode(array("success" => false));
}



?>