<div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
    <h2>Orders</h2>

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
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Order Date</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>