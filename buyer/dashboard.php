<?php
session_start();
// Only allow access if logged in as buyer, otherwise redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header('Location: login.php');
    exit();
}
// Dashboard cleaned. No content.
                <h1>Welcome, <?php echo htmlspecialchars($buyer_details['full_name']); ?>!</h1>
                <p>Browse fresh crops and manage your orders</p>
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
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_orders'] ?? 0; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['completed_orders'] ?? 0; ?></h3>
                        <p>Completed Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending_orders'] ?? 0; ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>₹<?php echo number_format($stats['total_spent'] ?? 0, 2); ?></h3>
                        <p>Total Spent</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="browse_crops.php" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Browse Crops
                    </a>
                    <a href="order_history.php" class="btn btn-secondary">
                        <i class="fas fa-history"></i>
                        Order History
                    </a>
                    <a href="browse_crops.php?featured=1" class="btn btn-outline">
                        <i class="fas fa-star"></i>
                        Featured Crops
                    </a>
                </div>
            </div>

            <div class="dashboard-content">
                <!-- Available Crops Summary -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Available Crops</h2>
                        <a href="browse_crops.php" class="btn btn-outline btn-sm">Browse All</a>
                    </div>
                    
                    <div class="crop-summary">
                        <div class="summary-card">
                            <i class="fas fa-seedling"></i>
                            <div>
                                <h3><?php echo $available_crops; ?> Crops Available</h3>
                                <p>Fresh crops from verified farmers across the region</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Orders</h2>
                        <a href="order_history.php" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    
                    <?php if (empty($recent_orders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>No orders yet</h3>
                            <p>Start browsing crops and place your first order</p>
                            <a href="browse_crops.php" class="btn btn-primary">Browse Crops</a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Crop</th>
                                        <th>Farmer</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['crop_name']); ?></strong>
                                                <?php if ($order['variety']): ?>
                                                    <br><small><?php echo htmlspecialchars($order['variety']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['farmer_name']); ?></strong>
                                                <br><small><?php echo htmlspecialchars($order['farmer_phone']); ?></small>
                                            </td>
                                            <td><?php echo $order['quantity'] . ' kg'; ?></td>
                                            <td><strong>₹<?php echo number_format($order['total_price'], 2); ?></strong></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                    View Details
                                                </button>
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

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Order Details</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body" id="orderDetails">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script>
        // Modal functionality
        const modal = document.getElementById('orderModal');
        const closeBtn = document.querySelector('.close');
        
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        function viewOrderDetails(orderId) {
            // In a real application, you would fetch order details via AJAX
            document.getElementById('orderDetails').innerHTML = `
                <p>Loading order details...</p>
                <p>Order ID: ${orderId}</p>
                <p>This would show detailed information about the order, farmer contact details, delivery information, etc.</p>
            `;
            modal.style.display = 'block';
        }
    </script>
</body>
</html>
