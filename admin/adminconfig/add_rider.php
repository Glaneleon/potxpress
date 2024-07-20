<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

$name = $_POST['fullName'];
$contact_number = $_POST['contactNumber'];

if (empty($name) || empty($contact_number) || !preg_match('/^[0-9]{11}$/', $contact_number)) {
    echo 'error';
    exit;
}

// Check if contact number already exists
$check_stmt = $conn->prepare("SELECT COUNT(*) FROM delivery_rider WHERE contact_number = ?");
$check_stmt->bind_param("s", $contact_number);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count > 0) {
    echo 'contact_number_exists';
    exit;
}

$stmt = $conn->prepare("INSERT INTO delivery_rider (name, contact_number) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $contact_number);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

$stmt->close();
$conn->close();
