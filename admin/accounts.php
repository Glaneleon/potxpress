<?php require_once "./adminconfig/adminhead.php"; ?>

<div>
    <h2>Accounts</h2>

    <?php
        $accountQuery = "SELECT * FROM users";
        $accountResult = $conn->query($accountQuery);

        if (!$accountResult) {
            die("Error fetching accounts: " . $conn->error);
        }
    ?>

    <div class="table-responsive">
        <table class="table" id="accountstable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Mobile Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($account = $accountResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $account['username']; ?></td>
                        <td><?php echo $account['fname'].' '.$account['lname']; ?></td>
                        <td><?php echo $account['email']; ?></td>
                        <td><?php echo $account['role']; ?></td>
                        <td><?php echo $account['mobile_number']; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editAccountModal<?php echo $account['user_id']; ?>">
                                Edit
                            </button>
                            <button class="btn btn-danger" id="deleteButton<?php echo $account['user_id']; ?>">Delete</button>
                        </td>

            <!-- edit account modal -->
            <div class="modal fade" id="editAccountModal<?php echo $account['user_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editAccountModalLabel">Edit Account #<?php echo $account['user_id']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editAccountForm<?php echo $account['user_id']; ?>" enctype="multipart/form-data">
                                <input type="hidden" id="editAccountID" name="editAccountID" value="<?php echo $account['user_id']; ?>">

                                <div class="form-group mb-3">
                                    <label for="accountName">First Name:</label>
                                    <input type="text" class="form-control" id="accountFName" name="accountFName" value="<?php echo $account['fname']; ?>" required>
                                    <label for="accountName">Last Name:</label>
                                    <input type="text" class="form-control" id="accountLName" name="accountLName" value="<?php echo $account['lname']; ?>" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="accountName">Role:</label>
                                    <select class="form-control" id="accountRole" name="accountRole" required>
                                        <option value="customer" <?php if($account['role'] === 'customer') echo 'selected'; ?>>Customer</option>
                                        <option value="admin" <?php if($account['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3 row">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" name="email" id="email" class="form-control" value="<?php echo $account['email']; ?>" required>
                                        <small class="form-text text-muted">Please enter a valid email address.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="mobile_number" class="form-label">Mobile Number:</label>
                                        <input type="tel" name="mobile_number" class="form-control" pattern="[0-9]{11}" value="<?php echo $account['mobile_number']; ?>" required>
                                        <small class="form-text text-muted">Please enter a valid 11-digit mobile number starting with a zero.</small>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <div class="col-md-4">
                                        <label for="accountName">Username:</label>
                                        <input type="text" class="form-control" id="accountusername" name="accountusername" value="<?php echo $account['username']; ?>" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>         
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "./adminconfig/adminscript.php"; ?>