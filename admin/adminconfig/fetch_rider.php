<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $riderId = $_POST['riderId'];

    $stmt = $conn->prepare("SELECT id, name, contact_number FROM delivery_rider WHERE id = ?");
    $stmt->bind_param("i", $riderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = array('success' => true, 'data' => $row);
    } else {
        $response = array('success' => false, 'message' => 'Rider not found');
    }

    echo json_encode($response);

    $stmt->close();
    $conn->close();
}
