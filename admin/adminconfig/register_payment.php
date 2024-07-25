<?php
include('../../config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $payment_method = $_POST['mode'];
    $payment_img = "./assets/payment/cod.png";
    $orderID = $_POST['orderID'];

    $sql = "UPDATE orders SET payment_img = ?, payment_mode = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error preparing statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param('sss', $payment_img, $payment_method, $orderID);

    if (!$stmt->execute()) {
        echo json_encode(['error' => 'Error executing query: ' . $stmt->error]);
        exit();
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => 'Payment registered successfully.']);
    } else {
        echo json_encode(['error' => 'No changes were made.']);
    }

    $stmt->close();
    $conn->close();
}
