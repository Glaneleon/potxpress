<div class="tab-pane fade" id="addproductstocklog" role="tabpanel" aria-labelledby="addproductstocklog-tab">
    <h2>Add Product Stock Logs</h2>
    <?php
        include('../config/config.php');

        $orderQuery = "SELECT inventory_log.*, users.fname FROM inventory_log JOIN users ON inventory_log.user_id = users.user_id;";
        $orderResult = $conn->query($orderQuery);

        if (!$orderResult) {
            die("Error fetching orders: " . $conn->error);
        }
    ?>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Product ID</th>
                    <th>Quantity Added</th>
                    <th>Stocks Added By</th>
                    <th>User IP Address</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orderResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $order['inventorylog_id']; ?></td>
                        <td><?php echo $order['product_id']; ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td><?php echo $order['fname']; ?></td>
                        <td><?php echo $order['ip_address']; ?></td>
                        <td><?php echo $order['datetime']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
