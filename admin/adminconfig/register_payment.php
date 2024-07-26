<?php
include('../../config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $payment_method = $_POST['mode'];
    $orderID = $_POST['orderID'];
    $payment = $_POST['amount'];
    if (!is_numeric($payment)) {
        echo json_encode(['success' => false, 'error' => 'Invalid payment amount.']);
        exit;
    }
    $payment = number_format($payment, 2, '.', '');

    $sql = "UPDATE orders SET payment_received = ?, payment_mode = ?";
    $bindParam = "ds";

    if ($payment_method == 'cod') {
        $payment_img = "./assets/payment/cod.png";
        $sql .= ', payment_img = ?';
        $bindParam .= 's';
    }

    $sql .= ' WHERE order_id = ?';
    $bindParam .= 's';

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error preparing statement: ' . $conn->error]);
        exit();
    }

    if (isset($payment_img)) {
        $stmt->bind_param($bindParam, $payment, $payment_method, $payment_img, $orderID);
    } else {
        $stmt->bind_param($bindParam, $payment, $payment_method, $orderID);
    }
    

    if (!$stmt->execute()) {
        echo json_encode(['error' => 'Error executing query: ' . $stmt->error]);
        exit();
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => 'Payment registered successfully.']);
    } else {
        echo json_encode(['success' => 'No changes were made.']);
    }

    $stmt->close();
    $conn->close();

    exit();
}
