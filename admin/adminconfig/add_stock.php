<?php
session_start();
include_once '../../config/config.php';
$current_user = $_SESSION['user_id'];
$ipAddress = $_SERVER['REMOTE_ADDR'];

if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
    $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
function sanitizeInput($input){
    return htmlspecialchars(trim($input));
}
function returnSuccess($data) {
    header('Content-Type: application/json');
    $response = [
        'success' => true,
        'data' => $data,
    ];
    echo json_encode($response);
    exit();
}
function sendErrorResponse($error){
    header('Content-Type: application/json');
    $response = [
        'success' => false,
        'error' => $error,
    ];
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID = sanitizeInput($_POST['addStockProductID']);
    $quantity = sanitizeInput($_POST['stock_quantity']);

    if (!is_numeric($productID) || !is_numeric($quantity) || $quantity < 1) {
        sendErrorResponse('Invalid input data.');
    }

    mysqli_begin_transaction($conn);

    try {
        $updateProductQuery = "UPDATE products SET stock_quantity = stock_quantity + $quantity WHERE product_id = $productID";
        mysqli_query($conn, $updateProductQuery);

        $insertLogQuery = "INSERT INTO inventory_log (product_id, quantity, datetime, user_id, ip_address) VALUES ($productID, $quantity, NOW(), '$current_user', '$ipAddress')";
        mysqli_query($conn, $insertLogQuery);

        mysqli_commit($conn);
        $addedStock = 'Added '.$quantity.' of Product #'.$productID;
        returnSuccess($addedStock);
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);

        sendErrorResponse("Exception in add_stock.php: " . $e->getMessage().". SQL: ".$insertLogQuery);
    } finally {
        mysqli_close($conn);
    }
} else {
    sendErrorResponse('Invalid request method.');
}
?>
