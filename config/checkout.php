<?php
session_start();
include('./config.php');

function returnSuccess($data) {
    include('./config.php');
    header('Content-Type: application/json');
    $response = [
        'success' => true,
        'message' => $data,
    ];
    echo json_encode($response);
    $conn->close();
    exit();
}

function returnError($errorMessage) {
    include('./config.php');
    header('Content-Type: application/json');
    echo json_encode(['error' => $errorMessage]);
    $conn->close();
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user_id from the session
    $user_id = $_SESSION['user_id'];

    // Get the selected products from the POST data or set it to an empty array
    $selected_products = $_POST['selected_products'] ?? [];

    // Check if there are selected products
    if (!empty($selected_products)) {
        try {
            // Begin a database transaction
            $conn->begin_transaction();

            // Fetch cart items using a prepared statement to prevent SQL injection
            $sql = "SELECT products.*, cart.quantity FROM cart 
            JOIN products ON cart.product_id = products.product_id 
            WHERE cart.user_id = ? AND cart.product_id IN (" . implode(',', $selected_products) . ")";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if there are cart items
            if ($result->num_rows > 0) {
                // Initialize total price
                $total_price = 0;

                // Iterate through cart items
                while ($row = $result->fetch_assoc()) {
                    $total_price += $row['quantity'] * $row['price'];
                }

                // Insert a new order record
                $order_sql = "INSERT INTO orders (user_id, order_date, total_amount) VALUES (?, NOW(), ?)";
                $order_stmt = $conn->prepare($order_sql);
                $order_stmt->bind_param('id', $user_id, $total_price);
                $order_stmt->execute();

                // Get the last inserted order ID
                $last_order_id = $order_stmt->insert_id;

                // Insert a new order_status record
                $order_status_sql = "INSERT INTO order_status (order_id, order_placed) VALUES (?, NOW())";
                $order_status_stmt = $conn->prepare($order_status_sql);
                $order_status_stmt->bind_param('i', $last_order_id);
                $order_status_stmt->execute();

                // Reset the result set
                $result->data_seek(0);

                // Iterate through cart items to insert order details and update product stock
                while ($row = $result->fetch_assoc()) {
                    $quantity = $row['quantity'];
                    $product_id = $row['product_id'];
                    $price = $row['price'];

                    // Insert order details
                    $order_details_sql = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                    $order_details_stmt = $conn->prepare($order_details_sql);
                    $order_details_stmt->bind_param('iiid', $last_order_id, $product_id, $quantity, $price);
                    $order_details_stmt->execute();

                    // Update product stock quantity
                    // Fetch current stock_quantity
                    $select_quantity_sql = "SELECT stock_quantity FROM products WHERE product_id = ?";
                    $select_quantity_stmt = $conn->prepare($select_quantity_sql);
                    $select_quantity_stmt->bind_param('i', $product_id);
                    $select_quantity_stmt->execute();
                    $select_quantity_stmt->bind_result($current_stock_quantity);
                    $select_quantity_stmt->fetch();
                    $select_quantity_stmt->close();

                    // Check if stock_quantity is sufficient
                    if ($current_stock_quantity < $quantity) {
                        // Return an error or handle the insufficient stock scenario
                        header('Content-Type: application/json');
                        $err = 'Sorry we cannot proceed with this transaction. Product #'.$product_id.' only has '.$current_stock_quantity.' piece(s) available while you requested to buy '.$quantity;
                        returnError($err);
                        exit();
                    } else {
                        // Update stock_quantity
                        $update_product_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
                        $update_product_stmt = $conn->prepare($update_product_sql);
                        $update_product_stmt->bind_param('ii', $quantity, $product_id);

                        // Execute the update only if stock_quantity is sufficient
                        // Get ready to delete cart items for the user only if it is in the selected products and update was successful
                        $delete_cart_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
                        $delete_cart_stmt = $conn->prepare($delete_cart_sql);

                        if ($update_product_stmt->execute()) {
    
                            // Bind parameters and execute deletion if update is successful
                            $delete_cart_stmt->bind_param('ii', $user_id, $product_id);
                            $delete_cart_stmt->execute();
                        } else {
                            $err = 'Cannot clean cart.';
                            returnError($err);
                            
                            exit();
                        }

                        // Close the prepared statement
                        $update_product_stmt->close();
                        $delete_cart_stmt->close();
                    }
                }

                // Commit the transaction if everything is successful
                $conn->commit();
                $msg = 'Purchase successful';
                returnSuccess($msg);
                
                exit();
            } else {
                $err = 'There was an error in purchasing.';
                returnError($err);
                
                exit();
            }
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $err = 'Error: ' . $e->getMessage();
            returnError($err);
            
            exit();
        }
    } else {
        $err = 'No item selected.';
        returnError($err);
        
        exit();
    }
} else {
    
    exit();
}

?>