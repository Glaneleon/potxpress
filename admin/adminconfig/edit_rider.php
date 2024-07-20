<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $riderId = $_POST['riderId'];
    $fullName = $_POST['fullName'];
    $contactNumber = $_POST['contactNumber'];

    // Validate input
    if (empty($riderId)) {
        echo 'riderId_invalid';
        exit;
    }

    if (empty($fullName) || strlen($fullName) < 3) {
        echo 'fullName_invalid';
        exit;
    }

    if (!preg_match('/^[0-9]{11}$/', $contactNumber)) {
        echo 'contactNumber_invalid';
        exit;
    }

    $stmt = $conn->prepare("UPDATE delivery_rider SET name = ?, contact_number = ? WHERE id = ?");
    $stmt->bind_param("ssi", $fullName, $contactNumber, $riderId);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
    $conn->close();
}
