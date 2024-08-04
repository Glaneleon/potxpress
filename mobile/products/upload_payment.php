<?php
include '../../config/config.php';
$user_id = $_POST['user_id'];
$order_id = $_POST['order_id'];
$image = $_FILES['image']['name'];

$uniqueFilename = md5(uniqid(time(), true));
$targetDirectory = "../../assets/payment/";  
$imageDirectory = "./assets/payment/";
$imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
$targetFile = $targetDirectory . $uniqueFilename . "." . $imageFileType;
$fileName = $imageDirectory . $uniqueFilename . "." . $imageFileType;


$tmp_image = $_FILES['image']['tmp_name'];
move_uploaded_file($tmp_image, $targetFile);

$payment_img = $fileName;

$sqlQuery = "UPDATE orders SET payment_img = '".$payment_img."' WHERE user_id = '".$user_id."' AND order_id = '".$order_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true,));
}
else{
    echo json_encode(array("success" => false));
}
?>