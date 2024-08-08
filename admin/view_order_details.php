<?php
include('../config/config.php');
$user_id = '';
$amount = 0;
$canSetDelivered = 0;

// Check if the order_id is set in the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../assets/icons/PotXpressicon1.png" type="image/icon type">
        <title>Order Details for #<?= $order_id ?></title>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .customer-name {
                display: inline-block;
            }

            body {
                background-color: #ddede1;
            }
        </style>
    </head>

    <body>

    <?php
    $receiptsql = "SELECT * FROM pdfs WHERE order_id = ? AND type != 'Official COD Receipt' ORDER BY created_at DESC LIMIT 1";
    $receiptstmt = $conn->prepare($receiptsql);
    $receiptstmt->bind_param("i", $order_id);
    $receiptstmt->execute();
    $receiptresult = $receiptstmt->get_result();
    if ($receiptresult->num_rows > 0) {
        $receiptrow = $receiptresult->fetch_assoc();
        $receipt = 'receipts/' . $receiptrow['file_path'];
    }

    // Retrieve order details and product information
    $orderDetailsQuery = "
        SELECT orders.*, order_details.*, products.*, order_status.order_placed, order_status.in_transit, order_status.delivered, order_status.order_confirmed
        FROM orders 
        JOIN order_details ON orders.order_id = order_details.order_id 
        JOIN products ON order_details.product_id = products.product_id 
        LEFT JOIN order_status ON orders.order_id = order_status.order_id
        WHERE orders.order_id = $order_id";

    $orderDetailsResult = $conn->query($orderDetailsQuery);

    if (!$orderDetailsResult) {
        // Handle query error
        die("Error fetching order details.");
    }

    $totalprice = 0;
    $orderDetails = [];

    while ($orderDetail = $orderDetailsResult->fetch_assoc()) {
        $orderDetails[] = $orderDetail;
        $user_id = $orderDetail['user_id'];
        $totalprice += ($orderDetail['price'] * $orderDetail['quantity']);
    }

    $orderplaced = isset($orderDetails[0]['order_placed']) ? $orderDetails[0]['order_placed'] : null;
    $intransit = isset($orderDetails[0]['in_transit']) ? $orderDetails[0]['in_transit'] : 'Not In Transit';
    $orderconfirmed = isset($orderDetails[0]['order_confirmed']) ? $orderDetails[0]['order_confirmed'] : 'Not Yet Confirmed';
    $delivered = isset($orderDetails[0]['delivered']) ? $orderDetails[0]['delivered'] : 'Not Yet Delivered';

    // Build the table content
    $table_content = '<thead>
      <tr>
          <th>Product ID</th>
          <th>Product Name</th>
          <th>Product Color</th>
          <th>Quantity</th>
          <th>Total Price</th>
      </tr>
     </thead>
     <tbody>';
    // var_dump($orderDetails);
    foreach ($orderDetails as $detail) {
        $table_content .= '<tr>
          <td>' . $detail['product_id'] . '</td>
          <td>' . $detail['name'] . '</td>
          <td>' . $detail['product_color'] . '</td>
          <td>' . $detail['quantity'] . '</td>
          <td>₱ ' . ($detail['price'] * $detail['quantity']) . '</td>
      </tr>';
    }

    $table_content .= '<tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>₱ ' . $totalprice . '</td>
     </tr>
     </tbody>';

    // Build the order status rows
    $status_rows = '';
    $statuses = [
        'order_placed' => ['image' => '../assets/icons/order-placed.svg', 'alt' => 'Order Placed', 'text' => 'Order Placed', 'value' => $orderplaced],
        'order_confirmed' => ['image' => '../assets/icons/order-confirmed.svg', 'alt' => 'Order Confirmed', 'text' => 'Order Confirmed', 'value' => $orderconfirmed],
        'in_transit' => ['image' => '../assets/icons/in-transit.svg', 'alt' => 'In Transit', 'text' => 'In Transit', 'value' => $intransit],
        'delivered' => ['image' => '../assets/icons/delivered.png', 'alt' => 'Delivered', 'text' => 'Delivered', 'value' => $delivered],
    ];

    // var_dump($statuses);
    foreach ($statuses as $status => $data) {
        $status_value = $data['value'];
        if ($status_value !== null && strpos($status_value, 'Not') === false) {
            $status_rows .= '<div class="row my-2 pt-3">
                    <div class="col-2">
                        <img src="' . $data['image'] . '" alt="' . $data['alt'] . '" style="width: 30px; height: 30px;">
                    </div>
                    <div class="col-6">
                        <p class="mx-0">' . ($status_value ? $status_value : 'Not Yet ' . ucfirst($status)) . '</p>
                    </div>
                    <div class="col-4">
                        ' . $data['text'] . '
                    </div>
                </div>';
        }
    }

    if (empty($status_rows)) {
        $status_rows .= '<div class="row my-2"><div class="col-12"><p><span class="text-danger fw-bold">ERROR: </span>There are no status records for this order.</p></div></div>';
    }


    // Combine everything
    $output = '<div class="my-5">
     <div class="row">
        <div class="col-9">
           <h2>Order Details</h2>
           <p class="small"><span class="fw-bold">Order Date: </span>' . $detail['order_date'] . '</p>
        </div>
        <div class="col-3">';

    if (!isset($detail['payment_received']) || $detail['status'] !== '6') {
        $output .= '
                    <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#updateOrderStatusModal">
                        Update Order Status
                    </button>';
    }


    $output .= ' <a href="./orders.php" class="btn btn-secondary">Go Back</a></div>
     </div>
     <div class="table-responsive">
       <table class="table table-bordered">
         ' . $table_content . '
       </table>
     </div>
     </div>';

    $customerDetailsQuery = "SELECT uers_test.user_id, uers_test.firstname, uers_test.lastname, shipping_address.* FROM uers_test INNER JOIN shipping_address
     ON uers_test.user_id = shipping_address.user_id WHERE uers_test.user_id = '" . $user_id . "' AND shipping_address.onSelected = '1' ";

    $customerDetailsQueryResult = $conn->query($customerDetailsQuery);

    if (!$customerDetailsQueryResult) {
        // Handle query error
        die("Error fetching customer details.");
    }
    $customerDetails = [];

    while ($customerDetail = $customerDetailsQueryResult->fetch_assoc()) {
        $customerDetails[] = $customerDetail;
    }
    $customerName = ucfirst($customerDetails[0]['firstname']) . ' ' . ucfirst($customerDetails[0]['lastname']);

    $address = $customerDetails[0]['street_no'] . ' ' . $customerDetails[0]['baranggay'] . ' ' . $customerDetails[0]['city'] . ' ' . $customerDetails[0]['province'];

    if ($detail['payment_received'] !== null || !empty($detail['payment_received'])) {
        $paymentReceived = 'Paid';
        $amount = $detail['payment_received'];
    } else {
        $paymentReceived = 'Not yet paid';
    }

    // for checking database values
    // var_dump($detail);
    $customer_details = '';

    if ($detail['status'] == '6') {
        $customer_details .= '<h1 class="text-danger fw-bold">ORDER CANCELLED</h1>';
    }

    $customer_details .= '
         <div class="col">
            <p class="mb-0"><strong>Order ID:</strong> ' . $detail['order_id'] . '</p>
            <p class="mb-0"><strong>Order ID No.:</strong> ' . $detail['order_id_no'] . '</p>
            <p class="mb-0"><strong>Customer Name:</strong> ' . $customerName . '</p>
            <p class="mb-0"><strong>Shipping Address:</strong> ' . $address . '</p>
            <p class="mb-0"><strong>Payment Status:</strong> ' . $paymentReceived . '</p>';

    if (!empty($receipt)) {
        $customer_details .= '<p class="mb-0"><strong>Receipt: </strong><a href="../' . $receipt . '" target="_blank" rel="noopener noreferrer">Click Here</a></p>';
    }

    if (!empty($detail['payment_mode'])) {
        $customer_details .= '<p class="mb-0"><strong>Payment Mode:</strong> ' . strtoupper($detail['payment_mode']) . '</p>';
    }

    if ($detail['payment_mode'] == 'gcash') {
        $customer_details .= '<p class="mb-0"><strong>Proof of Payment: </strong><a href="../' . $detail['payment_img'] . '" target="_blank" rel="noopener noreferrer">Click Here</a></p>';
    }

    if ($detail['payment_received'] !== null || !empty($detail['payment_received'])) {
        $customer_details .= '<p class="mb-0"><strong>Payment Received:</strong> ₱ ' . $detail['payment_received'] . '</p>';
    }

    if ($detail['status'] == '4' && !isset($detail['payment_received']) && $detail['payment_mode'] !== 'gcash') {
        $customer_details .= '</div><div class="col-md-2"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">Register Payment</button></div></div></div>';
    }

    $customer_details .= '</div>';

    if (isset($detail['payment_received']) && $detail['payment_mode'] !== 'gcash') {
        $customer_details .= '<button class="btn btn-danger mt-3" id="removePayment">Remove Payment Record</button>';
    }

    $feedbacksql = "SELECT * FROM feedback_web WHERE order_id = ? AND user_id = ?";
    $feedbackstmt = $conn->prepare($feedbacksql);
    $feedbackstmt->bind_param("ii", $order_id, $customerDetails[0]['user_id']);
    $feedbackstmt->execute();
    $feedbackresult = $feedbackstmt->get_result();

    if ($feedbackresult->num_rows > 0) {
        $feedbackrow = $feedbackresult->fetch_assoc();
        if ($feedbackrow['received'] !== null) {
            $canSetDelivered = 1;
        }
    }

    $customerFeedback = '';

    if (!empty($feedbackrow['message'])) {
        $customerFeedback = '
        <div class="card overflow-auto" style="height: 21rem;">
          <div class="card-body">
            <h6 class="card-title">Customer Feedback:</h6>
            <p class="card-text">' . $feedbackrow['message'] . '</p>
          </div>
        </div>';
    }
    echo '
    <div class="container">
     <section id="top">
        <div class="row my-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        ' . $output . '
                    </div>
                </div>
            </div>
        </div>
     </section>
        <div class="row my-4">
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                    <h4>Customer Details:</h4>
                        ' . $customer_details . '
                    </div>
                </div>
            </div>
            <div class="col-4">
                ' . $customerFeedback . '
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                    <h4>Order Status:</h4>
                        ' . $status_rows . '
                    </div>
                </div>
            </div>
        </div>
        '; // closing tag for container at the very bottom

    $orderId = $detail['order_id'];
    $orderNo = $detail['order_id_no'];
    $type = $detail['payment_mode'];

    $date = DateTime::createFromFormat('Y-m-d H:i:s', $detail['order_date']);
    $orderDate = $date->format('F d, Y');

    $data = array();
    foreach ($orderDetails as $detail) {
        $data[] = array(
            'product_name' => $detail['name'],
            'product_color' => $detail['product_color'],
            'quantity' => $detail['quantity'],
            'price' => $detail['price']
        );
    }

    $jsonData = json_encode($data);
} else {
    // Redirect to orders page
    header("Location: ./orders.php");
    exit();
}
    ?>
    <div class="row my-4">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <h5>Latest Delivery Rider: <a href="#riders" title="Click here to view all riders related to this order.">↵</a></h5>

                    <?php
                    $query = "SELECT rider_id FROM rider_orders WHERE order_id = ? ORDER BY date DESC LIMIT 1;";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $order_id);

                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        if ($row) {
                            $riderQuery = "SELECT * FROM delivery_riders WHERE id = ?";
                            $riderStmt = $conn->prepare($riderQuery);
                            $riderStmt->bind_param("i", $row["id"]);
                            $riderStmt->execute();
                            $riderResult = $riderStmt->get_result();

                            if ($riderResult->num_rows > 0) {
                                $riderRow = $riderResult->fetch_assoc();
                                $riderName = $riderRow["first_name"] . $riderRow["last_name"];
                                $riderContact = $riderRow["contact_number"];
                            } else {
                                $riderName = "Unknown Rider";
                                $riderContact = "Missing Details";
                            }
                            echo 'Name: ' . $riderName . '<br>';
                            echo 'Mobile: ' . $riderContact;
                        } else {
                            echo 'Your order doesn\'t have a rider yet.';
                        }
                    } else {
                        echo 'Error fetching rider information: ' . $stmt->error;
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <?php
        $sql = "SELECT status FROM orders WHERE order_id = $order_id";
        $result = $conn->query($sql);

        // Check if the query returned a result
        if ($result->num_rows > 0) {
            // Fetch the result
            $row = $result->fetch_assoc();
            $status = $row["status"];

            $query = "SELECT * FROM order_update_log WHERE order_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $order_id);  // Bind order_id as integer

            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo '<div class="container my-5 py-2 card">';
                    echo "<h2>Order Update Log</h2>";
                    echo '<div class="table-responsive"><table class="table table-bordered">';
                    echo "<tr>";
                    echo "<th>User ID</th>";
                    echo "<th>Date/Time</th>";
                    echo "<th>Change</th>";
                    echo "<th>IP Address</th>";
                    echo "</tr>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "<td>" . $row["datetime"] . "</td>";
                        echo "<td>" . $row["text"] . "</td>";
                        echo "<td>" . $row["ip_address"] . "</td>";
                        echo "</tr>";
                    }

                    echo "</table></div></div>";
                } else {
                    echo "No updates found for this order.";
                }
            } else {
                echo "Error fetching order update log: " . $conn->error;
            }

            $stmt->close();
        } else {
            // If the query didn't return any result, display an error message
            echo "No order found with the given order ID.";
        }
        ?>

        <?php
        // --- Display the riders log
        $query = "SELECT * FROM rider_orders WHERE order_id = ? ORDER BY date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<section id="riders">';
                echo '<div class="container card my-5 py-2">';
                echo "<h2>Delivery Rider Log</h2>";
                echo "<p class='small m-2 text-muted'>Latest rider at the top of the list.</p>";
                echo '<div class="table-responsive"><table class="table table-bordered">';
                echo "<tr>";
                echo "<th>Rider</th>";
                echo "<th>Mobile Number</th>";
                echo "<th>Delivery Schedule</th>";
                echo "</tr>";

                while ($row = $result->fetch_assoc()) {
                    $riderQuery = "SELECT * FROM delivery_riders WHERE id = ?";
                    $riderStmt = $conn->prepare($riderQuery);
                    $riderStmt->bind_param("i", $row["rider_id"]);
                    $riderStmt->execute();
                    $riderResult = $riderStmt->get_result();

                    if ($riderResult->num_rows > 0) {
                        $riderRow = $riderResult->fetch_assoc();
                        $riderName = $riderRow["first_name"] . ' ' . $riderRow["last_name"];
                        $riderContact = $riderRow["contact_number"];
                    } else {
                        $riderName = "Unknown Rider";
                        $riderContact = "Missing Details";
                    }

                    echo "<tr>";
                    echo "<td>" . $riderName . "</td>";
                    echo "<td>" . $riderContact . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "</tr>";

                    $riderStmt->close();
                }

                echo "</table></div></div></section>";
            } else {
                echo "No rider/s found for this order.";
            }
        } else {
            echo "Error fetching rider log: " . $conn->error;
        }

        $stmt->close();
        ?>
    </div>

    <a href="#top" title="Click here to go to the top." class="position-fixed bottom-0 end-0 m-4">
        <img src="../assets/icons/circle-up.svg" style="width: 40px; height: 40px;">
    </a>
    </div>
    <!-- register payment modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Register Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentModalForm">
                        <input type="number" class="form-control" id="orderID" name="orderID" required hidden value="<?= $order_id ?>"></input>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select required name="mode" class="form-select">
                                <!-- <option selected disabled value="">Select Payment Method</option>
                                <option value="gcash">GCash</option> -->
                                <option value="cod">Cash on Delivery</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <!-- <p class="small text-muted"><span class="text-danger">*</span>Additional ₱50.00 shipping fee.</p> -->
                            <input type="float" class="form-control" id="amount" name="amount" required value=""></input>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="paymentModalForm" class="btn btn-primary">Register</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /register payment modal -->
    <!-- update order status modal -->
    <div class="modal fade" id="updateOrderStatusModal" tabindex="-1" aria-labelledby="updateOrderStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateOrderStatusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateOrderStatusForm" action="./adminconfig/update_order_status.php" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                <!-- Order Status -->
                                <!-- <label for="order_status_label">Order Status:</label> -->
                                <select name="orderStatus" id="orderStatus" class="form-select" required>
                                    <option value="" selected disabled>Select Status</option>
                                    <option value="remove">Remove All Updates</option>
                                    <option value="confirmed">Order Confirmed</option>
                                    <option value="in_transit">In Transit</option>
                                    <?php
                                    if ($canSetDelivered === 1) {
                                        echo '<option value="delivered">Delivered</option>';
                                    }
                                    ?>
                                    <option value="invalid">Invalid Order</option>
                                    <option value="cancel">Cancel Order</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Delivery Date -->
                            <div class="col-md-6" id="delivery_datepicker" style="display: none;">
                                <label for="delivery_date_label">Delivery Date:</label>
                                <input id="deliveryDate" name="delivery_date" type="date" class="form-select" />
                            </div>
                            <!-- Delivery Time -->
                            <div class="col-md-6" id="delivery_timepicker" style="display: none;">
                                <label for="delivery_time_label">Delivery Time:</label>
                                <input id="deliveryTime" name="delivery_time" type="time" class="form-select" value="<?php echo date("H:i:s"); ?>" />
                            </div>
                            <!-- Delivery Rider -->
                            <div class="col-md-6 mt-2" id="rider_dropdown" style="display: none;">
                                <label for="order_status_label">Delivery Rider:</label>
                                <select name="rider_id" id="rider_id" class="form-select" required>
                                    <option value="" selected disabled>Select Rider</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div id="notification_message" style="display: none;">
                                <label for="notification_label">Message:</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" name="message_textarea" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /update order status modal -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <?php
    // Check if $_GET['msg'] is set
    if (isset($_GET['msg']) && !empty($_GET['msg'])) {
        $message = htmlspecialchars($_GET['msg']); // Sanitize message for security
        echo "<script>";
        echo "$(document).ready(function() {
                      alert('$message');
                      // Remove the msg parameter from the URL using history.replaceState
                      const url = new URL(window.location.href);
                      url.searchParams.delete('msg');
                      history.replaceState({}, document.title, url.href);
                    });";
        echo "</script>";
    }
    ?>

    <script>
        // --- Update Order Status
        $(document).ready(function() {
            const orderStatusDropdown = document.getElementById('orderStatus');
            const riderDropdown = document.getElementById('rider_dropdown');
            const deliveryDatePicker = document.getElementById('delivery_datepicker');
            const deliveryTimePicker = document.getElementById('delivery_timepicker');
            const notification_message = document.getElementById('notification_message');

            const riderOption = document.getElementById('rider_id');
            const deliveryDate = document.getElementById('deliveryDate');
            const deliveryTime = document.getElementById('deliveryTime');
            const messageText = document.getElementById('messageText');
            const form = document.querySelector('form'); // Assuming the form is the direct parent

            orderStatusDropdown.addEventListener('change', () => {
                // --- if the user select the in transit it will show the rider drop down
                if (orderStatusDropdown.value === 'in_transit' && orderStatusDropdown.value !== 'cancel') {
                    riderDropdown.style.display = 'block';
                    deliveryDatePicker.style.display = 'block';
                    deliveryTimePicker.style.display = 'block';
                    notification_message.style.display = 'block';

                    // Fetch rider data using AJAX and populate the dropdown -- galing sa database(getall_riders.php) i-featch niya tapos display sa dropdown
                    fetch('./adminconfig/getall_riders.php')
                        .then(response => response.json())
                        .then(data => {
                            const riderSelect = riderDropdown.querySelector('select');
                            riderSelect.innerHTML = '<option value="" selected disabled>Select Rider</option>';
                            data.forEach(rider => {
                                const option = document.createElement('option');
                                option.value = rider.id;
                                option.text = rider.first_name + ' ' + rider.last_name;
                                riderSelect.appendChild(option);
                            });
                        });
                } else {
                    riderDropdown.style.display = 'none';
                }
            });

            orderStatusDropdown.addEventListener('change', () => {
                if (orderStatusDropdown.value !== 'in_transit' && orderStatusDropdown.value !== 'cancels') {
                    riderDropdown.style.display = 'none';
                    deliveryDatePicker.style.display = 'none';
                    deliveryTimePicker.style.display = 'none';
                    notification_message.style.display = 'block';
                    riderDropdown.querySelector('select').removeAttribute('required');
                }
            });

            orderStatusDropdown.addEventListener('change', () => {
                if (orderStatusDropdown.value === 'cancel') {
                    riderOption.removeAttribute('required');
                }
            });

            // form.addEventListener('submit', (event) => {
            //     if (orderStatusDropdown.value === 'in_transit' && riderDropdown.querySelector('select').value === '') {
            //         event.preventDefault(); // Prevent form submission
            //         // Display an error message or handle the situation accordingly
            //         alert('Please select a rider.'); // Replace with appropriate error handling
            //     }
            // });
        });
    </script>

    <script>
        $(document).ready(function() {

            const deliveryDateInput = document.getElementById('deliveryDate');

            function disablePastDates() {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const minDate = `${year}-${month}-${day}`;
                deliveryDateInput.min = minDate;
            }

            disablePastDates();



            function generateReceipt() {
                var orderDate = "<?php echo $orderDate; ?>";
                var jsonData = <?php echo $jsonData; ?>;
                var orderNo = "<?php echo $orderNo; ?>";
                var orderId = "<?php echo $orderId; ?>";
                var type = "<?php echo $type; ?>";
                var customerName = "<?php echo $customerName; ?>";

                $.ajax({
                    url: './adminconfig/generate_cod_receipt.php',
                    type: 'POST',
                    data: {
                        jsonData: jsonData,
                        orderDate: orderDate,
                        orderNo: orderNo,
                        orderId: orderId,
                        type: type,
                        customerName: customerName
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.success);
                            window.location.href = './view_order_details.php?order_id=' + orderId;
                        } else {
                            alert('An error occurred while generating the receipt.');
                            window.location.href = './view_order_details.php?order_id=' + orderId;
                            console.error(response.error);
                        }
                    },
                    error: function(error) {
                        console.error(error);
                        alert('An error occurred. Please try again later.');
                    }
                });
            };

            $('#updateOrderStatusForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: './adminconfig/update_order_status.php',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            if (response.type === 'generate') {
                                $('#updateOrderStatusModal').modal('hide');
                                generateReceipt().then(() => {
                                    window.location.href = './view_order_details.php?order_id=' + response.orderId;
                                }).catch(error => {
                                    console.error('Error generating receipt:', error);
                                    alert('Error generating receipt:', error);
                                });
                                alert(response.message);
                            }
                            alert(response.message);
                            window.location.href = './view_order_details.php?order_id=' + response.orderId;
                        } else {
                            $('#updateOrderStatusModal').modal('hide');
                            alert(response.message);
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        $('#errorMessage').html('An error occurred. Please try again later.');
                    }
                });
            });

            $('.register-payment').click(function() {
                $('#paymentModal').modal('show');
            });

            $('#paymentModalForm').submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: './adminconfig/register_payment.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        try {
                            var parsedResponse = JSON.parse(response);
                            if (parsedResponse.success) {
                                alert(parsedResponse.success);
                                $('#paymentModal').modal('hide');
                                location.reload();
                            } else {
                                alert(parsedResponse.error);
                            }
                        } catch (error) {
                            console.error('Error parsing JSON:', error);
                            alert('An error occurred while processing the payment.');
                        }
                    },
                    error: function(error) {
                        console.error('AJAX error:', error);
                        alert('An unexpected error occurred. Please try again later.');
                    }
                });
            });

            $('#removePayment').click(function() {
                var orderId = <?= $order_id ?>;
                var amount = <?= $amount ?>;

                if (confirm("WARNING: This action will remove the payment received for order #" + orderId + ". This change cannot be undone. Are you sure you want to proceed?")) {
                    $.ajax({
                        url: './adminconfig/remove_payment.php',
                        type: 'POST',
                        data: {
                            orderId: orderId,
                            amount: amount
                        },
                        success: function(response) {
                            var parsedResponse = JSON.parse(response);
                            if (parsedResponse.success) {
                                alert(parsedResponse.message);
                            } else {
                                alert(parsedResponse.message);
                            }
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred: ' + error);
                        }
                    });
                }
            });


        });
    </script>
    </body>

    </html>