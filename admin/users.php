<div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
    <h2>Users</h2>
    <?php
        // Sample code to retrieve and display customer data
        include('../config/config.php');

        $customerQuery = "SELECT * FROM users";
        $customerResult = $conn->query($customerQuery);

        if (!$customerResult) {
            die("Error fetching customer accounts: " . $conn->error);
        }
    ?>

    <div class="table-responsive">  
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Action</th>
                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>
                <?php while ($customer = $customerResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $customer['user_id']; ?></td>
                        <td><?php echo $customer['fname'] . " " . $customer['lname']; ?></td>
                        <td><?php echo $customer['email']; ?></td>
                        <td><?php echo $customer['mobile_number']; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#usertoadmin<?php echo $customer['user_id']; ?>">
                                Grant Admin Access
                            </button>
                        </td>
                        <!-- Add more columns as needed -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>