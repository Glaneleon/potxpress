<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
include('../../config/config.php'); // Include your database connection configuration

$sql = "SELECT id, name, contact_number FROM delivery_rider";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($data); // Encode data as JSON for the DataTable

?>
