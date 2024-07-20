<?php
include '../../config/config.php';
date_default_timezone_set('Asia/Manila');

$user_email = $_POST['user_email'];
$verification_code = $_POST['verification_code'];

// $user_email = 'zoilojun38@gmail.com';
// $verification_code = '184091';

$sqlQuery = "SELECT user_verification_code.verification_code, user_verification_code.expired_at FROM user_verification_code INNER JOIN uers_test ON user_verification_code.user_id = uers_test.user_id WHERE uers_test.email ='$user_email'";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery -> num_rows > 0){
    $user_verification = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $user_verification[] = $rowFound;
    }
    if($user_verification[0]['verification_code'] == $verification_code){
        $currentTime = date("Y-m-d H:i:s");
        if($currentTime < $user_verification[0]['expired_at']){
            
            echo json_encode(array("success" => true));
        }
        else{
            echo json_encode(array("success" => false));
        }
    }
    else{
        echo json_encode(array("match" => false));
    }

}else{
    echo json_encode(array("error" => false));
}


?>
