<?php
// etong file na to siya yung nag sesendng otp
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
include '../../config/config.php';
date_default_timezone_set('Asia/Manila');

$user_id = $_POST['user_id'];
$user_email = $_POST['user_email'];

// $user_id = 35;
// $user_email = 'zoilojun38@gmail.com';

$mail = new PHPMailer(true);
try{
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
    // $mail->addAddress($user_email);  

     $otpCode = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

    // //Content
    // $mail->isHTML(true);  
    // $mail->AddEmbeddedImage("../../assets/logo/pot_supplier_logo.jpg", "companyLogo", "pot_supplier_logo.png");                                  //Set email format to HTML
    // $mail->Subject = 'OTP Verification';
    // $mail->Body    = '<!DOCTYPE html>
    // <html lang="en">
    // <head>
    //     <meta charset="UTF-8">
    //     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    //     <title>Document</title>
    //     <style>
    //     body{
            
    //         font-family: Tahoma, sans-serif;
    //     }
    //     .main-container{
    //         margin: auto;
    //         width: 40%;
    //         background-color: rgba(248,248,248,255);
            
    //     }
    //     .body-container{
    //         background-color: white;
    //         padding: 25px 25px 75px 25px;
    
    //     }
    //     p{
    //         line-height: 1.8;
    //         text-align: justify;
    //     }
    //     .welcome-title{
    //         padding-top: 15px;
    
    //         padding-bottom: 10px;
    //         text-align: center;
    //     }
    //     .welcome-title img{
    //         width: 35px;
    //         height: 35px;
    //         border-radius: 2px;
    //     }
        
    //     .verification-code{
    //         font-size: 40px;
    //         font-weight: 700;
    //         text-align: center;
    //         padding-top: 15px;
    //         padding-bottom: 15px;
    //         color: black;
    //         margin: 0;
            
    //     }
    //     .verification-container{
    //         background-color: rgb(255, 213, 128);
    //         margin: 0;
            
    //     }
    //     .timer-note{
    //         text-align: center;
    //         font-size: 11px;
    //         margin: 0;
    //         color: rgb(85, 84, 84);
    //         font-weight: 600;
    //     }
    //     .title-welcome{
    //        padding: 25px 15px 10px 15px;
    //        text-align: center;
    //     }
    //     .space-btw-sections{
    //         height: 25px;
    //     }
    //     .footer p{
    //         text-align: center;
    //         margin: 0;
    //         font-size: 14px;

    //     }
    //     .footer{
    //         background-color: rgba(248,248,248,255);
    //     }
    //     .contact-section p{
    //         text-align: center;
    //         margin: 0;
    //     }
    //     .company-address{
    //         padding-top: 15px;
    //         padding-bottom: 20px;
    //         text-align:center;
            
    //     }
    //     .contact-no{
    //         color: rgb(136,136,136,255);
    //         font-weight: 700;
    //     }
    //     h1{
    //         display: inline-block;
    //         text-align: center;
    //     }
    //     img{
    //         display: inline-block;
            
    //     }
    //     .space-horizontal{
    //         display: inline-block;
    //         width: 1px;
    //     }
    //     .footer-company img{
    //         height: 30px;
    //         width: 30px;
    //         border-radius: 2px;
    //     }
    //     .footer-company{
    //         display: flex;
    //         justify-content: center;
            
    //     }
    //     .footer-company p{
    //         margin-left: 5px;
    //     }
        

    // </style>
    // </head>
    // <body>
    //     <div class="main-container">
    //         <!-- mail logo -->
    //         <div class="welcome-title">
    //             <img src="cid:companyLogo">
    //             <div class="space-horizontal"></div>
    //             <h1>Pot Supplier Manila</h1>  
    //         </div>
    //         <!-- body -->
    //         <div class="body-container">
    //             <div class="title-welcome">
    //                 <h1>Verify your login</h1>
    //                 <p>For your security, we\'re verifying your identity. Here\'s your one-time code (OTP) to proceed.</p>
    //             </div>
                
                
    //             <div class="verification-container">
    //                 <!-- verification code -->
    //                 <p class="verification-code">' .$otpCode.'</p>
    
    //             </div>
    //                 <!-- Timer of validation -->
    //                 <p class="timer-note">This code only lasts for 5 minutes.</p>
    
    //                 <div class="space-btw-sections">
    //                 </div>
                
    //             <div class="contact-section">
    //                 <p>Have a question or trouble logging in? Please contact us here</p>
    //             </div>
    
    //             <div class="space-btw-sections">
                    
    //             </div>
    
    //             <!-- footer -->
    //             <div class="footer">
    //                 <!-- logo -->
    
    //                 <!-- Company address -->
    //                 <div class="company-address">
    //                     <div class="footer-company" style="justify-content: center;">
    //                         <img src="cid:companyLogo">
    //                         <p>Pot Supplier Manila</p>
    //                     </div>
                        
    //                     <p>39 Doña Hemady St., New Manila, QC</p>
    //                     <p class="contact-no">Call us - 09153033690</p>
    //                 </div>
                    
    
    //                 <!-- Contact number -->
    //             </div>
    
    //         </div>
    //     </div>
    // </body>
    // </html>';
    
    // $mail->send();
    
    $current_time = date("Y-m-d H:i:s");
    $expiredDate = date("y-m-d H:i:s", strtotime(' +5 minutes', strtotime($current_time)));

    $insertOTPQuery = "INSERT INTO users_otp (user_id, otp_code, expired_at) VALUES ('".$user_id."', '".$otpCode."', '".$expiredDate."')";
    $resultOfQuery = $conn->query($insertOTPQuery);

    // $sqlQuery = "UPDATE uers_test SET otp_code = '".$otpCode."' WHERE user_id = '".$user_id."' ";

    if($resultOfQuery){
        echo json_encode(array("success"=>true));
    }
    else{
        echo json_encode(array("success"=>false));
    }  
   

}catch(Exception $e){
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

}



?>