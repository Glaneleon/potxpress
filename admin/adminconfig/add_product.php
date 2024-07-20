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
    $imagevalidationError = null;

    if (!empty($_FILES['productImage']['name'])) {
        // Generate a unique filename based on time
        $uniqueFilename = md5(uniqid(time(), true));
        $targetDirectory = "../../assets/images/";  // Change this to your desired directory
        $imageDirectory = "./assets/images/";
        $imageFileType = strtolower(pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION));
        $targetFile = $targetDirectory . $uniqueFilename . "." . $imageFileType;
        $fileName = $imageDirectory . $uniqueFilename . "." . $imageFileType;
        $uploadOk = 1;

        // Check if image file is a actual image or fake image
        if (getimagesize($_FILES['productImage']['tmp_name']) === false) {
            $imagevalidationError = "File is not a valid image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($targetFile)) {
            $imagevalidationError = "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES['productImage']['size'] > 5000000) {
            $imagevalidationError = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowedExtensions)) {
            $imagevalidationError = "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            returnError($imagevalidationError);
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFile)) {
                // Set $productImage to the file path
                $productImage = $fileName;
            } else {
                $uploadError = "Sorry, there was an error uploading your file.";
                error_log("[Upload Error] $uploadError");
                returnError($uploadError);
            }
        }
    } else {
        // No image provided, use default value
        $productImage = "./assets/images/default.png";
    }

    // Sanitize and validate inputs
    $productName = sanitizeInput($_POST['productName']);
    $productCategory = sanitizeInput($_POST['productCategory']);
    $productDescription = sanitizeInput($_POST['productDescription']);
    $productPrice = sanitizeInput($_POST['productPrice']);
    $stockQuantity = sanitizeInput($_POST['stockQuantity']);

    // Additional validation checks if needed
    if (!isValidInput($productName) || !isValidInput($productCategory) || !isValidInput($productDescription) || !isValidInput($productPrice) || !isValidInput($stockQuantity)) {
        // Handle validation error, redirect, or show an error message
        $validationError = "Invalid input data";
        returnError($validationError);
    }
    
    // Use prepared statement to prevent SQL injection
    $insertProductQuery = "INSERT INTO products (name, category, description, imagefilepath, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertProductQuery);

    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param("ssssdi", $productName, $productCategory, $productDescription, $productImage, $productPrice, $stockQuantity);
        $stmt->execute();

        // Check if the insertion was successful
        if ($stmt->affected_rows > 0) {
            $productId = $stmt->insert_id;
            // Make an add product log on success
            $insertProductAddLogQuery = "INSERT INTO product_add_log (product_id, product_name, added_by_user_id, added_at, ip_address) VALUES (?, ?, ?, NOW(), ?)";
            $stmtAddLog = $conn->prepare($insertProductAddLogQuery);

            if ($stmtAddLog) {
                $stmtAddLog->bind_param("isis", $productId, $productName, $current_user, $ipAddress);
                $stmtAddLog->execute();
            }

            // Check if the add log insertion was successful
            if ($stmtAddLog->affected_rows > 0) {
            // Return a JSON response
            returnSuccess($productName);
            }
        } else {
            // Error in adding product
            $insertError = "Error: " . $stmt->error;
            error_log($insertError);
            returnError($insertError);
        }
    } else {
        // Invalid request method
        $invalidMethodError = "Invalid request methods";
        error_log($invalidMethodError);
        returnError($invalidMethodError);
    }

    // Close the statement
    $stmt->close();
} else {
    // Invalid request method
    returnError("Invalid request method");
}
?>
