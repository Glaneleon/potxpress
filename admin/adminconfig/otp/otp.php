<?php
require_once 'c:/xampp/htdocs/vendor/autoload.php';
include_once 'c:/xampp/htdocs/admin/adminconfig/otp/config.php';

use Twilio\Rest\Client;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sid = $accountSID;
    $token = $authToken;
    $twilio = new Client($sid, $token);

    $phoneNumber = "+639304044237"; // Adjust this to the submitted phone number from the form

    $verification = $twilio->verify->v2->services($serviceSID)
                                       ->verifications
                                       ->create($phoneNumber, "sms");

    print($verification->status);

    if ($verification->status === 'pending') {
        // Redirect user to verifyotp.php
        header("Location: verifyotp.php");
        exit; // Ensure no further code execution after redirection
    } else {
        print($verification->status);
    }
}