<?php
include '../../config/config.php';

//Tong file nato siya yung nag validate ng otp kung tama yung otp na input ni user

$user_id = $_POST['user_id'];
$otp_code = $_POST['otp_code'];

// $user_id = 35;
// $otp_code = '337867';


// $user_email = 'zoilojun38@gmail.com';

// $sqlQuery = "SELECT otp_code FROM uers_test WHERE user_id ='$user_id'";

// $resultOfQuery = $conn->query($sqlQuery);

$sqlQuery = "SELECT users_otp.otp_code, users_otp.expired_at FROM users_otp INNER JOIN uers_test ON users_otp.user_id = uers_test.user_id WHERE uers_test.user_id ='$user_id'";
$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery -> num_rows > 0){
    $user_otp = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $user_otp[] = $rowFound;
    }
    if($user_otp[0]['otp_code'] == $otp_code){
        $currentTime = date("Y-m-d H:i:s");
        if($currentTime < $user_otp[0]['expired_at']){
            
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