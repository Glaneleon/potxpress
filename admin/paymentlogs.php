<?php require_once "./adminconfig/adminhead.php"; ?>

<div>
    <h2>Payments Logs</h2>
    <?php
        include('../config/config.php');

        $orderQuery = "SELECT * FROM payment_log";
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
                    <th>Type</th>
                    <th>Order ID</th>
                    <th>Amount</th>
                    <th>User IP Address</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orderResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['type']; ?></td>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['amount']; ?></td>
                        <td><?php echo $order['ip_address']; ?></td>
                        <td><?php echo $order['date']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "./adminconfig/adminscript.php"; ?>
