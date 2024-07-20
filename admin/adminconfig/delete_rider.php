<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $riderId = $_POST['riderId'];

    if (!is_numeric($riderId)) {
        echo 'riderId_invalid | riderID: '.$riderId;
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM delivery_rider WHERE id = ?");
    $stmt->bind_param("i", $riderId);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
    $conn->close();
}
