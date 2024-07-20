<?php
session_start();
include('../../config/config.php');
$current_user = $_SESSION['user_id'];
$ipAddress = $_SERVER['REMOTE_ADDR'];

function returnSuccess($data) {
    header('Content-Type: application/json');
    $responseData = ['success' => true, 'data' => true, 'originalString' => $data];
    echo json_encode($responseData);
    exit();
}

function returnError($errorMessage) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $errorMessage]);
    exit();
}

function sanitizeInput($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

// Function to validate the input
function isValidInput($input) {
    // You can add custom validation rules here
    return !empty($input);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validationError = null;
    $categoryName = sanitizeInput($_POST['categoryName']);

      // Additional validation checks if needed
      if (!isValidInput($categoryName) ) {
        // Handle validation error, redirect, or show an error message
        $validationError = "Invalid input data";
        returnError($validationError);
    }
    $insertCategoryQuery = "INSERT INTO category (category_name) VALUES (?)";
    $stmt = $conn->prepare($insertCategoryQuery);

    
    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param("s", $categoryName);
        $stmt->execute();

        returnSuccess($categoryName);
      
   
    } else {
        // Invalid request method
        $invalidMethodError = "Invalid request methods";
        error_log($invalidMethodError);
        returnError($invalidMethodError);
    }
    
    $stmt->close();



} else{
     // Invalid request method
     returnError("Invalid request method");
}



?>