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
    
    // Farmer-specific fields
    $land_size = $_POST['land_size'] ?? '';
    $land_unit = $_POST['land_unit'] ?? 'acres';
    $farming_experience = $_POST['farming_experience'] ?? '';
    $primary_crops = trim($_POST['primary_crops'] ?? '');
    $organic_certified = isset($_POST['organic_certified']) ? 1 : 0;
    $certification_number = trim($_POST['certification_number'] ?? '');
    $bank_account = trim($_POST['bank_account'] ?? '');
    $ifsc_code = trim($_POST['ifsc_code'] ?? '');
    $aadhar_number = trim($_POST['aadhar_number'] ?? '');
    $pan_number = trim($_POST['pan_number'] ?? '');
    
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
    if (empty($land_size)) $errors[] = 'Land size is required';
    if (empty($aadhar_number)) $errors[] = 'Aadhar number is required';
    
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
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'farmer', 'pending')
                ");
                $stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $address, $city, $state, $pincode]);
                $user_id = $pdo->lastInsertId();
                
                // Insert farmer details
                $stmt = $pdo->prepare("
                    INSERT INTO farmer_details (user_id, land_size, land_unit, farming_experience, primary_crops, organic_certified, certification_number, bank_account, ifsc_code, aadhar_number, pan_number) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$user_id, $land_size, $land_unit, $farming_experience, $primary_crops, $organic_certified, $certification_number, $bank_account, $ifsc_code, $aadhar_number, $pan_number]);
                
                // Create approval record
                $stmt = $pdo->prepare("
                    INSERT INTO government_approvals (user_id, approval_type, status) 
                    VALUES (?, 'farmer_registration', 'pending')
                ");
                $stmt->execute([$user_id]);
                
                $message = 'Registration successful! Your account is pending government approval.';
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
    <title>Farmer Registration - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Farmer Registration</h1>
                <p>Join our platform to connect with buyers and sell your crops directly</p>
            </div>

            <div class="registration-form">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
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

                    <div class="form-section">
                        <h2>Farming Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="land_size">Land Size *</label>
                                <input type="number" id="land_size" name="land_size" step="0.01" value="<?php echo htmlspecialchars($_POST['land_size'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="land_unit">Unit</label>
                                <select id="land_unit" name="land_unit">
                                    <option value="acres" <?php echo ($_POST['land_unit'] ?? 'acres') === 'acres' ? 'selected' : ''; ?>>Acres</option>
                                    <option value="hectares" <?php echo ($_POST['land_unit'] ?? '') === 'hectares' ? 'selected' : ''; ?>>Hectares</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="farming_experience">Farming Experience (Years)</label>
                                <input type="number" id="farming_experience" name="farming_experience" value="<?php echo htmlspecialchars($_POST['farming_experience'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="primary_crops">Primary Crops</label>
                            <input type="text" id="primary_crops" name="primary_crops" placeholder="e.g., Rice, Wheat, Cotton" value="<?php echo htmlspecialchars($_POST['primary_crops'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="organic_certified" value="1" <?php echo isset($_POST['organic_certified']) ? 'checked' : ''; ?>>
                                Organic Certified
                            </label>
                        </div>
                        
                        <div class="form-group" id="certification_group" style="<?php echo isset($_POST['organic_certified']) ? '' : 'display: none;'; ?>">
                            <label for="certification_number">Certification Number</label>
                            <input type="text" id="certification_number" name="certification_number" value="<?php echo htmlspecialchars($_POST['certification_number'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Banking & Identity Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_account">Bank Account Number</label>
                                <input type="text" id="bank_account" name="bank_account" value="<?php echo htmlspecialchars($_POST['bank_account'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="ifsc_code">IFSC Code</label>
                                <input type="text" id="ifsc_code" name="ifsc_code" value="<?php echo htmlspecialchars($_POST['ifsc_code'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="aadhar_number">Aadhar Number *</label>
                                <input type="text" id="aadhar_number" name="aadhar_number" maxlength="12" value="<?php echo htmlspecialchars($_POST['aadhar_number'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="pan_number">PAN Number</label>
                                <input type="text" id="pan_number" name="pan_number" maxlength="10" value="<?php echo htmlspecialchars($_POST['pan_number'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Register as Farmer</button>
                        <a href="login.php" class="btn btn-outline">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script>
        // Show/hide certification number field based on organic certified checkbox
        document.querySelector('input[name="organic_certified"]').addEventListener('change', function() {
            const certGroup = document.getElementById('certification_group');
            if (this.checked) {
                certGroup.style.display = 'block';
            } else {
                certGroup.style.display = 'none';
            }
        });
    </script>
</body>
</html>
