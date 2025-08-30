<?php
session_start();
include '../includes/language.php';
include '../config/db.php';

$message = '';
$message_type = '';

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    
    // Buyer-specific fields
    $business_name = trim($_POST['business_name'] ?? '');
    $business_type = $_POST['business_type'] ?? 'retailer';
    $gst_number = trim($_POST['gst_number'] ?? '');
    $license_number = trim($_POST['license_number'] ?? '');
    $preferred_crops = trim($_POST['preferred_crops'] ?? '');
    $max_order_quantity = $_POST['max_order_quantity'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match';
    if (empty($full_name)) $errors[] = 'Full name is required';
    if (empty($phone)) $errors[] = 'Phone number is required';
    if (empty($address)) $errors[] = 'Address is required';
    if (empty($business_name)) $errors[] = 'Business name is required';
    if (empty($business_type)) $errors[] = 'Business type is required';
    
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Username or email already exists';
            } else {
                // Insert user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, full_name, phone, address, city, state, pincode, user_type, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'buyer', 'approved')
                ");
                $stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $address, $city, $state, $pincode]);
                $user_id = $pdo->lastInsertId();
                
                // Insert buyer details
                $stmt = $pdo->prepare("
                    INSERT INTO buyer_details (user_id, business_name, business_type, gst_number, license_number, preferred_crops, max_order_quantity) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$user_id, $business_name, $business_type, $gst_number, $license_number, $preferred_crops, $max_order_quantity]);
                
                $message = 'Registration successful! You can now login and start browsing crops.';
                $message_type = 'success';
                
                // Clear form data
                $_POST = [];
            }
        } catch (Exception $e) {
            $errors[] = 'Registration failed. Please try again.';
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
    <title>Buyer Registration - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Buyer Registration</h1>
                <p>Join our platform to connect with farmers and buy fresh crops directly</p>
            </div>

            <div class="registration-form">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-section">
                        <h2>Basic Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Password *</label>
                                <input type="password" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password *</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Business Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="business_name">Business Name *</label>
                                <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($_POST['business_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="business_type">Business Type *</label>
                                <select id="business_type" name="business_type" required>
                                    <option value="retailer" <?php echo ($_POST['business_type'] ?? 'retailer') === 'retailer' ? 'selected' : ''; ?>>Retailer</option>
                                    <option value="wholesaler" <?php echo ($_POST['business_type'] ?? '') === 'wholesaler' ? 'selected' : ''; ?>>Wholesaler</option>
                                    <option value="processor" <?php echo ($_POST['business_type'] ?? '') === 'processor' ? 'selected' : ''; ?>>Processor</option>
                                    <option value="exporter" <?php echo ($_POST['business_type'] ?? '') === 'exporter' ? 'selected' : ''; ?>>Exporter</option>
                                    <option value="other" <?php echo ($_POST['business_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gst_number">GST Number</label>
                                <input type="text" id="gst_number" name="gst_number" value="<?php echo htmlspecialchars($_POST['gst_number'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="license_number">License Number</label>
                                <input type="text" id="license_number" name="license_number" value="<?php echo htmlspecialchars($_POST['license_number'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="preferred_crops">Preferred Crops</label>
                            <input type="text" id="preferred_crops" name="preferred_crops" placeholder="e.g., Rice, Wheat, Cotton" value="<?php echo htmlspecialchars($_POST['preferred_crops'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="max_order_quantity">Maximum Order Quantity (kg)</label>
                            <input type="number" id="max_order_quantity" name="max_order_quantity" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['max_order_quantity'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Address Information</h2>
                        <div class="form-group">
                            <label for="address">Address *</label>
                            <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="pincode">Pincode</label>
                                <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($_POST['pincode'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Register as Buyer</button>
                        <a href="login.php" class="btn btn-outline">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
