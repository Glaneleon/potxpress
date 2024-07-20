<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('./config.php');

    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $age = $_POST["age"];
    $address = $_POST["address"];
    $email = $_POST["email"];
    $mobileNumber = $_POST["mobile_number"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (fname, lname, age, address, email, mobile_number, username, password)
            VALUES ('$fname', '$lname', '$age', '$address', '$email', '$mobileNumber', '$username', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        $response = array("status" => "success", "message" => "Signup successful. Please login.");
        echo json_encode($response);
        exit();
    } else {
        $response = array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error);
        echo json_encode($response);
        exit();
    }

    $conn->close();
}
?>
