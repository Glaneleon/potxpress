<?php
if (!isset($_SESSION)) {
    session_start();
}

include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

$draw = intval($_POST['draw']) ?? null;
$start = intval($_POST['start']) ?? null;
$length = intval($_POST['length']) ?? null;
$search = $_POST['search']['value'] ?? null;

$query = "SELECT * FROM order_notifications";

if (!empty($search)) {
    $query .= " WHERE order_id LIKE '%" . $search . "%'";
}

$result = $conn->query($query);
$totalRecords = $result->num_rows;

$query .= " ORDER BY created_at DESC LIMIT $start, $length";
$result = $conn->query($query);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$output = array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
);

echo json_encode($output);

$conn->close();
