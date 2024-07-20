<?php
session_start();
include('./config.php');
$current_user = $_SESSION['user_id'];

// Ensure the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the product ID from the POST data
    $productID = isset($_POST['productID']) ? $_POST['productID'] : null;

    // Check if the product ID is valid
    if ($productID !== null) {
        // Use prepared statement to delete the product
        $deleteProductQuery = "DELETE FROM cart WHERE product_id = ? AND user_id = ?";
        $stmt = $conn->prepare($deleteProductQuery);

        if ($stmt) {
            // Bind parameters and execute the statement
            $stmt->bind_param("ii", $productID, $current_user);
            $stmt->execute();

            // Check if the deletion was successful
            if ($stmt->affected_rows > 0) {
                // Return a JSON response indicating success
                echo json_encode(['success' => true, 'data' => 'Product removed successfully.']);
            } else {
                // Error in deleting product
                echo json_encode(['success' => false, 'error' => 'Error: Product not found or deletion failed.']);
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error in preparing the statement
            echo json_encode(['success' => false, 'error' => 'Error: Unable to prepare statement.']);
        }
    } else {
        // Invalid product ID
        echo json_encode(['success' => false, 'error' => 'Error: Invalid product ID.']);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'error' => 'Error: Invalid request method.']);
}
?>
