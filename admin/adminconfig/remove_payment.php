<?php
require_once('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['orderId'];
    $amount = $_POST['amount'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $type = "Remove";

    $sql = "UPDATE `orders` SET `payment_received` = NULL WHERE `order_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        $logsql = "INSERT INTO `payment_log` (`amount`, `order_id`, `ip_address`, `type`) VALUES (?, ?, ?, ?);";
        $logstmt = $conn->prepare($logsql);
        $logstmt->bind_param("diss", $amount, $orderId, $ip, $type);
        $logstmt->execute();

        echo json_encode(['success' => true, 'message' => 'Payment received for order ID ' . $orderId . ' removed successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating payment received: ' . $stmt->error]);
    }

    $stmt->close();

    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}