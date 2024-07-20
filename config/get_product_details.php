<?php
// Include your database connection configuration
include('config.php');

// Function to get all products
function getAllProducts() {
    global $conn;

    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $products = array();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    } else {
        return null;
    }
}

// Get all products
$allProducts = getAllProducts();
?>
