<?php
session_start();
include '../includes/language.php';
include '../config/db.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Get system statistics
    $stats = [];
    
    // Total users by type
    $stmt = $pdo->prepare("SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type");
    $stmt->execute();
    $user_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Total crops
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_crops FROM crops");
    $stmt->execute();
    $stats['total_crops'] = $stmt->fetch()['total_crops'];
    
    // Total orders
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders");
    $stmt->execute();
    $stats['total_orders'] = $stmt->fetch()['total_orders'];
    
    // Total revenue
    $stmt = $pdo->prepare("SELECT SUM(total_price) as total_revenue FROM orders WHERE status = 'delivered'");
    $stmt->execute();
    $stats['total_revenue'] = $stmt->fetch()['total_revenue'] ?? 0;
    
    // Pending approvals
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_approvals FROM government_approvals WHERE status = 'pending'");
    $stmt->execute();
    $stats['pending_approvals'] = $stmt->fetch()['pending_approvals'];
    
    // Recent activities
    $stmt = $pdo->prepare("
        SELECT 'user_registration' as type, u.full_name, u.user_type, u.created_at
        FROM users u
        WHERE u.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        UNION ALL
        SELECT 'crop_listing' as type, CONCAT(u.full_name, ' - ', c.crop_name) as full_name, 'crop' as user_type, c.created_at
        FROM crops c
        JOIN users u ON c.farmer_id = u.id
        WHERE c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        UNION ALL
        SELECT 'order_placed' as type, CONCAT(u.full_name, ' - Order #', o.order_number) as full_name, 'order' as user_type, o.order_date as created_at
        FROM orders o
        JOIN users u ON o.buyer_id = u.id
        WHERE o.order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recent_activities = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error_message = "Error loading dashboard data";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Monitor and manage the platform.</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo ($user_counts['farmer'] ?? 0) + ($user_counts['buyer'] ?? 0); ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_crops']; ?></h3>
                        <p>Total Crops</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>₹<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
            </div>

            <!-- User Breakdown -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $user_counts['farmer'] ?? 0; ?></h3>
                        <p>Farmers</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $user_counts['buyer'] ?? 0; ?></h3>
                        <p>Buyers</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $user_counts['government'] ?? 0; ?></h3>
                        <p>Government Officials</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending_approvals']; ?></h3>
                        <p>Pending Approvals</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="manage_users.php" class="btn btn-primary">
                        <i class="fas fa-users-cog"></i>
                        Manage Users
                    </a>
                    <a href="disputes.php" class="btn btn-secondary">
                        <i class="fas fa-gavel"></i>
                        Handle Disputes
                    </a>
                    <a href="analytics.php" class="btn btn-outline">
                        <i class="fas fa-chart-line"></i>
                        View Analytics
                    </a>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2>Recent Activities</h2>
                </div>
                
                <?php if (empty($recent_activities)): ?>
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <h3>No recent activities</h3>
                        <p>Platform activities will appear here</p>
                    </div>
                <?php else: ?>
                    <div class="activities-list">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <?php if ($activity['type'] === 'user_registration'): ?>
                                        <i class="fas fa-user-plus"></i>
                                    <?php elseif ($activity['type'] === 'crop_listing'): ?>
                                        <i class="fas fa-seedling"></i>
                                    <?php else: ?>
                                        <i class="fas fa-shopping-cart"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="activity-content">
                                    <h4><?php echo htmlspecialchars($activity['full_name']); ?></h4>
                                    <p>
                                        <?php if ($activity['type'] === 'user_registration'): ?>
                                            New <?php echo $activity['user_type']; ?> registration
                                        <?php elseif ($activity['type'] === 'crop_listing'): ?>
                                            New crop listing added
                                        <?php else: ?>
                                            New order placed
                                        <?php endif; ?>
                                    </p>
                                    <small><?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
