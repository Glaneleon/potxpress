<?php
include './config.php';

$script_tz = date_default_timezone_get();

if (strcmp($script_tz, ini_get('date.timezone'))){
    echo 'Script timezone differs from ini-set timezone.';
} else {
    echo 'Script timezone and ini-set timezone match.';
}

$timezone = date_default_timezone_get();
echo "The current server timezone is: " . $timezone;
$date = date('m/d/Y h:i:s a', time());
echo "Date: " . $date;
?>