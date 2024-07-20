<?php
session_start();
include('../../config/config.php');
$current_user = $_SESSION['user_id'];
$ipAddress = $_SERVER['REMOTE_ADDR'];
$dateFrom = date('Y-m-d H:i:s', strtotime('-1 day'));
$today = date('Y-m-d');

if ($conn->connect_error) {
    returnError("Connection failed: " . $conn->connect_error);
}

function returnSuccess($data) {
    header('Content-Type: application/json');
    $responseData = ['success' => true, $data];
    echo json_encode($responseData);
    exit();
}

function returnError($errorMessage) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $errorMessage]);
    exit();
}

$query = "SELECT * FROM products WHERE stock_quantity > 0 AND stock_quantity < 10";
$result = $conn->query($query);
if (!$result) {
    returnError("Error checking product stocks less than ten: " . $conn->error);
}
$lowStockProducts = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * FROM products WHERE stock_quantity = 0";
$result = $conn->query($query);
if (!$result) {
    returnError("Error checking zero product stocks: " . $conn->error);
}
$outOfStockProducts = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * FROM inventory_log WHERE datetime BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $dateFrom, $today);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    returnError("Error checking product stock logs: " . $conn->error);
}
$inventoryLogs = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * FROM product_add_log WHERE added_at BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $dateFrom, $today);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    returnError("Error checking product add logs: " . $conn->error);
}
$productAddLogs = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * FROM product_edit_log WHERE edited_at BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $dateFrom, $today);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    returnError("Error checking product edit logs: " . $conn->error);
}
$productEditLogs = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * FROM product_delete_log WHERE deleted_at BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $dateFrom, $today);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    returnError("Error checking product delete logs: " . $conn->error);
}
$productDeleteLogs = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * FROM orders WHERE order_date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $today);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    returnError("Error checking orders: " . $conn->error);
}
$todayOrders = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();

$returnData = [
    'lowStockProducts' => $lowStockProducts,
    'outOfStockProducts' => $outOfStockProducts,
    'inventoryLogs' => $inventoryLogs,
    'productAddLogs' => $productAddLogs,
    'productEditLogs' => $productEditLogs,
    'productDeleteLogs' => $productDeleteLogs,
    'todayOrders' => $todayOrders,
];

returnSuccess($returnData);
?>
