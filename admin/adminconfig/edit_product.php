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

function getProductDetails($productId) {
    global $conn;

    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
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
    if (isset($_POST['editProductID'])) {
        // Editing existing product logic here
        $productId = sanitizeInput($_POST['editProductID']);

        // Check if the product exists
        $existingProduct = getProductDetails($productId);

        if (!$existingProduct) {
            returnError(['success' => false, 'data' => 'Product does not exist.']);
        }

        // Update fields with the original value if the corresponding form field is empty
        $productName = (!empty($_POST['productName'])) ? sanitizeInput($_POST['productName']) : $existingProduct['name'];
        $productDescription = (!empty($_POST['productDescription'])) ? sanitizeInput($_POST['productDescription']) : $existingProduct['description'];
        $productPrice = (!empty($_POST['productPrice'])) ? sanitizeInput($_POST['productPrice']) : $existingProduct['price'];
        $stockQuantity = (!empty($_POST['stockQuantity'])) ? sanitizeInput($_POST['stockQuantity']) : $existingProduct['stock_quantity'];

        // Additional validation checks if needed
        if (!isValidInput($productName) || !isValidInput($productDescription) || !isValidInput($productPrice) || !isValidInput($stockQuantity)) {
            // Handle validation error, redirect, or show an error message
            $validationError = "Invalid input data";
            error_log($validationError);
            returnError(['success' => false, 'data' => $validationError]);
        }

        // Use prepared statement to prevent SQL injection
        $editProductQuery = "UPDATE products SET name = ?, description = ?, imagefilepath = ?, price = ?, stock_quantity = ? WHERE product_id = ?";
        $stmt = $conn->prepare($editProductQuery);

        if ($stmt) {
            // Check if image is provided for updating
            if (!empty($_FILES['productImage']['name'])) {
                // Generate a unique filename based on time
                $uniqueFilename = md5(uniqid(time(), true));
                $targetDirectory = "../../assets/images/";  // Change this to your desired directory
                $indexDirectory = "./assets/images/";
                $imageFileType = strtolower(pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION));
                $targetFile = $targetDirectory . $uniqueFilename . "." . $imageFileType;
                $filename = $indexDirectory . $uniqueFilename . "." . $imageFileType;
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
                if ($_FILES['productImage']['size'] > 500000) {
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
                        $productImage = $filename;
                    } else {
                        $uploadError = "Sorry, there was an error uploading your file.";
                        error_log("[Upload Error] $uploadError");
                        returnError($uploadError);
                    }
                }
            } else {
                // if no image provided, use the original value
                $productImage = $existingProduct['imagefilepath'];
            }

            // Bind parameters and execute the statement
            $stmt->bind_param("sssdis", $productName, $productDescription, $productImage, $productPrice, $stockQuantity, $productId);
            $stmt->execute();

            $editquery = 'UPDATE products SET name = '.$productName.', description = '.$productDescription.', imagefilepath = '.$productImage.', price = '.$productPrice.', stock_quantity = '.$stockQuantity.' WHERE product_id = '.$productId;
            // Check if the update was successful
            if ($stmt->affected_rows > 0) {

                // Make an add product log on success
                $insertProductEditLogQuery = "INSERT INTO product_edit_log (product_id, edited_by_user_id, edit_description, edited_at, ip_address) VALUES (?, ?, ?, NOW(), ?)";
                $stmtEditLog = $conn->prepare($insertProductEditLogQuery);

                if ($stmtEditLog) {
                    $stmtEditLog->bind_param("iiss", $productId, $current_user, $editquery, $ipAddress);
                    $stmtEditLog->execute();
                }

                // Check if the add log insertion was successful
                if ($stmtEditLog->affected_rows > 0)

                $productUpdated = "Product $productId was edited successfully.";
                // Return a JSON response
                returnSuccess(['success' => true, 'data' => $productUpdated]);
            } else {
                // Error in updating product
                $updateError = "Error: " . $stmt->error;
                error_log($updateError);
                returnError(['success' => false, 'data' => $updateError]);
            }

            // Close the statement
            $stmt->close();
        } else {
            // Invalid request method or SQL error
            $invalidMethodError = "Invalid request method or SQL error";
            error_log($invalidMethodError);
            returnError(['success' => false, 'data' => $invalidMethodError]);
        }
    } else {
        echo "Product not found.";
        exit();
    }
} else {
    echo "Invalid request method";
    exit();
}
?>
