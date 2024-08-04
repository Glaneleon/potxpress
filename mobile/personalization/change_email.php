<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
$email = $_POST['email'];

// $user_id = 38;
// $email = 'zoilojun0406@gmail.com';



$findEmailQuery = "SELECT * FROM uers_test WHERE email='$email'";
$resultOfQuery = $conn->query($findEmailQuery);

if($resultOfQuery -> num_rows > 0){

    echo json_encode(array("emailFound" => true));

}
else{
    $sqlQuery = "UPDATE uers_test SET email = '".$email."' WHERE user_id = '".$user_id."' ";
    $resultOfQuery = $conn->query($sqlQuery);
    if($resultOfQuery){
        echo json_encode(array("success" => true));

    }
    else{
        echo json_encode(array("success" => false));
    }
    
}

?>