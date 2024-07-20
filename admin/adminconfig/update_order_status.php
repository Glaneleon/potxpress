<?php
include('../../config/config.php');

if (isset($_POST['order_id']) && isset($_POST['orderStatus'])) {
    $rider_id = $_POST['rider_id'];
    $order_id = $_POST['order_id'];
    $status = $_POST['orderStatus'];
    $statusname = ($_POST['orderStatus'] === 'remove') ? 'Removed Updates' : (
        ($_POST['orderStatus'] === 'in_transit') ? 'Changed to In-Transit' : (
            ($_POST['orderStatus'] === 'delivered') ? 'Changed to Delivered' : null
        ));
    $user_id = $_SESSION['user_id'];
    $datetime = date("Y-m-d H:i:s");
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (isset($_POST['rider_id'])) {
        // Insert into rider_orders table
        $riderOrdersInsert = "INSERT INTO rider_orders (order_id, rider_id, date) VALUES (?, ?, NOW())";
        $riderOrdersStmt = $conn->prepare($riderOrdersInsert);
        $riderOrdersStmt->bind_param("ii", $order_id, $rider_id);
        $riderOrdersStmt->execute();
        $riderOrdersStmt->close();
    }

    $stmt = $conn->prepare("INSERT INTO order_update_log (user_id, order_id, datetime, text, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $user_id, $order_id, $datetime, $statusname, $ip_address);

    $message = "You have chosen an invalid option.";

    if ($status == 'remove') {
        $removeUpdateOrdersQuery = "UPDATE orders SET status = 1 WHERE order_id = $order_id";
        $conn->query($removeUpdateOrdersQuery);

        $updateOrderStatusQuery = "UPDATE order_status SET in_transit = null, delivered = null WHERE order_id = $order_id";
        $conn->query($updateOrderStatusQuery);

        if ($stmt->execute()) {
            $message = "Record inserted successfully.";
        } else {
            $message = "Error inserting record: " . $conn->error;
        }
    } elseif ($status == 'in_transit') {
        $updateOrderStatusQuery = "UPDATE order_status SET in_transit = NOW() WHERE order_id = $order_id";
        $conn->query($updateOrderStatusQuery);
        $updateOrdersQuery = "UPDATE orders SET status = 2 WHERE order_id = $order_id";
        $conn->query($updateOrdersQuery);

        if ($stmt->execute()) {
            $message = "Record inserted successfully.";
        } else {
            $message = "Error inserting record: " . $conn->error;
        }
    } elseif ($status == 'delivered') {
        $updateDeliveredQuery = "UPDATE order_status SET delivered = NOW() WHERE order_id = $order_id";
        $conn->query($updateDeliveredQuery);
        $updateOrdersQuery = "UPDATE orders SET status = 3 WHERE order_id = $order_id";
        $conn->query($updateOrdersQuery);

        if ($stmt->execute()) {
            $message = "Record inserted successfully.";
        } else {
            $message = "Error inserting record: " . $conn->error;
        }
    } else {
        header("Location: ../view_order_details.php?order_id=$order_id&msg=" . urlencode($message));
        exit();
    }

    $stmt->close();

    header("Location: ../view_order_details.php?order_id=$order_id&msg=" . urlencode($message));
    exit();
} else {
    // If order_id or status is not set, redirect to the order page
    header("Location: ../admin.php");
    exit();
}
