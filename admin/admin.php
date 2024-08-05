<?php require_once "./adminconfig/adminhead.php"; ?>

<div>
    <h1 class="my-4">Dashboard</h1>

    <?php include_once("./adminconfig/dashboard_data.php"); ?>
    <!-- dashboard charts -->
    <div class="row">
        <div class="col-md-3">
            <div class="card border-secondary mb-3" style="max-width: 28rem;">
                <div class="card-header">Products</div>
                <div class="card-body text-secondary">
                    <h5 class="card-title"><?= $products ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary mb-3" style="max-width: 28rem;">
                <div class="card-header">Total Amount Sold</div>
                <div class="card-body text-secondary">
                    <h5 class="card-title">₱ <?= $sales ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger mb-3" style="max-width: 28rem;">
                <div class="card-header">Out of Stock Products</div>
                <div class="card-body text-danger">
                    <h5 class="card-title"><?= $outOfStock ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning mb-3" style="max-width: 28rem;">
                <div class="card-header">Low-Stock Products</div>
                <div class="card-body text-warning">
                    <h5 class="card-title"><?= $lowStock ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Orders Per Month
                </div>
                <div class="card-body"><canvas id="OrdersChart" height="100%"></canvas></div>
            </div>
        </div>
    </div>
    <!-- /dashboard charts -->

</div>

<?php require_once "./adminconfig/adminscript.php"; ?>
