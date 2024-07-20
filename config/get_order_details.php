<?php
session_start();
include 'config.php';

function getOrdersById($orderId) {
    global $conn;

    // Check if the connection is established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Sanitize and validate the orderId value
    $orderId = mysqli_real_escape_string($conn, $orderId);
    $user_id = $_SESSION['user_id'];

    // Check if the user_id is set and valid
    if (!isset($user_id) || empty($user_id)) {
        die("Invalid user ID");
    }

    $sql = "SELECT orders.*, order_details.*, products.*, order_status.order_placed, order_status.in_transit, order_status.delivered
            FROM orders 
            JOIN order_details ON orders.order_id = order_details.order_id 
            JOIN products ON order_details.product_id = products.product_id 
            LEFT JOIN order_status ON orders.order_id = order_status.order_id
            WHERE orders.order_id = $orderId
            AND orders.user_id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $orderdetails = array();
        while ($row = $result->fetch_assoc()) {
            $orderdetails[] = $row;
        }
        return $orderdetails;
    } else {
        return null;
    }
}


// Retrieve the order ID from the POST parameters
$orderId = $_POST['orderId'];

// Call the function with the $orderId variable
$orderDetails = getOrdersById($orderId);

// Check if the orderDetails variable is set and not empty
if (isset($orderDetails) && !empty($orderDetails)) {
    // Output the orderDetails variable
    echo json_encode($orderDetails);
} else {
    // Output an error message
    echo "No order details found";
}
?>
