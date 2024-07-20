<?php
session_start();
include('../../config/config.php');
$current_user = $_SESSION['user_id'];
$ipAddress = $_SERVER['REMOTE_ADDR'];

// Ensure the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the product ID from the POST data
    $userID = isset($_POST['userID']) ? $_POST['userID'] : null;

    // Check if the product ID is valid
    if ($userID !== null) {
        // Use prepared statement to delete the product
        $deleteAccountQuery = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($deleteAccountQuery);

        if ($stmt) {
            // Bind parameters and execute the statement
            $stmt->bind_param("i", $userID);
            $stmt->execute();

            // Check if the deletion was successful
            if ($stmt->affected_rows > 0) {
               
                // Return a JSON response indicating success
                echo json_encode(['success' => true, 'data' => 'Account deleted successfully.']);
            } else {
                // Error in deleting product
                echo json_encode(['success' => false, 'error' => 'Error: Account not found or deletion failed.']);
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error in preparing the statement
            echo json_encode(['success' => false, 'error' => 'Error: Unable to prepare statement.']);
        }
    } else {
        // Invalid product ID
        echo json_encode(['success' => false, 'error' => 'Error: Invalid Account ID.']);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'error' => 'Error: Invalid request method.']);
}
?>
