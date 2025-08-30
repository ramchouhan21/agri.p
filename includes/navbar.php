<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="index.php">
                <i class="fas fa-seedling"></i>
                <span>Smart Agriculture</span>
            </a>
        </div>
        
        <div class="nav-menu" id="nav-menu">
            <a href="index.php" class="nav-link"><?php echo $lang['home_title']; ?></a>
            <a href="about.php" class="nav-link"><?php echo $lang['about_title']; ?></a>
            <a href="contact.php" class="nav-link"><?php echo $lang['contact_title']; ?></a>
            
            <!-- Language Selector -->
            <div class="language-selector">
                <select onchange="changeLanguage(this.value)">
                    <?php foreach ($languages as $code => $name): ?>
                        <option value="<?php echo $code; ?>" <?php echo $current_lang === $code ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- User Menu -->
            <div class="user-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-dropdown">
                        <button class="user-btn">
                            <i class="fas fa-user"></i>
                            <?php echo $_SESSION['full_name']; ?>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <?php if ($_SESSION['user_type'] === 'farmer'): ?>
                                <a href="farmer/dashboard.php">Dashboard</a>
                                <a href="farmer/add_crop.php">Add Crop</a>
                                <a href="farmer/view_status.php">View Status</a>
                                <a href="farmer/sales_history.php">Sales History</a>
                            <?php elseif ($_SESSION['user_type'] === 'buyer'): ?>
                                <a href="buyer/dashboard.php">Dashboard</a>
                                <a href="buyer/browse_crops.php">Browse Crops</a>
                                <a href="buyer/order_history.php">Order History</a>
                            <?php elseif ($_SESSION['user_type'] === 'government'): ?>
                                <a href="government/dashboard.php">Dashboard</a>
                                <a href="government/approve_farmers.php">Approve Farmers</a>
                                <a href="government/approve_prices.php">Approve Prices</a>
                                <a href="government/reports.php">Reports</a>
                            <?php elseif ($_SESSION['user_type'] === 'admin'): ?>
                                <a href="admin/dashboard.php">Dashboard</a>
                                <a href="admin/manage_users.php">Manage Users</a>
                                <a href="admin/disputes.php">Disputes</a>
                                <a href="admin/analytics.php">Analytics</a>
                            <?php endif; ?>
                            <hr>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="farmer/login.php" class="btn btn-outline">Farmer Login</a>
                    <a href="buyer/login.php" class="btn btn-outline">Buyer Login</a>
                    <a href="government/login.php" class="btn btn-outline">Government</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="nav-toggle" id="nav-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>
</nav>

<script>
function changeLanguage(lang) {
    window.location.href = '?lang=' + lang;
}

// Mobile menu toggle
document.getElementById('nav-toggle').addEventListener('click', function() {
    document.getElementById('nav-menu').classList.toggle('active');
});

// User dropdown toggle
document.querySelector('.user-btn')?.addEventListener('click', function() {
    document.querySelector('.dropdown-menu').classList.toggle('show');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-dropdown')) {
        document.querySelector('.dropdown-menu')?.classList.remove('show');
    }
});
</script>
