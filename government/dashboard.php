<?php
session_start();
// Only allow access if logged in as government, otherwise redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'government') {
    header('Location: login.php');
    exit();
}
// Dashboard cleaned. No content.
    $pending_approvals = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error_message = "Error loading dashboard data";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Government Dashboard - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="dashboard-header">
                <h1>Government Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Manage approvals and monitor the platform.</p>
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
                        <h3><?php echo $total_farmers; ?></h3>
                        <p>Approved Farmers</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $total_buyers; ?></h3>
                        <p>Registered Buyers</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $pending_farmers; ?></h3>
                        <p>Pending Farmer Approvals</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $pending_crops; ?></h3>
                        <p>Pending Crop Approvals</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="approve_farmers.php" class="btn btn-primary">
                        <i class="fas fa-user-check"></i>
                        Approve Farmers
                    </a>
                    <a href="approve_prices.php" class="btn btn-secondary">
                        <i class="fas fa-chart-line"></i>
                        Set MSP Prices
                    </a>
                    <a href="reports.php" class="btn btn-outline">
                        <i class="fas fa-chart-bar"></i>
                        Generate Reports
                    </a>
                </div>
            </div>

            <div class="dashboard-content">
                <!-- Pending Approvals -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Pending Approvals</h2>
                        <a href="approve_farmers.php" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    
                    <?php if (empty($pending_approvals)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h3>No pending approvals</h3>
                            <p>All applications have been processed</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Applicant</th>
                                        <th>Contact</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_approvals as $approval): ?>
                                        <tr>
                                            <td>
                                                <span class="status-badge status-pending">
                                                    <?php echo ucfirst(str_replace('_', ' ', $approval['approval_type'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($approval['full_name']); ?></strong>
                                                <br><small><?php echo htmlspecialchars($approval['email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($approval['phone']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($approval['created_at'])); ?></td>
                                            <td>
                                                <a href="approve_farmers.php?id=<?php echo $approval['id']; ?>" class="btn btn-sm btn-outline">Review</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Approvals -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Approvals</h2>
                    </div>
                    
                    <?php if (empty($recent_approvals)): ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No recent approvals</h3>
                            <p>Your approval history will appear here</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Applicant</th>
                                        <th>Status</th>
                                        <th>Approved Date</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_approvals as $approval): ?>
                                        <tr>
                                            <td>
                                                <?php echo ucfirst(str_replace('_', ' ', $approval['approval_type'])); ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($approval['full_name']); ?></strong>
                                                <br><small><?php echo htmlspecialchars($approval['email']); ?></small>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $approval['status']; ?>">
                                                    <?php echo ucfirst($approval['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($approval['approved_at'])); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars(substr($approval['comments'] ?? '', 0, 50)) . (strlen($approval['comments'] ?? '') > 50 ? '...' : ''); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
