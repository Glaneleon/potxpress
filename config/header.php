 <link rel="stylesheet" href="assets/styles/headerstyles.css">

 <nav class="navbar navbar-expand-lg navbar-light shadow-sm px-3" style="background-color: #FFD580;">
    <a class="navbar-brand" href="/">
        <img src="assets/images/potxpresslogo.png" alt="PotXpress Logo" width="50" height="50" class="d-inline-block">
        PotXpress | Pot Supplier Manila
    </a>
    <button class="btn btn-light d-lg-none" id="toggleSidebarBtn"><span><i class="fas fa-bars"></i></span></button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto"> <!-- Use ms-auto class here -->
            <?php if (isset($_SESSION['username'])) : ?>
                <li class="nav-item">
                    <span class="nav-link"><i class="fas fa-user"></i> Welcome, <?php echo $_SESSION["username"]; ?>!</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/orders.php"><i class="fas fa-parachute-box"></i> Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/config/logout.php" id="logout"><i class="fas fa-power-off"></i> Logout</a>
                </li>
            <?php else : ?>
                <li class="nav-item">
                    <a class="nav-link" href="/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/signup.php"><i class="fas fa-user-plus"></i> Sign Up</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
