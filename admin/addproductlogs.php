<?php require_once "./adminconfig/adminhead.php"; ?>

<div>
    <h2>Add Product Logs</h2>
    <?php
        include('../config/config.php');

        $orderQuery = "SELECT product_add_log.*, users.fname FROM product_add_log JOIN users ON product_add_log.added_by_user_id = users.user_id;";
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
                    <th>Product Name</th>
                    <th>Added By</th>
                    <th>User IP Address</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orderResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $order['log_id']; ?></td>
                        <td><?php echo $order['product_id']; ?></td>
                        <td><?php echo $order['product_name']; ?></td>
                        <td><?php echo $order['fname']; ?></td>
                        <td><?php echo $order['ip_address']; ?></td>
                        <td><?php echo $order['added_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "./adminconfig/adminscript.php"; ?>

