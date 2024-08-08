<?php
if (!isset($_SESSION)) {
    session_start();
}

include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

if (isset($_POST['notification_id'])) {

    $notificationId = intval($_POST['notification_id']);

    $stmt = $conn->prepare("SELECT is_read FROM order_notifications WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $stmt->bind_result($is_read);
    $stmt->fetch();

    if ($is_read === 1){
        $check = true;
        $error = null;
        header('Content-Type: application/json');
        echo json_encode(['success' => $check, 'error' => $error]);
        exit;
    }

    $stmt->close();

    $updateStmt = $conn->prepare("UPDATE order_notifications SET is_read = 1 WHERE id = ?");
    $updateStmt->bind_param("i", $notificationId);

    if ($updateStmt->execute()) {
        $check = true;
        $error = '';
    } else {
        $check = false;
        $error = $updateStmt->error;
    }

    $updateStmt->close();
    $conn->close();

} else {
    $check = false;
    $error = 'Error fetching notification ID.';
}

header('Content-Type: application/json');
echo json_encode(['success' => $check, 'error' => $error]);
