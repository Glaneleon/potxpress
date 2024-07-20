<?php
session_start();
include('../config/config.php');
// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PotXpress Admin</title>
    <link rel="icon" href="/assets/images/potsuppliermanila.jpg" type="image/icon type">
    <link rel="stylesheet" href="../assets/styles/styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="sb-nav-fixed" style="font-family: Dosis;">
    <div class="container-fluid">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="./admin.php">Admin Panel</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-secondary btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="text-decoration-none text-danger dropdown-item" href="../config/logout.php"><i class="fas fa-power-off mx-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" aria-current="page" id="dashboard-tab" href="#dashboard">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link" aria-current="page" id="accounts-tab" href="#accounts">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Accounts
                            </a>
                            <a class="nav-link" aria-current="page" id="category-tab" href="#category">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-layer-group"></i></div>
                                Category
                            </a>
                            <a class="nav-link" aria-current="page" id="products-tab" href="#products">
                                <div class="sb-nav-link-icon"><i class="fas fa-dollar-sign"></i></div>
                                Products
                            </a>
                            <a class="nav-link" aria-current="page" id="customers-tab" href="#customers">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div>
                                Customers
                            </a>
                            <a class="nav-link" aria-current="page" id="orders-tab" href="#orders">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-file"></i></div>
                                Orders
                            </a>
                            <a class="nav-link" aria-current="page" id="pdf-tab" href="#pdf">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-file-pdf"></i></div>
                                PDF Reports
                            </a>
                            <a class="nav-link" aria-current="page" id="riders-tab" href="#riders">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-motorcycle"></i></div>
                                Delivery Riders
                            </a>
                            <a id="logs" class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Logs
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link" aria-current="page" id="orders-tab" href="#orders">
                                        Order Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="addproductlog-tab" href="#addproductlog">
                                        Add Product Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="addproductstocklog-tab" href="#addproductstocklog">
                                        Add Product Stock Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="deleteproductlog-tab" href="#deleteproductlog">
                                        Delete Product Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="editproductlog-tab" href="#editproductlog">
                                        Edit Product Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="accesslog-tab" href="#accesslog">
                                        Access Log
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?php echo $_SESSION["userfname"];
                        echo ' (USERID: ';
                        echo $_SESSION["user_id"] . ')'; ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main role="main" class="px-4">
                    <div id="content" class="tab-content mt-3">
                        <!-- admin dashboard -->
                        <div class="tab-pane fade" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                            <h1 class="mt-4">Dashboard</h1>
                            <div class="row mb-4">
                                <div class="col-md-10">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dailySalesReportModal">
                                        Generate Daily Sales Report
                                    </button>
                                </div>
                            </div>

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

                            <!-- generate report modal -->
                            <div class="modal fade" id="dailySalesReportModal" tabindex="-1" aria-labelledby="dailySalesReportModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="dailySalesReportModalLabel">Daily Sales Report</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="./adminconfig/dailysalesreport.php">
                                                <label for="date">Select Date:</label>
                                                <input type="date" name="date" id="date" required>
                                                <br><br>
                                                <button type="submit" class="btn btn-primary">Generate Report</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /generate report modal -->

                        </div>
                        <!-- other tabs -->
                        <?php
                        require_once "addproductlogs.php";
                        require_once "editproductlogs.php";
                        require_once "deleteproductlogs.php";
                        require_once "products.php";
                        require_once "category.php";
                        require_once "customers.php";
                        require_once "orders.php";
                        require_once "addproductstocklogs.php";
                        require_once "accesslogs.php";
                        require_once "accounts.php";
                        require_once "pdf.php";
                        require_once "riders.php";
                        ?>

                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; PotXpress 2024</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

    </div>

    <!-- orderspermonthchart -->
    <script src="./adminconfig/graphs/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>

    <?php include "./adminconfig/adminscript.php"; ?>

    <script>
        $(document).ready(function() {
            $('#productstable').DataTable();
        });
    </script>

    <!-- <script>
        $(document).ready( function () {
            $('#category_table').DataTable();
        } );
    </script> -->



</body>

</html>