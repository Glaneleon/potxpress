<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
include('../../config/config.php');

$sql = "SELECT * FROM delivery_riders";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($data);

?>
