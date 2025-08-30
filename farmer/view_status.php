<?php
session_start();
include '../includes/language.php';
include '../config/db.php';

// Check if user is logged in as farmer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    header('Location: login.php');
    exit();
}

$farmer_id = $_SESSION['user_id'];

try {
    $pdo = getDBConnection();
    
    // Get farmer approval status
    $stmt = $pdo->prepare("
        SELECT * FROM government_approvals 
        WHERE user_id = ? AND approval_type = 'farmer_registration'
        ORDER BY created_at DESC
    ");
    $stmt->execute([$farmer_id]);
    $farmer_approval = $stmt->fetch();
    
    // Get crop approval statuses
    $stmt = $pdo->prepare("
        SELECT ga.*, c.crop_name, c.variety, c.created_at as crop_created_at
        FROM government_approvals ga
        JOIN crops c ON ga.crop_id = c.id
        WHERE c.farmer_id = ?
        ORDER BY ga.created_at DESC
    ");
    $stmt->execute([$farmer_id]);
    $crop_approvals = $stmt->fetchAll();
    
    // Get farmer details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$farmer_id]);
    $farmer = $stmt->fetch();
    
} catch (Exception $e) {
    $error_message = "Error loading status data";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Status - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Approval Status</h1>
                <p>Track the status of your registrations and crop listings</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Farmer Registration Status -->
            <div class="status-section">
                <h2>Farmer Registration Status</h2>
                <div class="status-card">
                    <?php if ($farmer_approval): ?>
                        <div class="status-header">
                            <div class="status-info">
                                <h3>Registration Application</h3>
                                <p>Submitted on <?php echo date('M d, Y H:i', strtotime($farmer_approval['created_at'])); ?></p>
                            </div>
                            <div class="status-badge status-<?php echo $farmer_approval['status']; ?>">
                                <?php echo ucfirst($farmer_approval['status']); ?>
                            </div>
                        </div>
                        
                        <?php if ($farmer_approval['status'] === 'approved'): ?>
                            <div class="status-message success">
                                <i class="fas fa-check-circle"></i>
                                <p>Congratulations! Your farmer registration has been approved. You can now add crops and start selling.</p>
                            </div>
                        <?php elseif ($farmer_approval['status'] === 'rejected'): ?>
                            <div class="status-message error">
                                <i class="fas fa-times-circle"></i>
                                <p>Your registration has been rejected. Please contact support for more information.</p>
                                <?php if ($farmer_approval['comments']): ?>
                                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($farmer_approval['comments']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="status-message warning">
                                <i class="fas fa-clock"></i>
                                <p>Your registration is under review by government officials. This process typically takes 2-3 business days.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="status-message info">
                            <i class="fas fa-info-circle"></i>
                            <p>No registration application found. Please complete your farmer registration first.</p>
                            <a href="register.php" class="btn btn-primary">Complete Registration</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Crop Approval Status -->
            <div class="status-section">
                <h2>Crop Listing Status</h2>
                
                <?php if (empty($crop_approvals)): ?>
                    <div class="empty-state">
                        <i class="fas fa-seedling"></i>
                        <h3>No crops submitted for approval</h3>
                        <p>Add your first crop to start selling on the platform</p>
                        <a href="add_crop.php" class="btn btn-primary">Add Crop</a>
                    </div>
                <?php else: ?>
                    <div class="crop-status-list">
                        <?php foreach ($crop_approvals as $approval): ?>
                            <div class="status-card">
                                <div class="status-header">
                                    <div class="status-info">
                                        <h3><?php echo htmlspecialchars($approval['crop_name']); ?></h3>
                                        <?php if ($approval['variety']): ?>
                                            <p>Variety: <?php echo htmlspecialchars($approval['variety']); ?></p>
                                        <?php endif; ?>
                                        <p>Submitted on <?php echo date('M d, Y H:i', strtotime($approval['crop_created_at'])); ?></p>
                                    </div>
                                    <div class="status-badge status-<?php echo $approval['status']; ?>">
                                        <?php echo ucfirst($approval['status']); ?>
                                    </div>
                                </div>
                                
                                <?php if ($approval['status'] === 'approved'): ?>
                                    <div class="status-message success">
                                        <i class="fas fa-check-circle"></i>
                                        <p>Your crop listing has been approved and is now visible to buyers.</p>
                                    </div>
                                <?php elseif ($approval['status'] === 'rejected'): ?>
                                    <div class="status-message error">
                                        <i class="fas fa-times-circle"></i>
                                        <p>Your crop listing has been rejected.</p>
                                        <?php if ($approval['comments']): ?>
                                            <p><strong>Reason:</strong> <?php echo htmlspecialchars($approval['comments']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="status-message warning">
                                        <i class="fas fa-clock"></i>
                                        <p>Your crop listing is under review. This typically takes 1-2 business days.</p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($approval['approved_at']): ?>
                                    <div class="approval-details">
                                        <p><strong>Approved on:</strong> <?php echo date('M d, Y H:i', strtotime($approval['approved_at'])); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Help Section -->
            <div class="help-section">
                <h2>Need Help?</h2>
                <div class="help-cards">
                    <div class="help-card">
                        <i class="fas fa-phone"></i>
                        <h3>Contact Support</h3>
                        <p>Call our support team for assistance with your applications</p>
                        <a href="../contact.php" class="btn btn-outline">Contact Us</a>
                    </div>
                    <div class="help-card">
                        <i class="fas fa-question-circle"></i>
                        <h3>FAQ</h3>
                        <p>Find answers to common questions about the approval process</p>
                        <a href="../about.php" class="btn btn-outline">Learn More</a>
                    </div>
                    <div class="help-card">
                        <i class="fas fa-file-alt"></i>
                        <h3>Guidelines</h3>
                        <p>Read our guidelines for successful crop listings</p>
                        <a href="#" class="btn btn-outline">View Guidelines</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
