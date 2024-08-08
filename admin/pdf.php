<?php require_once "./adminconfig/adminhead.php"; ?>

<div>
    <h1 class="mb-5">Sales Reports</h1>

    <div class="row mb-4">
        <div class="col-md-10">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dailySalesReportModal">
                Generate Sales Report
            </button>
        </div>
    </div>
    <table id="reportsTable" class="table table-bordered table-hover" style="width:100%"></table>
</div>

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
                                    echo "<option value='" . $row['user_id'] . "'>" . $row['user_id'] . ' - ' . ucfirst($row['firstname']) . ' ' . ucfirst($row['lastname']) . "</option>";
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

<?php require_once "./adminconfig/adminscript.php"; ?>