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

$notifsql = "SELECT COUNT(is_read) AS 'Unread Count' FROM order_notifications WHERE is_read = 0";
$notifstmt = $conn->prepare($notifsql);
$notifstmt->execute();
$notifresult = $notifstmt->get_result();
if ($notifresult->num_rows > 0) {
    $notifrow = $notifresult->fetch_assoc();
    if ($notifrow['Unread Count'] !== null) {
        $unreadCount = $notifrow['Unread Count'];
    }
}

$notifications = array();
$notifssql = "SELECT * FROM order_notifications ORDER BY created_at DESC";
$notifsstmt = $conn->prepare($notifssql);
$notifsstmt->execute();
$notifsresult = $notifsstmt->get_result();
if ($notifsresult->num_rows > 0) {
    while ($notifsrow = $notifsresult->fetch_assoc()) {
        $notifications[] = $notifsrow;
    }
} else {
    echo "No notifications found.";
}

function time_elapsed($datetime)
{
    $now = new DateTime();
    $past = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    $diff = $now->diff($past);

    $intervals = [
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second'
    ];

    $elapsed = '';
    foreach ($intervals as $key => $name) {
        $value = $diff->$key;
        if ($value > 0) {
            $elapsed .= $value . ' ' . $name . ($value > 1 ? 's' : '') . ' ';
        }
    }

    return $elapsed ? trim($elapsed) . ' ago' : 'just now';
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PotXpress Admin</title>
    <link rel="icon" href="../assets/icons/PotXpressicon1.png" type="image/icon type">
    <link rel="stylesheet" href="../assets/styles/styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dropdown-menu {
            position: relative;
        }

        .sticky-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
    </style>
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
                <button class="btn btn-secondary dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-bell"></i>
                    <?php if ($unreadCount !== 0) { ?>
                        <span class="badge bg-danger"><?= $unreadCount ?></span>
                    <?php } ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end px-1" aria-labelledby="notificationDropdown">
                    <h5>Notifications</h5>
                    <div  style="max-height: 300px; overflow-y: auto;">
                    <?php if (count($notifications) > 0) : ?>
                        <?php foreach ($notifications as $notification) : ?>
                            <li class="notification px-1 pt-1" data-id="<?= $notification['id'] ?>">
                                <div class="rounded-1 dropdown-item text-wrap mark-as-read <?php echo $notification['is_read'] ? 'bg-light text-dark border border-secondary' : 'bg-dark text-white'; ?>" style="width: 30rem;">
                                    <?= $notification['text']; ?>
                                    <div class="small text-end">
                                        <p class="mb-0"><?= $notification['created_at']; ?></p>
                                        <p class="mb-0"><?= time_elapsed($notification['created_at']); ?></p>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        
                    <?php endif; ?>
                    </div>
                    <li class="my-5"></li>
                    <li class="sticky-footer my-3 bg-white"><a class="dropdown-item" href="./notifications.php">See All Notifications</a></li>
                </ul>
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
                                Sales Reports
                            </a>
                            <a class="nav-link" aria-current="page" id="pdf-tab" href="receipts.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-receipt"></i></div>
                                Receipts
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