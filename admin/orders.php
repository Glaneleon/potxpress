<div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
    <h1>Orders</h1>
    <div class="row mb-4">
        <div class="col-md-10">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dailySalesReportModal">
                Generate Sales Report
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6"><label for="min_date">Start Date:</label>
            <input type="date" class="form-control" id="min_date" name="min_date">
        </div>
        <div class="col-md-6"><label for="max_date">End Date:</label>
            <input type="date" class="form-control" id="max_date" name="max_date">
        </div>
    </div>

    <table class="table table-bordered table-hover" id="ordersTable" style="width:100%">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Customer Name</th>
                <th>Order Date</th>
                <th>Total Amount</th>
                <th>Payment</th>
                <th>Payment Mode</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>

    <!-- generate report modal -->
    <div class="modal fade" id="dailySalesReportModal" tabindex="-1" aria-labelledby="dailySalesReportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dailySalesReportModalLabel">Generate Sales Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="./adminconfig/dailysalesreport.php">
                        <div class="mb-2">
                            <label for="date" class="form-label">Start Date:</label>
                            <input type="date" name="date" id="date" class="form-control mb-4" required>
                        </div>
                        <div class="mb-2">
                            <label for="endDate" class="form-label">End Date:</label>
                            <input type="date" name="endDate" id="endDate" class="form-control mb-4">
                        </div>
                        <!-- <div class="mb-2">
                            <label for="potCategory" class="form-label">Product Category:</label>
                            <select name="potCategory" id="potCategory" class="form-select mb-4">
                                <option selected disabled value="">Select a category..</option>
                            </select>
                        </div> -->
                        <div class="mb-2">
                            <label for="customerId" class="form-label">Customer Name:</label>
                            <p class="small text-muted">Only customers with existing records can be chosen.</p>

                            <?php
                            $sql = "SELECT DISTINCT uers_test.user_id, uers_test.firstname, uers_test.lastname 
                                    FROM orders 
                                    INNER JOIN uers_test 
                                    ON orders.user_id = uers_test.user_id";

                            $result = $conn->query($sql);
                            ?>

                            <select name="customerId" id="customerId" class="form-select mb-4">
                                <option selected disabled value="">Select a customer..</option>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['user_id'] . "'>". $row['user_id'] .' - '. ucfirst($row['firstname']) . ' ' . ucfirst($row['lastname']) . "</option>";
                                    }
                                }
                                ?>
                            </select>

                            <?php
                            $conn->close();
                            ?>

                        </div>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /generate report modal -->
</div>