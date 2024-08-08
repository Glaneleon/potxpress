<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('../../config/config.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get DataTable parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderId = isset($_POST['orderId']) ? $_POST['orderId'] : '';

// Order by
$order = isset($_POST['order']) ? $_POST['order'] : [];
$orderBy = '';
if (!empty($order)) {
    $orderBy = ' ORDER BY ' . $_POST['columns'][$order[0]['column']]['data'] . ' ' . $order[0]['dir'];
}

// Search logic (example using LIKE)
$whereClause = " WHERE type LIKE '%Receipt%'";
if (!empty($search)) {
    $whereClause = " OR order_id LIKE '%$search%'";
}

// Your SQL query with potential filtering, sorting, etc.
$sql = "SELECT * FROM pdfs" . $whereClause . $orderBy . " LIMIT $start, $length";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

} else {
    // Handle no data found condition
    $data = array('data' => []);
}

// Binary Search Algorithm 
if (isset($_POST['orderId'])){
    usort($data, 'compare_order_id');

    $target_order_id = $orderId; // search value here
    $index = binarySearch($data, $target_order_id, 'order_id');

    if ($index !== -1) {
        $foundRecord = $data[$index];
        $data = array($foundRecord);
        // echo "Found record with Order ID: #" . $foundRecord['order_id'];
    } else {
        $data = array('data' => []);
    }
}
// /Binary Search Algorithm 

$conn->close();

// DataTable response format
$output = array(
    "draw" => $draw,
    "recordsTotal" => count($data), // Total records
    "recordsFiltered" => count($data), // Filtered records (for future filtering)
    "data" => $data
);

echo json_encode($output);

function compare_order_id($a, $b) {
    return $a['order_id'] <=> $b['order_id'];
}

function binarySearch($array, $target, $key) {
    $left = 0;
    $right = count($array) - 1;

    while ($left <= $right) {
        $mid = floor(($left + $right) / 2);

        if ($array[$mid][$key] === $target) {
            return $mid;
        } elseif ($array[$mid][$key] < $target) {
            $left = $mid + 1;
        } else {
            $right = $mid - 1;
        }
    }

    return -1;
}
