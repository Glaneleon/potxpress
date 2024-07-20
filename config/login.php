<?php
session_start();
require_once("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION["username"] = $username;
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: ../index.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
}

$conn->close();
?>
