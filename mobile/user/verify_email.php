<?php
include '../../config/config.php';
// NOTE: di ko nagamit tong file pwede i-delete

$user_email = $_POST['user_email'];
// $user_email = 'zoilojun38@gmail.com';

// $sqlQuery = "SELECT verification_code FROM uers_test WHERE email ='$user_email'";

$sqlQuery = "SELECT user_verification_code.verification_code FROM user_verification_code INNER JOIN uers_test ON user_verification_code.user_id = uers_test.user_id WHERE uers_test.email ='$user_email'";
$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery -> num_rows > 0){
    $userVerificationCode = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $userVerificationCode = $rowFound;
    }
 
    echo json_encode(array(
        "verificationCodeFound" => true,
        "userVerificationCode"=>$userVerificationCode,
    ));
}
else{
    echo json_encode(array("verificationCodeFound" => false));
}


?>
