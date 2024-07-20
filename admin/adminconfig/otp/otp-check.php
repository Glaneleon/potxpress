<?php
require_once 'c:/xampp/htdocs/vendor/autoload.php';
include_once 'c:/xampp/htdocs/admin/adminconfig/otp/config.php';

use Twilio\Rest\Client;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Twilio credentials
    $sid = $accountSID;
    $token = $authToken;
    $twilio = new Client($sid, $token);

    // Capture user input (verification code)
    $userEnteredCode = $_POST['verification_code']; // Assuming you're using POST method

    // Phone number to verify
    $phoneNumber = "+639304044237"; // Replace with the user's phone number

    try {
        // Verify the code entered by the user
        $verificationCheck = $twilio->verify->v2->services($serviceSID)
                                               ->verificationChecks
                                               ->create([
                                                            "to" => $phoneNumber,
                                                            "code" => $userEnteredCode
                                                        ]
                                               );

        // Check if verification was successful
        if ($verificationCheck->status === "approved") {
            // Verification successful, proceed with the next step (e.g., grant access)
            echo "Verification successful!";
        } else {
            // Verification failed
            echo "Invalid verification code. Please try again.";
        }
    } catch (Exception $e) {
        // Handle exceptions (e.g., connection errors, API errors)
        echo "Error: " . $e->getMessage();
    }

}