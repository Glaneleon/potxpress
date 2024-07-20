<?php
session_start();
// Include your database connection configuration
include('config/config.php');

// Function to get product details by product ID
function getProductDetails($productId) {
    global $conn;

    $productId = $conn->real_escape_string($productId); // Sanitize input to prevent SQL injection

    $sql = "SELECT * FROM products WHERE product_id = $productId";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Check if a product ID is provided in the URL
if (isset($_GET['id'])) {
    $productToDisplay = $_GET['id'];
    $productDetails = getProductDetails($productToDisplay);
} else {
    // Redirect or display an error message if no product ID is provided
    header("Location: index.php");
    exit();
}

include './config/add_to_cart.php';

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $productDetails['name'] ?? 'Product Details'; ?></title>
    <link rel="stylesheet" href="assets/styles/productstyles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;700&display=swap">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>
<body>
    <?php include 'config/header.php'; ?>

    <!-- Product details container -->
    <div class="d-flex flex-row justify-content-center">
        <?php if ($productDetails) : ?>
            <div class="d-flex w-75 p-5 justify-content-center">
                <div class="d-flex p-2 col-md-6 justify-content-center border">
                    <img class="product-image" src="<?php echo $productDetails['imagefilepath']; ?>" alt="<?php echo $productDetails['name']; ?>">
                </div>
                <div class="d-flex px-5 flex-column col-md-6">
                    <h3 class="p-2"><?php echo $productDetails['name']; ?></h3>
                    <p class="p-2"><?php echo $productDetails['description']; ?></p>
                    <p class="p-2">₱<?php echo number_format($productDetails['price'], 2); ?></p>
                    <p class="p-2">Current Stock: <?php echo $productDetails['stock_quantity']; ?></p>

                    <!-- Quantity selector added here -->
                    <form action="product.php?id=<?php echo $productDetails['product_id']; ?>" method="post">
                        <div class="p-2">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $productDetails['stock_quantity']; ?>">
                        </div>
                        <div class="p-2">
                            <?php if($productDetails['stock_quantity'] == 0){
                                echo "<p class='text-secondary'>Currently out of stock.</p>";
                            } else{
                                echo '<button type="submit" name="buy_now" class="buy-now-button me-3">Buy Now</button>';
                                echo '<button type="submit" name="add_to_cart" class="add-to-cart-button"><i class="fas fa-cart-plus"></i> Add to Cart</button>';
                            }
                            ?>
                            
                        </div>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <p>Product not found.</p>
        <?php endif; ?>
    </div>

    <?php include 'config/footer.php'; ?>

    <script>
        // Check if there are any notifications to show
        <?php
        if (isset($_SESSION['notification'])) {
            $notification = $_SESSION['notification'];

            echo "toastr.success('{$notification['message']}', '{$notification['type']}');";

            // Clear the session variable after displaying the notification
            unset($_SESSION['notification']);
        }
        ?>
    </script>
</body>
</html>
