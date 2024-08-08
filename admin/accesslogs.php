<?php require_once "./adminconfig/adminhead.php"; ?>

<div>
    <h2>Access Logs</h2>
    <?php
        include('../config/config.php');

        $accesslogQuery = "SELECT * FROM user_login_log";
        $accesslogResult = $conn->query($accesslogQuery);

        if (!$accesslogResult) {
            die("Error fetching orders: " . $conn->error);
        }
    ?>

        <table class="table table-bordered table-hover" id="accesslogs">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>User ID</th>
                    <th>Time</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($accesslog = $accesslogResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $accesslog['logID']; ?></td>
                        <td><?php echo $accesslog['userID']; ?></td>
                        <td><?php echo $accesslog['name']; ?></td>
                        <td><?php echo $accesslog['time']; ?></td>
                        <td><?php echo $accesslog['type']; ?></td>
                        <td><?php echo $accesslog['IPAddress']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
</div>

<?php require_once "./adminconfig/adminscript.php"; ?>