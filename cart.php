<?php
session_start();
include('config/config.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT products.*, cart.quantity FROM cart JOIN products ON cart.product_id = products.product_id WHERE cart.user_id = '$user_id' AND products.stock_quantity != 0";
$result = $conn->query($sql);

$sql = "SELECT products.*, cart.quantity FROM cart JOIN products ON cart.product_id = products.product_id WHERE cart.user_id = '$user_id' AND products.stock_quantity = 0";
$outofstockrows = $conn->query($sql);

// Include the header
include('config/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="icon" href="assets/images/potsuppliermanila.jpg" type="image/icon type">
    <link rel="stylesheet" href="assets/styles/cartstyles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;700&display=swap">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>
<body>

<div class="container">
    <!-- products that are in stock -->
    <div class="row">
        <h2>Your Cart</h2>

        <?php if ($result && $result->num_rows > 0) : ?>
            <form id="checkoutForm">
            <table class="table" id="cartTable">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr data-product-id="<?php echo $row['product_id']; ?>" class="cart-row">
                                <td>
                                <input type="checkbox" name="selected_products[]" class="select-checkbox" value="<?php echo $row['product_id']; ?>"/>
                                </td>
                                <td><img src="<?php echo $row['imagefilepath']; ?>" alt="<?php echo $row['name']; ?>" class="cart-product-image"/>
                                </td>
                                <td><?php echo $row['name'];?></td>
                                <td>₱<?php echo number_format($row['price'], 2); ?></td>
                                <td><input type="number" class="quantity-input" value="<?php echo $row['quantity']; ?>" data-price="<?php echo $row['price']; ?>" readonly /></td>
                                <td><button class="btn btn-danger" id="removeFromCart<?php echo $row['product_id']; ?>">Remove</button></td>
                            </tr>
                    <?php endwhile; ?>
                </tbody>


            </table>
            <button type="submit" class="btn btn-primary d-none" id="buybutton">Buy Now</button>
            </form>
            <div id="real-time-total">Total Price: ₱0.00</div>
        <?php else : ?>
            <p>Your cart is empty.</p>
            <a class="text-warning" href="./index.php">Go Back to Shopping!</a>
        <?php endif; ?>
    </div>    

    <!-- out of stock products in cart -->
    <div class="row my-5">
        <h4>Out of Stock Items</h4>

        <?php if ($outofstockrows && $outofstockrows->num_rows > 0) : ?>
            <table class="table" id="outofstocktable">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($outofstockrow = $outofstockrows->fetch_assoc()) : ?>
                            <tr data-product-id="<?php echo $outofstockrow['product_id']; ?>" class="cart-row">
                                <td></td>
                                <td><img src="<?php echo $outofstockrow['imagefilepath']; ?>" alt="<?php echo $outofstockrow['name']; ?>" class="cart-product-image" style="filter: grayscale(100%);"/>
                            </td>
                                <td><?php echo $outofstockrow['name'];?></td>
                                <td>₱<?php echo number_format($outofstockrow['price'], 2); ?></td>
                                <td>Currently out of stock</td>
                                <td><button class="btn btn-danger" id="removeFromCart<?php echo $outofstockrow['product_id']; ?>">Remove</button></td>
                            </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php 
include './config/footer.php';
?>

<!-- selected total amount -->
<script>
    // Wait for the DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {
        // Get the element where the real-time total will be displayed
        const realTimeTotalElement = document.getElementById('real-time-total');

        // Get the "Buy Now" button element
        const buyNowButton = document.getElementById('buybutton');

        // Attach event listener to the cartTable
        document.getElementById('cartTable').addEventListener('change', function () {
            updateRealTimeTotal();
        });

        // Function to update the real-time total
        function updateRealTimeTotal() {
            let total = 0;

            // Loop through each row of the cartTable
            document.querySelectorAll('#cartTable .cart-row').forEach(function (row) {
                const isSelected = row.querySelector('.select-checkbox').checked;
                const quantity = parseInt(row.querySelector('.quantity-input').value, 10);
                const price = parseFloat(row.querySelector('.quantity-input').dataset.price);

                // Check if the item is selected
                if (isSelected) {
                    total += quantity * price;
                }
            });

            // Update the total amount on the page
            realTimeTotalElement.textContent = 'Total Price: ₱' + total.toFixed(2);

            // Remove the "d-none" class if total is not zero
            buyNowButton.classList.toggle('d-none', total === 0);
        }
    });
</script>
<!-- checkout  -->
<script>
    // Wait for the DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {
        // Get the "Buy Now" button element
        const buyNowButton = document.getElementById('buybutton');
    
        // Attach click event to buy button
        buyNowButton.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default form submission
        
            // Get all select checkboxes within the cartTable
            const selectCheckboxes = document.querySelectorAll('#cartTable .select-checkbox:checked');
        
            // Construct an array of selected product IDs
            const selectedProducts = Array.from(selectCheckboxes).map(checkbox => checkbox.value);
        
            // Create an object with the data to be sent
            const formData = {
                selected_products: selectedProducts
            };
        
            // AJAX request
            $.ajax({
                type: "POST",
                url: "./config/checkout.php",
                data: formData,
                dataType: "json",  // Specify that you expect a JSON response
                success: function (response) {
                    console.log(response);
                    console.log('AJAX success');
                    try {
                        if (response.error) {
                            console.log('Server error');
                            // Handle error with SweetAlert2
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'Error',
                            }).then(function () {
                                console.log('Swal reached error');
                            });
                        } else {
                            console.log('Server success');
                            // Handle success with SweetAlert2
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message || 'Success',
                            }).then(function () {
                                console.log('Swal reached success');
                                // Redirect or perform additional actions
                                window.location.href = "./cart.php";
                            });
                        }
                    } catch (error) {
                        console.error('Error processing AJAX response:', error);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.status); // Log the HTTP status code
                    console.log(textStatus);   // Log the textStatus
                    console.log(errorThrown);   // Log the error message
                    // Handle AJAX error with SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred.'
                    });
                }
            });
        });
    });
</script>
<!-- remove from cart -->
<script>
    $(document).ready(function () {
        // Function to handle deletion process
        function handleDelete(event) {
            event.preventDefault();
            // Extract the product ID from the button ID
            var productID = this.id.replace('removeFromCart', '');
        
            // Show the confirmation dialog
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                timer: 5000,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make an AJAX request to delete_product.php
                    $.ajax({
                        type: 'POST',
                        url: './config/remove_from_cart.php',
                        data: { productID: productID },
                        dataType: 'json',
                        success: function (response) {
                            // Handle the response from the server
                            if (response.success) {
                                // Show success message after deletion
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.data || 'Product was removed from your cart.',
                                    icon: "success"
                                }).then(() => {
                                    // Reload the page after dismissing the success message
                                    window.location.reload();
                                });
                            } else {
                                // Show error message if deletion fails
                                Swal.fire({
                                    title: "Error",
                                    text: response.error || 'Failed to remove the product. Please try again.',
                                    icon: "error"
                                });
                            }
                        },
                        error: function () {
                            // Show error message for AJAX error
                            Swal.fire({
                                title: "Error",
                                text: "An error occurred. Please try again.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }

        // Attach click event to delete buttons for both tables
        $('#cartTable, #outofstocktable').on('click', '[id^="removeFromCart"]', handleDelete);
    });
</script>


</body>
</html>

<?php
// Close the result set
$result->close();
?>