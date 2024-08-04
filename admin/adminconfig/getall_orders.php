<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('../../config/config.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$draw = $_POST['draw'];
$order = $_POST['order'] ?? null;
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];
$min_date = isset($_POST['min_date']) ? $_POST['min_date'] : null;
$max_date = isset($_POST['max_date']) ? $_POST['max_date'] : null;

$totalRecordsQuery = "SELECT COUNT(*) as total FROM orders";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecordsRow = $totalRecordsResult->fetch_assoc();
$totalRecords = $totalRecordsRow['total'];

$whereClause = "";
if (!empty($search)) {
    $whereClause .= " WHERE (orders.order_id_no LIKE '%$search%'
                   OR uers_test.firstname LIKE '%$search%' 
                   OR uers_test.lastname LIKE '%$search%')";
}

$orderBy = '';
if (isset($order) && count($order) > 0) {
    $orderBy = 'ORDER BY ';
    $columnMap = array('0' => 'orders.order_id', '1' => 'uers_test.firstname', '2' => 'orders.order_date', '3' => 'orders.total_amount', '4' => 'orders.payment_img', '5' => 'orders.payment_mode', '6' => 'orders.status'); // Adjust based on your column order
    for ($i = 0; $i < count($order); $i++) {
        $columnIdx = $order[$i]['column'];
        $orderBy .= $columnMap[$columnIdx] . ' ' . $order[$i]['dir'] . ', ';
    }
    $orderBy = substr_replace($orderBy, '', -2);
}

if (!empty($min_date) && !empty($max_date)) {
    $whereClause .= ($whereClause == "" ? " WHERE" : " AND") . " orders.order_date BETWEEN '$min_date' AND '$max_date'";
}

$orderQuery = "SELECT orders.*, uers_test.firstname, uers_test.lastname 
              FROM orders 
              INNER JOIN uers_test ON orders.user_id = uers_test.user_id
              $whereClause $orderBy
              LIMIT $start, $length";

$orderResult = $conn->query($orderQuery);

$filteredRecordsQuery = "SELECT COUNT(*) as total FROM orders
                       INNER JOIN uers_test ON orders.user_id = uers_test.user_id
                       $whereClause";
$filteredRecordsResult = $conn->query($filteredRecordsQuery);
$filteredRecordsRow = $filteredRecordsResult->fetch_assoc();
$filteredRecords = $filteredRecordsRow['total'];

$data = array();
while ($row = $orderResult->fetch_assoc()) {
    $data[] = $row;
}

$output = array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
);

echo json_encode($output);
