<div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
    <h2>Orders</h2>
    <?php
        include('../config/config.php');

        $orderQuery = "SELECT orders.*, uers_test.firstname, uers_test.lastname FROM orders INNER JOIN uers_test ON orders.user_id = uers_test.user_id";
        $orderResult = $conn->query($orderQuery);

        if (!$orderResult) {
            die("Error fetching orders: " . $conn->error);
        }
    ?>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orderResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['firstname'] . " " . $order['lastname']; ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td><?php echo '₱' . number_format($order['total_amount'], 2); ?></td>
                        <?php 
                            echo '<td>';
                              switch ($order['status']) {
                                  case 1:
                                      echo "Order Placed";
                                      break;
                                  case 2:
                                      echo "In Transit";
                                      break;
                                  case 3:
                                      echo "Delivered";
                                      break;
                                  default:
                                      echo "Unknown";
                                      break;
                              }
                            echo '</td>';
                        ?>
                        <td>
                            <a href="view_order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-info">View Details</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>