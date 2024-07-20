<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// resend the otp code

require '../../vendor/autoload.php';
include '../../config/config.php';

$user_id = $_POST['user_id'];
$email = $_POST['user_email'];

// $user_id = 35;

$mail = new PHPMailer(true);
try{
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;  
    // $mail->SMTPDebug = 0;                    //Enable verbose debug output
    // $mail->isSMTP();                                            //Send using SMTP
    // $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    // $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    // $mail->Username   = 'qjyzoilo@tip.edu.ph';                            //SMTP username
    // $mail->Password   = 'gdtntorjqhfgcdmi';                          //SMTP password
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    // $mail->Port       = 465;   
    
    // //Recipients
    // $mail->setFrom('from@example.com', 'Pot Supplier Manila');
    // $mail->addAddress($email); 

    $otpCode = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

    //Content
    // $mail->isHTML(true);                                  //Set email format to HTML
    // $mail->Subject = 'OTP Verification';
    // $mail->Body    = '<p> Your OTP code is: <b style="font-size: 30px;">' .
    //    $otpCode . '</b></p>';
    
    // $mail->send();

    $currentTime = date("Y-m-d H:i:s");
    $expiredDate = date("y-m-d H:i:s", strtotime(' +5 minutes', strtotime($currentTime)));


    $sqlQuery = "UPDATE users_otp SET otp_code = '".$otpCode."', created_at = '".$currentTime."' , expired_at = '".$expiredDate."' WHERE user_id = '".$user_id."' ";

    $resultOfQuery = $conn->query($sqlQuery);
    
    if($resultOfQuery){
        echo json_encode(array("success"=>true));
    }
    else{
        echo json_encode(array("success"=>false));
    }  
    exit();
}
catch(Exception $e){
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

}


?>