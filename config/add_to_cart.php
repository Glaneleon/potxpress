<?php

// Function to add a product to the cart
function addToCart($user_id, $product_id, $quantity) {
    global $conn;

    // Check if the product is already in the cart
    $checkCart = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
    $checkResult = $conn->query($checkCart);

    if ($checkResult->num_rows > 0) {
        // If the product is already in the cart, update the quantity
        $updateCart = "UPDATE cart SET quantity = quantity + '$quantity' WHERE user_id = '$user_id' AND product_id = '$product_id'";
        $conn->query($updateCart);
    } else {
        // If the product is not in the cart, insert a new record
        $insertCart = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
        $conn->query($insertCart);
    }

}

// Handle "Buy Now" button click
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["buy_now"])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $product_id = $productDetails['product_id'];
        $quantity = 1; // You can customize the quantity if needed

        // Add the product to the cart
        addToCart($user_id, $product_id, $quantity);

        // Redirect to the cart page
        header("Location: ../cart.php");
        exit();
    } else {
        // Redirect to the login page if the user is not logged in
        header("Location: ../login.php");
        exit();
    }
}
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $product_id = $productDetails['product_id'];
        $quantity = $_POST["quantity"];

        // Add the product to the cart
        addToCart($user_id, $product_id, $quantity);
        $_SESSION['notification'] = array(
            'type' => 'Success',
            'message' => "Successfully added $quantity $productName to your cart!"
        );
        header("Location: #");
        exit();
    } else {
        // Redirect to the login page if the user is not logged in
        header("Location: ../login.php");
        exit();
    }
}
?>