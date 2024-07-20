<?php
session_start();
include 'config.php';

function getAllOrders() {
    global $conn;

    // Check if the connection is established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $user_id = $_SESSION['user_id'];

    // Sanitize and validate the user_id value
    $user_id = mysqli_real_escape_string($conn, $user_id);

    $sql = "SELECT * FROM orders WHERE user_id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    } else {
        return null;
    }
}
?>
