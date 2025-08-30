<?php
session_start();
include '../includes/language.php';
include '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'government') {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$message_type = '';

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $message = 'Please enter both username and password';
        $message_type = 'error';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND user_type = 'government'");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] === 'approved') {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['email'] = $user['email'];
                    
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $message = 'Your account is not approved. Please contact support.';
                    $message_type = 'error';
                }
            } else {
                $message = 'Invalid username or password';
                $message_type = 'error';
            }
        } catch (Exception $e) {
            $message = 'Login failed. Please try again.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Government Login - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="login-container">
                <div class="login-form">
                    <div class="login-header">
                        <h1>Government Login</h1>
                        <p>Access government dashboard</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="username">Username or Email</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">Login</button>
                    </form>
                    
                    <div class="login-footer">
                        <p><a href="../index.php">← Back to Home</a></p>
                    </div>
                </div>
                
                <div class="login-info">
                    <h2>Government Portal</h2>
                    <ul class="benefits-list">
                        <li><i class="fas fa-check"></i> Approve farmer registrations</li>
                        <li><i class="fas fa-check"></i> Verify crop listings</li>
                        <li><i class="fas fa-check"></i> Set Minimum Support Prices</li>
                        <li><i class="fas fa-check"></i> Generate reports</li>
                        <li><i class="fas fa-check"></i> Monitor transactions</li>
                        <li><i class="fas fa-check"></i> Manage logistics</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
