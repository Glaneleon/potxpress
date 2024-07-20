<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php
    include('../config/config.php');

    // Check if the order_id is set in the URL
    if (isset($_GET['order_id'])) {
        $order_id = $_GET['order_id'];

        // Retrieve order details and product information
        $orderDetailsQuery = "
        SELECT orders.*, order_details.*, products.*, order_status.order_placed, order_status.in_transit, order_status.delivered
        FROM orders 
        JOIN order_details ON orders.order_id = order_details.order_id 
        JOIN products ON order_details.product_id = products.product_id 
        LEFT JOIN order_status ON orders.order_id = order_status.order_id
        WHERE orders.order_id = $order_id
  ";
        $orderDetailsResult = $conn->query($orderDetailsQuery);

        if (!$orderDetailsResult) {
            // Handle query error
            die("Error fetching order details.");
        }

        $totalprice = 0;
        $orderDetails = [];

        while ($orderDetail = $orderDetailsResult->fetch_assoc()) {
            $orderDetails[] = $orderDetail;
            $totalprice += ($orderDetail['price'] * $orderDetail['quantity']);
        }

        $orderplaced = isset($orderDetails[0]['order_placed']) ? $orderDetails[0]['order_placed'] : null;
        $intransit = isset($orderDetails[0]['in_transit']) ? $orderDetails[0]['in_transit'] : 'Not In Transit';
        $delivered = isset($orderDetails[0]['delivered']) ? $orderDetails[0]['delivered'] : 'Not Yet Delivered';

        // Build the table content
        $table_content = '<thead>
      <tr>
          <th>Product ID</th>
          <th>Product Name</th>
          <th>Quantity</th>
          <th>Total Price</th>
      </tr>
  </thead>
  <tbody>';

        foreach ($orderDetails as $detail) {
            $table_content .= '<tr>
          <td>' . $detail['product_id'] . '</td>
          <td>' . $detail['name'] . '</td>
          <td>' . $detail['quantity'] . '</td>
          <td>₱ ' . ($detail['price'] * $detail['quantity']) . '</td>
      </tr>';
        }

        $table_content .= '<tr>
      <td></td>
      <td></td>
      <td></td>
      <td>₱ ' . $totalprice . '</td>
  </tr>
  </tbody>';

        // Build the order status rows
        $status_rows = '';
        $statuses = [
            'order_placed' => ['image' => '../assets/icons/order_placed.png', 'alt' => 'Order Placed', 'text' => 'Order Placed', 'value' => $orderplaced],
            'in_transit' => ['image' => '../assets/icons/domestic_transit.png', 'alt' => 'In Transit', 'text' => 'In Transit', 'value' => $intransit],
            'delivered' => ['image' => '../assets/icons/delivered.png', 'alt' => 'Delivered', 'text' => 'Delivered', 'value' => $delivered],
        ];

        foreach ($statuses as $status => $data) {
            $status_value = $data['value'];
            $status_rows .= '<div class="row my-2">
            <div class="col-4"></div>
        <div class="col-1">
          <img src="' . $data['image'] . '" alt="' . $data['alt'] . '" style="width: 30px; height: 30px;">
        </div>
        <div class="col-2">
          ' . ($status_value ? $status_value : 'Not Yet ' . ucfirst($status)) . '
        </div>
        <div class="col-2">
          ' . $data['text'] . '
        </div>
        <div class="col-4"></div>
      </div>';
        }

        // Combine everything
        $output = '<div class="container my-5">
    <h2>Order Details</h2>
    <div class="table-responsive">
      <table class="table table-bordered">
        ' . $table_content . '
      </table>
    </div>
    ' . $status_rows . '
  </div>';

        echo $output;
    } else {
        // Redirect to orders page
        header("Location: ./admin.php#orders");
        exit();
    }
    ?>
    <div class="m-5">
        <h5>Latest Delivery Rider: <a href="#riders" title="Click here to view all riders related to this order.">↵</a></h5>

        <?php
        $query = "SELECT rider_id FROM rider_orders WHERE order_id = ? ORDER BY date DESC LIMIT 1;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row) {
                $riderQuery = "SELECT * FROM delivery_rider WHERE id = ?";
                $riderStmt = $conn->prepare($riderQuery);
                $riderStmt->bind_param("i", $row["rider_id"]);
                $riderStmt->execute();
                $riderResult = $riderStmt->get_result();

                if ($riderResult->num_rows > 0) {
                    $riderRow = $riderResult->fetch_assoc();
                    $riderName = $riderRow["name"];
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
    <div class="mx-5">
        <?php
        $sql = "SELECT status FROM orders WHERE order_id = $order_id";
        $result = $conn->query($sql);

        // Check if the query returned a result
        if ($result->num_rows > 0) {
            // Fetch the result
            $row = $result->fetch_assoc();
            $status = $row["status"];
        ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateOrderStatusModal">
                Update Order Status
            </button>

            <div class="modal fade" id="updateOrderStatusModal" tabindex="-1" aria-labelledby="updateOrderStatusModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateOrderStatusModalLabel">Update Order Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="./adminconfig/update_order_status.php" method="post" class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                    <select name="orderStatus" id="orderStatus" class="form-select" required>
                                        <option value="" selected disabled>Select Status</option>
                                        <option value="remove">Remove All Updates</option>
                                        <option value="in_transit">In Transit</option>
                                        <option value="delivered">Delivered</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="rider_dropdown" style="display: none;">
                                    <select name="rider_id" class="form-select" required>
                                        <option value="" selected disabled>Select Rider</option>
                                    </select>
                                </div>
                                <div class="col-md-auto">
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <a href="./admin.php" class="btn btn-secondary">Go Back</a>

        <?php
            $query = "SELECT * FROM order_update_log WHERE order_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $order_id);  // Bind order_id as integer

            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo '<div class="container my-5">';
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
        $query = "SELECT * FROM rider_orders WHERE order_id = ? ORDER BY date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<section id="riders">';
                echo '<div class="container my-5">';
                echo "<h2>Delivery Rider Log</h2>";
                echo "<p class='small m-2 text-muted'>Latest rider at the top of the list.</p>";
                echo '<div class="table-responsive"><table class="table table-bordered">';
                echo "<tr>";
                echo "<th>Rider</th>";
                echo "<th>Mobile Number</th>";
                echo "<th>Date & Time</th>";
                echo "</tr>";

                while ($row = $result->fetch_assoc()) {
                    $riderQuery = "SELECT * FROM delivery_rider WHERE id = ?";
                    $riderStmt = $conn->prepare($riderQuery);
                    $riderStmt->bind_param("i", $row["rider_id"]);
                    $riderStmt->execute();
                    $riderResult = $riderStmt->get_result();

                    if ($riderResult->num_rows > 0) {
                        $riderRow = $riderResult->fetch_assoc();
                        $riderName = $riderRow["name"];
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

    <!-- Include your scripts if necessary -->
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
        $(document).ready(function() {
            const orderStatusDropdown = document.getElementById('orderStatus');
            const riderDropdown = document.getElementById('rider_dropdown');
            const form = document.querySelector('form'); // Assuming the form is the direct parent

            orderStatusDropdown.addEventListener('change', () => {
                if (orderStatusDropdown.value === 'in_transit') {
                    riderDropdown.style.display = 'block';
                    // Fetch rider data using AJAX and populate the dropdown
                    fetch('./adminconfig/getall_riders.php')
                        .then(response => response.json())
                        .then(data => {
                            const riderSelect = riderDropdown.querySelector('select');
                            riderSelect.innerHTML = '<option value="" selected disabled>Select Rider</option>';
                            data.forEach(rider => {
                                const option = document.createElement('option');
                                option.value = rider.id;
                                option.text = rider.name;
                                riderSelect.appendChild(option);
                            });
                        });
                } else {
                    riderDropdown.style.display = 'none';
                }
            });

            orderStatusDropdown.addEventListener('change', () => {
                if (orderStatusDropdown.value !== 'in_transit') {
                    riderDropdown.style.display = 'none';
                    riderDropdown.querySelector('select').removeAttribute('required');
                }
            });

            form.addEventListener('submit', (event) => {
                if (orderStatusDropdown.value === 'in_transit' && riderDropdown.querySelector('select').value === '') {
                    event.preventDefault(); // Prevent form submission
                    // Display an error message or handle the situation accordingly
                    alert('Please select a rider'); // Replace with appropriate error handling
                }
            });
        });
    </script>
</body>

</html>