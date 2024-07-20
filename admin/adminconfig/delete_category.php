<?php
session_start();
include('../../config/config.php');
$current_user = $_SESSION['user_id'];
$ipAddress = $_SERVER['REMOTE_ADDR'];

// Ensure the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the product ID from the POST data
    $categoryID = isset($_POST['categoryID']) ? $_POST['categoryID'] : null;

    // Check if the product ID is valid
    if ($categoryID !== null) {
        // Use prepared statement to delete the product
        $deleteCategoryQuery = "DELETE FROM category WHERE category_id = ?";
        $stmt = $conn->prepare($deleteCategoryQuery);

        if ($stmt) {
            // Bind parameters and execute the statement
            $stmt->bind_param("i", $categoryID);
            $stmt->execute();

            // Check if the deletion was successful
            if ($stmt->affected_rows > 0) {
                // Make an add product log on success
                
                echo json_encode(['success' => true, 'data' => 'Category deleted successfully.']);
            } else {
                // Error in deleting product
                echo json_encode(['success' => false, 'error' => 'Error: Category not found or deletion failed.']);
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error in preparing the statement
            echo json_encode(['success' => false, 'error' => 'Error: Unable to prepare statement.']);
        }
    } else {
        // Invalid product ID
        echo json_encode(['success' => false, 'error' => 'Error: Invalid Category ID.']);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'error' => 'Error: Invalid request method.']);
}
?>
