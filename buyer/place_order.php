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
$crop_id = $_GET['id'] ?? '';

if (!$crop_id) {
    header('Location: browse_crops.php');
    exit();
}

$message = '';
$message_type = '';

try {
    $pdo = getDBConnection();
    
    // Get crop details
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name as farmer_name, u.phone as farmer_phone, u.email as farmer_email, u.city, u.state
        FROM crops c
        JOIN users u ON c.farmer_id = u.id
        WHERE c.id = ? AND c.status = 'available' AND u.status = 'approved'
    ");
    $stmt->execute([$crop_id]);
    $crop = $stmt->fetch();
    
    if (!$crop) {
        header('Location: browse_crops.php');
        exit();
    }
    
} catch (Exception $e) {
    $error_message = "Error loading crop data";
}

if ($_POST) {
    $quantity = $_POST['quantity'] ?? '';
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'bank_transfer';
    $notes = trim($_POST['notes'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($quantity) || $quantity <= 0) {
        $errors[] = 'Please enter a valid quantity';
    } elseif ($quantity > $crop['quantity']) {
        $errors[] = 'Quantity cannot exceed available amount (' . $crop['quantity'] . ' ' . $crop['unit'] . ')';
    }
    
    if (empty($delivery_address)) {
        $errors[] = 'Delivery address is required';
    }
    
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Calculate total price
            $total_price = $quantity * $crop['price_per_unit'];
            
            // Create order
            $stmt = $pdo->prepare("
                INSERT INTO orders (buyer_id, crop_id, quantity, unit_price, total_price, delivery_address, payment_method, notes, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$buyer_id, $crop_id, $quantity, $crop['price_per_unit'], $total_price, $delivery_address, $payment_method, $notes]);
            $order_id = $pdo->lastInsertId();
            
            // Update crop quantity
            $new_quantity = $crop['quantity'] - $quantity;
            if ($new_quantity <= 0) {
                $stmt = $pdo->prepare("UPDATE crops SET status = 'sold' WHERE id = ?");
                $stmt->execute([$crop_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE crops SET quantity = ? WHERE id = ?");
                $stmt->execute([$new_quantity, $crop_id]);
            }
            
            $message = 'Order placed successfully! Order ID: ' . $order_id;
            $message_type = 'success';
            
            // Redirect to order history after 3 seconds
            header("refresh:3;url=order_history.php");
            
        } catch (Exception $e) {
            $message = 'Failed to place order. Please try again.';
            $message_type = 'error';
        }
    }
    
    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Place Order</h1>
                <p>Complete your purchase of fresh crops</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="order-container">
                <!-- Crop Details -->
                <div class="crop-details-section">
                    <h2>Crop Details</h2>
                    <div class="crop-info-card">
                        <?php if ($crop['image_url']): ?>
                            <div class="crop-image">
                                <img src="../<?php echo htmlspecialchars($crop['image_url']); ?>" alt="<?php echo htmlspecialchars($crop['crop_name']); ?>">
                            </div>
                        <?php else: ?>
                            <div class="crop-image placeholder">
                                <i class="fas fa-seedling"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="crop-info">
                            <h3><?php echo htmlspecialchars($crop['crop_name']); ?></h3>
                            <?php if ($crop['variety']): ?>
                                <p class="variety"><?php echo htmlspecialchars($crop['variety']); ?></p>
                            <?php endif; ?>
                            
                            <div class="crop-specs">
                                <div class="spec-item">
                                    <i class="fas fa-weight"></i>
                                    <span>Available: <?php echo $crop['quantity'] . ' ' . $crop['unit']; ?></span>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-rupee-sign"></i>
                                    <span>Price: ₹<?php echo number_format($crop['price_per_unit'], 2); ?>/<?php echo $crop['unit']; ?></span>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-star"></i>
                                    <span>Quality: <?php echo $crop['quality_grade']; ?></span>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Harvested: <?php echo date('M d, Y', strtotime($crop['harvest_date'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="farmer-info">
                                <h4>Farmer Information</h4>
                                <p><strong><?php echo htmlspecialchars($crop['farmer_name']); ?></strong></p>
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($crop['farmer_phone']); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($crop['city'] . ', ' . $crop['state']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Form -->
                <div class="order-form-section">
                    <h2>Order Information</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="quantity">Quantity (<?php echo $crop['unit']; ?>) *</label>
                            <input type="number" id="quantity" name="quantity" step="0.01" min="0.01" max="<?php echo $crop['quantity']; ?>" required>
                            <small>Maximum available: <?php echo $crop['quantity'] . ' ' . $crop['unit']; ?></small>
                        </div>
                        
                        <div class="form-group">
                            <label for="delivery_address">Delivery Address *</label>
                            <textarea id="delivery_address" name="delivery_address" rows="4" required placeholder="Enter complete delivery address"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select id="payment_method" name="payment_method">
                                <option value="bank_transfer" <?php echo ($_POST['payment_method'] ?? 'bank_transfer') === 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                <option value="upi" <?php echo ($_POST['payment_method'] ?? '') === 'upi' ? 'selected' : ''; ?>>UPI</option>
                                <option value="cash" <?php echo ($_POST['payment_method'] ?? '') === 'cash' ? 'selected' : ''; ?>>Cash on Delivery</option>
                                <option value="card" <?php echo ($_POST['payment_method'] ?? '') === 'card' ? 'selected' : ''; ?>>Credit/Debit Card</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Special Instructions (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Any special delivery instructions or requirements"></textarea>
                        </div>
                        
                        <div class="order-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-item">
                                <span>Price per <?php echo $crop['unit']; ?>:</span>
                                <span>₹<?php echo number_format($crop['price_per_unit'], 2); ?></span>
                            </div>
                            <div class="summary-item">
                                <span>Quantity:</span>
                                <span id="summary-quantity">0 <?php echo $crop['unit']; ?></span>
                            </div>
                            <div class="summary-item total">
                                <span>Total Amount:</span>
                                <span id="summary-total">₹0.00</span>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-full">Place Order</button>
                            <a href="browse_crops.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script>
        // Calculate order total
        const quantityInput = document.getElementById('quantity');
        const pricePerUnit = <?php echo $crop['price_per_unit']; ?>;
        const unit = '<?php echo $crop['unit']; ?>';
        
        function updateOrderSummary() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const total = quantity * pricePerUnit;
            
            document.getElementById('summary-quantity').textContent = quantity + ' ' + unit;
            document.getElementById('summary-total').textContent = '₹' + total.toFixed(2);
        }
        
        quantityInput.addEventListener('input', updateOrderSummary);
        
        // Initialize summary
        updateOrderSummary();
    </script>
</body>
</html>
