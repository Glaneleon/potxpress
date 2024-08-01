<?php
include('../../config/config.php');
date_default_timezone_set('Asia/Manila');

if (isset($_POST['order_id']) && isset($_POST['orderStatus'])) {
    $rider_id = isset($_POST['rider_id']) ? $_POST['rider_id'] : null;
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;
    $status = isset($_POST['orderStatus']) ? $_POST['orderStatus'] : null;
    $delivery_date = isset($_POST['delivery_date']) ? $_POST['delivery_date'] : null;
    $delivery_time = isset($_POST['delivery_time']) ? $_POST['delivery_time'] : null;
    $notificationMessage = isset($_POST['message_textarea']) ? $_POST['message_textarea'] : null;

    

    // $statusname = ($_POST['orderStatus'] === 'remove') ? 'Removed Updates' : (
    //     ($_POST['orderStatus'] === 'in_transit') ? 'Changed to In-Transit' : (($_POST['orderStatus'] === 'confirmed') ? 'Changed to Confirmed') :(
    //         ($_POST['orderStatus'] === 'delivered') ? 'Changed to Delivered' : null
    // ));
    $statusname = null;

        switch ($_POST['orderStatus']) {
        case 'remove':
            $statusname = 'Removed Updates';
            break;
        case 'in_transit':
            $statusname = 'Changed to In-Transit';
            break;
        case 'confirmed':
            $statusname = 'Changed to Order Confirmed';
            break;
        case 'delivered':
            $statusname = 'Changed to Delivered';
            break;
        case 'invalid':
            $statusname = 'Invalid Order';
            break;
    }
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

    //-- date for the notification
    $notificationDate = date("Y/m/d H:i:s");
    
    //--- get the order_id_no from the database
    $getOrderIdNoQuery = "SELECT * FROM orders WHERE order_id = $order_id";
    $getOrderIdNoQueryResult = $conn->query($getOrderIdNoQuery);
    if( $getOrderIdNoQueryResult -> num_rows > 0){
        $row = $getOrderIdNoQueryResult->fetch_assoc();
        $order_id_no = $row["order_id_no"];
        $order_user_id = $row["user_id"];
        $total_amount = $row["total_amount"];
        $payment_mode = $row["payment_mode"];
        
    }


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
    } elseif ($status == 'confirmed') {
        $updateOrderStatusQuery = "UPDATE order_status SET order_confirmed = NOW() WHERE order_id = $order_id";
        $conn->query($updateOrderStatusQuery);
        if($payment_mode == 'gcash'){
            $updateOrdersQuery = "UPDATE orders SET status = 4, payment_received = $total_amount WHERE order_id = $order_id";
            $conn->query($updateOrdersQuery);
        }else if($payment_mode == 'cod'){
            $updateOrdersQuery = "UPDATE orders SET status = 4 WHERE order_id = $order_id";
            $conn->query($updateOrdersQuery);
        }
        
        if ($stmt->execute()) {
            // --- Inserting notification 
            
            $orderStatus = 4;
            if(empty($notificationMessage)){
                $notificationMessage = "Thank you for your order! We've received your order ".$order_id_no." and will process it shortly.";
            }
            
            $stmtInsertNotication = $conn->prepare( "INSERT INTO notification (order_id, user_id, messages, order_status, notif_date) VALUES (?, ?, ?, ?, ?)");
            $stmtInsertNotication->bind_param("iisis", $order_id, $order_user_id, $notificationMessage , $orderStatus, $notificationDate);
            $stmtInsertNotication->execute();
            $stmtInsertNotication->close();
            $message = "Record inserted successfully.";
        } else {
            $message = "Error inserting record: " . $conn->error;
        }
    }
    elseif ($status == 'in_transit') {
        // $delivery_date = date("Y-m-d H:i:s");
        $deliveryDateAndTime = date("Y-m-d H:i:s", strtotime($delivery_date. " ". $delivery_time));
        // $updateOrderStatusQuery = "UPDATE order_status SET in_transit = NOW() WHERE order_id = $order_id";
        $updateOrderStatusQuery = "UPDATE order_status SET in_transit = '".$deliveryDateAndTime."' WHERE order_id = '".$order_id."' "; // putang inang = yan !!!
        $conn->query($updateOrderStatusQuery);
        // --- update the Orders 
        $updateOrdersQuery = "UPDATE orders SET status = 2, delivery_date = '".$deliveryDateAndTime."'  WHERE order_id = $order_id";
        $conn->query($updateOrdersQuery);

        if ($stmt->execute()) {
            //-- notification
            $messageDeliveryDate = date('M d, Y', strtotime($delivery_date));
            // $time = DateTime::createFromFormat('H:i:s', $delivery_time);
            // $normalTime = $time->format('h:i:s A');
            $normalTime = date('h:i A', strtotime($delivery_time));

            $orderStatus = 2;
            if(empty($notificationMessage)){
                $notificationMessage = "Your order ".$order_id_no." is on its way! Expect to receive your package by ".$messageDeliveryDate. " at ". $normalTime;
            }
            
            $stmtInsertNotication = $conn->prepare( "INSERT INTO notification (order_id, user_id, messages, order_status, notif_date) VALUES (?, ?, ?, ?, ?)");
            $stmtInsertNotication->bind_param("iisis", $order_id, $order_user_id, $notificationMessage , $orderStatus, $notificationDate);
            $stmtInsertNotication->execute();
            $stmtInsertNotication->close();

            
            // $message = "Record inserted successfully.";
            $message = $delivery_date. " " . $delivery_time;
        } else {
            $message = "Error inserting record: " . $conn->error;
        }
    } elseif ($status == 'delivered') {
        $updateDeliveredQuery = "UPDATE order_status SET delivered = NOW() WHERE order_id = $order_id";
        $conn->query($updateDeliveredQuery);
        $updateOrdersQuery = "UPDATE orders SET status = 3 WHERE order_id = $order_id";
        $conn->query($updateOrdersQuery);

        if ($stmt->execute()) {
            $orderStatus = 3;
            if(empty($notificationMessage)){
                $notificationMessage = "Your order ".$order_id_no." has been delivered";
            }
            
            $stmtInsertNotication = $conn->prepare( "INSERT INTO notification (order_id, user_id, messages, order_status, notif_date) VALUES (?, ?, ?, ?, ?)");
            $stmtInsertNotication->bind_param("iisis", $order_id, $order_user_id, $notificationMessage , $orderStatus, $notificationDate);
            $stmtInsertNotication->execute();
            $stmtInsertNotication->close();
            $message = "Record inserted successfully.";
        } else {
            $message = "Error inserting record: " . $conn->error;
        }
    } elseif ($status == 'invalid') {
        $updateDeliveredQuery = "UPDATE order_status SET invalid = NOW() WHERE order_id = $order_id";
        $conn->query($updateDeliveredQuery);
        $updateOrdersQuery = "UPDATE orders SET status = 5, payment_status = 1 WHERE order_id = $order_id";
        $conn->query($updateOrdersQuery);

        if ($stmt->execute()) {
            $orderStatus = 5;
            if(empty($notificationMessage)){
                $notificationMessage = "Your order ".$order_id_no." cannot be processed. Please check details.";
            }
            
            $stmtInsertNotication = $conn->prepare( "INSERT INTO notification (order_id, user_id, messages, order_status, notif_date) VALUES (?, ?, ?, ?, ?)");
            $stmtInsertNotication->bind_param("iisis", $order_id, $order_user_id, $notificationMessage , $orderStatus, $notificationDate);
            $stmtInsertNotication->execute();
            $stmtInsertNotication->close();
            $message = "Record inserted successfully.";
        } else {
            $message = "Error inserting record: " . $conn->error;
        }
    } 
     else {
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
