<?php
    // Update the path below to your autoload.php,
    // see https://getcomposer.org/doc/01-basic-usage.md
    require_once 'c:/xampp/htdocs/vendor/autoload.php';
    include_once 'c:/xampp/htdocs/admin/adminconfig/otp/config.php';

    use Twilio\Rest\Client;

    $sid = $accountSID;
    $token = $authToken;
    $twilio = new Client($sid, $token);

    $message = $twilio->messages
      ->create("+639304044237", // to
        array(
          "from" => "+19173365352",
          "body" => 'testing sms'
        )
      );

print($message->sid);