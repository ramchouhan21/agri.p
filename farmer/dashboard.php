<?php
session_start();
// Only allow access if logged in as farmer, otherwise redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    header('Location: login.php');
    exit();
}
// Dashboard cleaned. No content.
                <h1>Welcome, <?php echo htmlspecialchars($farmer_details['full_name']); ?>!</h1>
                <p>Manage your crops and track your sales</p>
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
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_crops'] ?? 0; ?></h3>
                        <p>Total Crops</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['available_crops'] ?? 0; ?></h3>
                        <p>Available Crops</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['sold_crops'] ?? 0; ?></h3>
                        <p>Sold Crops</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>₹<?php echo number_format($stats['total_earnings'] ?? 0, 2); ?></h3>
                        <p>Total Earnings</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="add_crop.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add New Crop
                    </a>
                    <a href="view_status.php" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        View Approval Status
                    </a>
                    <a href="sales_history.php" class="btn btn-outline">
                        <i class="fas fa-chart-line"></i>
                        Sales History
                    </a>
                </div>
            </div>

            <div class="dashboard-content">
                <!-- Recent Crops -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Crops</h2>
                        <a href="add_crop.php" class="btn btn-outline btn-sm">Add New</a>
                    </div>
                    
                    <?php if (empty($recent_crops)): ?>
                        <div class="empty-state">
                            <i class="fas fa-seedling"></i>
                            <h3>No crops added yet</h3>
                            <p>Start by adding your first crop to begin selling</p>
                            <a href="add_crop.php" class="btn btn-primary">Add Your First Crop</a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Crop Name</th>
                                        <th>Variety</th>
                                        <th>Quantity</th>
                                        <th>Price/Unit</th>
                                        <th>Status</th>
                                        <th>Date Added</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_crops as $crop): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($crop['crop_name']); ?></td>
                                            <td><?php echo htmlspecialchars($crop['variety']); ?></td>
                                            <td><?php echo $crop['quantity'] . ' ' . $crop['unit']; ?></td>
                                            <td>₹<?php echo number_format($crop['price_per_unit'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $crop['status']; ?>">
                                                    <?php echo ucfirst($crop['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($crop['created_at'])); ?></td>
                                            <td>
                                                <a href="edit_crop.php?id=<?php echo $crop['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Orders -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Orders</h2>
                        <a href="sales_history.php" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    
                    <?php if (empty($recent_orders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>No orders yet</h3>
                            <p>Orders will appear here once buyers start purchasing your crops</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Crop</th>
                                        <th>Buyer</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td><?php echo htmlspecialchars($order['crop_name'] . ' (' . $order['variety'] . ')'); ?></td>
                                            <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                            <td><?php echo $order['quantity'] . ' kg'; ?></td>
                                            <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
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
