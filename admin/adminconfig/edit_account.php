<?php

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

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Include your database connection here
    include('../../config/config.php');// Adjust this based on your actual file structure

    // Validate and sanitize form data
    $accountID = $_POST['editAccountID'];
    $firstName = htmlspecialchars($_POST['accountFName']);
    $lastName = htmlspecialchars($_POST['accountLName']);
    $role = $_POST['accountRole']; // Ensure proper validation based on expected values
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $mobileNumber = $_POST['mobile_number']; // You might want to validate phone number format
    $username = htmlspecialchars($_POST['accountusername']);

    // Prepare SQL statement using prepared statement
    $sql = "UPDATE users SET 
            fname = ?,
            lname = ?,
            email = ?,
            role = ?,
            mobile_number = ?,
            username = ?
            WHERE user_id = ?"; 

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $firstName, $lastName, $email, $role, $mobileNumber, $username, $accountID);

    // Execute SQL statement
    if ($stmt->execute()) {
        returnSuccess(['success' => true, 'data' => 'Account successfully updated.']);
    } else {
        returnError('Error editing account.');
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
    exit();
}
