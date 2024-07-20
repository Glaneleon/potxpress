<?php
    // include '../connection/connection.php';
    include '../../config/config.php';

    $userEmail = $_POST['email'];

    $sqlQuery = "SELECT * FROM uers_test WHERE email='$userEmail'";


    $resultOfQuery = $conn->query($sqlQuery);

    if($resultOfQuery -> num_rows > 0){
        echo json_encode(array("emailFound" => true));
    }
    else{
        echo json_encode(array("emailFound" => false));
    }
?>



