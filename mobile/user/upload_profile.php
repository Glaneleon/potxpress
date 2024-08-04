<?php
include '../../config/config.php';

$user_id = $_POST['user_id'];
$image = $_FILES['image']['name'];


$uniqueFilename = md5(uniqid(time(), true));
$targetDirectory = "../../assets/profile_img/";  
$imageDirectory = "./assets/profile_img/";
$imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
$targetFile = $targetDirectory . $uniqueFilename . "." . $imageFileType;
$fileName = $imageDirectory . $uniqueFilename . "." . $imageFileType;

$tmp_image = $_FILES['image']['tmp_name'];
move_uploaded_file($tmp_image, $targetFile);

$profile_image = $fileName;

$sqlQuery = "UPDATE uers_test SET profile_img = '".$profile_image."' WHERE user_id = '".$user_id."' ";
$resultOfQuery = $conn->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("success" => true, "image_name" => $profile_image));
}
else{
    echo json_encode(array("success" => false));
}




?>