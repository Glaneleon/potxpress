<?php
session_start();
include('../../config/config.php');
$current_user = $_SESSION['user_id'];
$ipAddress = $_SERVER['REMOTE_ADDR'];

function returnSuccess($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
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

function isValidInput($input) {
    return !empty($input);
}

function getCategoryDetails($categoryId) {
    global $conn;

    $sql = "SELECT * FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validationError = null;

    // Check if a product ID is provided for editing
    if (isset($_POST['editCategoryID'])) {
        // Editing existing product logic here
        $categoryId = sanitizeInput($_POST['editCategoryID']);

        // Check if the product exists
        $existingCategory = getCategoryDetails($categoryId);

        if (!$existingCategory) {
            returnError(['success' => false, 'data' => 'Category does not exist.']);
        }

        // Update fields with the original value if the corresponding form field is empty
        $categoryName = (!empty($_POST['categoryName'])) ? sanitizeInput($_POST['categoryName']) : $existingCategory['category_name'];
       

        // Additional validation checks if needed
        if (!isValidInput($categoryName) ) {
            // Handle validation error, redirect, or show an error message
            $validationError = "Invalid input data";
            error_log($validationError);
            returnError(['success' => false, 'data' => $validationError]);
        }

        // Use prepared statement to prevent SQL injection
        $editCategoryQuery = "UPDATE category SET category_name = ? WHERE category_id = ?";
        $stmt = $conn->prepare($editCategoryQuery);
        $stmt->bind_param("si", $categoryName, $categoryId);

        if ($stmt->execute()) {
            returnSuccess(['success' => true, 'data' => 'Category successfully updated.']); 
            
        } else {
            returnError('Error editing category.');

        }
        $stmt->close();
        $conn->close();
        exit();
 } 

}


?>