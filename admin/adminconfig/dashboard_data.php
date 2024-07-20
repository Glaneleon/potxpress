<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "potxpress";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT COUNT(*) AS OutOfStock FROM `products` WHERE `stock_quantity` = 0";
$sql1 = "SELECT COUNT(*) AS LowStock FROM `products` WHERE `stock_quantity` > 0 AND `stock_quantity` < 10";
$sql2 = "SELECT SUM(`total_amount`) AS Sales FROM `orders`";
$sql3 = "SELECT COUNT(*) AS Products FROM `products`";

$outOfStock = "";
$lowStock = "";
$sales = "";
$products = "";

$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $outOfStock = $row["OutOfStock"];
}
mysqli_free_result($result);

$result = mysqli_query($conn, $sql1);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $lowStock = $row["LowStock"];
}
mysqli_free_result($result);

$result = mysqli_query($conn, $sql2);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $sales = $row["Sales"];
}
mysqli_free_result($result);

$result = mysqli_query($conn, $sql3);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $products = $row["Products"];
}
mysqli_free_result($result);

mysqli_close($conn);
