<?php require_once "./adminconfig/adminhead.php"; ?>

<div>
    <h2>Edit Product Logs</h2>
    <?php
        include('../config/config.php');

        $orderQuery = "SELECT product_edit_log.*, users.fname FROM product_edit_log JOIN users ON product_edit_log.edited_by_user_id = users.user_id;";
        $orderResult = $conn->query($orderQuery);

        if (!$orderResult) {
            die("Error fetching orders: " . $conn->error);
        }
    ?>

        <table class="table table-bordered table-hover" id="editproductlogs">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Product ID</th>
                    <th>Edited By</th>
                    <th>User IP Address</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orderResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $order['log_id']; ?></td>
                        <td><?php echo $order['product_id']; ?></td>
                        <td><?php echo $order['fname']; ?></td>
                        <td><?php echo $order['ip_address']; ?></td>
                        <td><?php echo $order['edited_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
</div>
<?php require_once "./adminconfig/adminscript.php"; ?>
