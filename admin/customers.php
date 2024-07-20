<div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="customers-tab">
    <h2>Customers</h2>
    <?php
        // Sample code to retrieve and display customer data
        include('../config/config.php');
        

        // $customerQuery = "SELECT * FROM users WHERE role = 'customer'";
        $customerQuery = "SELECT * FROM uers_test";
        $customerResult = $conn->query($customerQuery);

        if (!$customerResult) {
            die("Error fetching customer accounts: " . $conn->error);
        }
    ?>

    <div class="table-responsive">
        <table class="table table-bordered" id="customertable">
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Password</th>
                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>
                <?php while ($customer = $customerResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $customer['user_id']; ?></td>
                        <td><?php echo $customer['firstname'] . " " . $customer['lastname']; ?></td>
                        <td><?php echo $customer['email']; ?></td>
                        <td><?php echo $customer['phone_no']; ?></td>
                        <!-- <td>  <button class="btn btn-danger" id="deleteButton<?php// echo $customer['user_id']; ?>">Delete</button></td> -->
                        <!-- Add more columns as needed -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>