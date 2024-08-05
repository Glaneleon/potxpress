<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    include('../config/config.php');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
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
    <link rel="icon" href="../assets/images/potsuppliermanila.jpg" type="image/icon type">
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
            <a class="navbar-brand ps-3" href="">Admin Panel</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-secondary btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="!"><i class="fas fa-bars"></i></button>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
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
                            <a class="nav-link" aria-current="page" id="dashboard-tab" href="admin.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link" aria-current="page" id="accounts-tab" href="accounts.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Accounts
                            </a>
                            <a class="nav-link" aria-current="page" id="category-tab" href="category.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-layer-group"></i></div>
                                Category
                            </a>
                            <a class="nav-link" aria-current="page" id="products-tab" href="products.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-dollar-sign"></i></div>
                                Products
                            </a>
                            <a class="nav-link" aria-current="page" id="customers-tab" href="customers.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div>
                                Customers
                            </a>
                            <a class="nav-link" aria-current="page" id="orders-tab" href="orders.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-file"></i></div>
                                Orders
                            </a>
                            <a class="nav-link" aria-current="page" id="pdf-tab" href="pdf.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-file-pdf"></i></div>
                                PDFs
                            </a>
                            <a class="nav-link" aria-current="page" id="riders-tab" href="riders.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-motorcycle"></i></div>
                                Delivery Riders
                            </a>
                            <a id="logs" class="nav-link collapsed" href="" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Logs
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link" aria-current="page" id="payments-tab" href="paymentlogs.php">
                                        Payment Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="addproductlog-tab" href="addproductlogs.php">
                                        Add Product Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="addproductstocklog-tab" href="addproductstocklogs.php">
                                        Add Product Stock Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="deleteproductlog-tab" href="deleteproductlogs.php">
                                        Delete Product Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="editproductlog-tab" href="editproductlogs.php">
                                        Edit Product Log
                                    </a>
                                    <a class="nav-link" aria-current="page" id="accesslog-tab" href="accesslogs.php">
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