<?php
session_start();
include '../includes/language.php';
include '../config/db.php';

// Check if user is logged in as buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header('Location: login.php');
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

try {
    $pdo = getDBConnection();
    
    // Build query with filters
    $where_conditions = ["o.buyer_id = ?"];
    $params = [$buyer_id];
    
    if ($status_filter) {
        $where_conditions[] = "o.status = ?";
        $params[] = $status_filter;
    }
    
    if ($date_from) {
        $where_conditions[] = "o.order_date >= ?";
        $params[] = $date_from;
    }
    
    if ($date_to) {
        $where_conditions[] = "o.order_date <= ?";
        $params[] = $date_to . ' 23:59:59';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get orders
    $stmt = $pdo->prepare("
        SELECT o.*, c.crop_name, c.variety, c.quality_grade, u.full_name as farmer_name, u.phone as farmer_phone, u.email as farmer_email
        FROM orders o
        JOIN crops c ON o.crop_id = c.id
        JOIN users u ON c.farmer_id = u.id
        WHERE $where_clause
        ORDER BY o.order_date DESC
    ");
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
    
    // Get order summary
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed_orders,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status = 'delivered' THEN total_price ELSE 0 END) as total_spent,
            AVG(CASE WHEN status = 'delivered' THEN total_price ELSE NULL END) as avg_order_value
        FROM orders
        WHERE buyer_id = ?
    ");
    $stmt->execute([$buyer_id]);
    $summary = $stmt->fetch();
    
} catch (Exception $e) {
    $error_message = "Error loading order data";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Order History</h1>
                <p>Track all your crop orders and purchases</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Order Summary -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $summary['total_orders'] ?? 0; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $summary['completed_orders'] ?? 0; ?></h3>
                        <p>Completed Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $summary['pending_orders'] ?? 0; ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>₹<?php echo number_format($summary['total_spent'] ?? 0, 2); ?></h3>
                        <p>Total Spent</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-section">
                <h2>Filter Orders</h2>
                <form method="GET" class="filter-form">
                    <div class="filter-row">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="date_from">From Date</label>
                            <input type="date" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="date_to">To Date</label>
                            <input type="date" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="order_history.php" class="btn btn-outline">Clear</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Orders Table -->
            <div class="orders-section">
                <h2>Order History</h2>
                
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>No orders found</h3>
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
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($order['crop_name']); ?></strong>
                                            <?php if ($order['variety']): ?>
                                                <br><small><?php echo htmlspecialchars($order['variety']); ?></small>
                                            <?php endif; ?>
                                            <br><span class="quality-badge quality-<?php echo strtolower($order['quality_grade']); ?>">
                                                <?php echo $order['quality_grade']; ?>
                                            </span>
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
                <p>This would show detailed information about the order, farmer contact details, delivery information, payment status, etc.</p>
            `;
            modal.style.display = 'block';
        }
    </script>
</body>
</html>
