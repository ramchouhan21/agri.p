<?php
session_start();
include '../includes/language.php';
include '../config/db.php';

// Check if user is logged in as government
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'government') {
    header('Location: login.php');
    exit();
}

$govt_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Handle approval action
if ($_POST && isset($_POST['approval_id'])) {
    $approval_id = $_POST['approval_id'];
    $action = $_POST['action'];
    $comments = trim($_POST['comments'] ?? '');
    
    try {
        $pdo = getDBConnection();
        
        if ($action === 'approve') {
            // Update approval status
            $stmt = $pdo->prepare("
                UPDATE government_approvals 
                SET status = 'approved', approved_by = ?, approved_at = NOW(), comments = ?
                WHERE id = ?
            ");
            $stmt->execute([$govt_id, $comments, $approval_id]);
            
            // Update user status
            $stmt = $pdo->prepare("
                UPDATE users u
                JOIN government_approvals ga ON u.id = ga.user_id
                SET u.status = 'approved'
                WHERE ga.id = ?
            ");
            $stmt->execute([$approval_id]);
            
            $message = 'Farmer registration approved successfully!';
            $message_type = 'success';
            
        } elseif ($action === 'reject') {
            // Update approval status
            $stmt = $pdo->prepare("
                UPDATE government_approvals 
                SET status = 'rejected', approved_by = ?, approved_at = NOW(), comments = ?
                WHERE id = ?
            ");
            $stmt->execute([$govt_id, $comments, $approval_id]);
            
            // Update user status
            $stmt = $pdo->prepare("
                UPDATE users u
                JOIN government_approvals ga ON u.id = ga.user_id
                SET u.status = 'rejected'
                WHERE ga.id = ?
            ");
            $stmt->execute([$approval_id]);
            
            $message = 'Farmer registration rejected.';
            $message_type = 'success';
        }
        
    } catch (Exception $e) {
        $message = 'Failed to process approval. Please try again.';
        $message_type = 'error';
    }
}

// Get specific approval details if ID is provided
$approval_id = $_GET['id'] ?? '';
$approval_details = null;

if ($approval_id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT ga.*, u.*, fd.*
            FROM government_approvals ga
            JOIN users u ON ga.user_id = u.id
            LEFT JOIN farmer_details fd ON u.id = fd.user_id
            WHERE ga.id = ? AND ga.approval_type = 'farmer_registration'
        ");
        $stmt->execute([$approval_id]);
        $approval_details = $stmt->fetch();
    } catch (Exception $e) {
        $error_message = "Error loading approval details";
    }
}

// Get pending farmer approvals
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT ga.*, u.full_name, u.email, u.phone, u.created_at as registration_date
        FROM government_approvals ga
        JOIN users u ON ga.user_id = u.id
        WHERE ga.approval_type = 'farmer_registration'
        ORDER BY ga.created_at ASC
    ");
    $stmt->execute();
    $pending_approvals = $stmt->fetchAll();
} catch (Exception $e) {
    $error_message = "Error loading approvals data";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Farmers - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Farmer Registration Approvals</h1>
                <p>Review and approve farmer registrations</p>
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

            <?php if ($approval_details): ?>
                <!-- Detailed Approval View -->
                <div class="approval-details">
                    <div class="approval-header">
                        <h2>Farmer Registration Review</h2>
                        <div class="approval-status">
                            <span class="status-badge status-<?php echo $approval_details['status']; ?>">
                                <?php echo ucfirst($approval_details['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="approval-content">
                        <div class="applicant-info">
                            <h3>Applicant Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Full Name:</label>
                                    <span><?php echo htmlspecialchars($approval_details['full_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span><?php echo htmlspecialchars($approval_details['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Phone:</label>
                                    <span><?php echo htmlspecialchars($approval_details['phone']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Address:</label>
                                    <span><?php echo htmlspecialchars($approval_details['address']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>City:</label>
                                    <span><?php echo htmlspecialchars($approval_details['city']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>State:</label>
                                    <span><?php echo htmlspecialchars($approval_details['state']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="farming-info">
                            <h3>Farming Information</h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Land Size:</label>
                                    <span><?php echo $approval_details['land_size'] . ' ' . $approval_details['land_unit']; ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Farming Experience:</label>
                                    <span><?php echo $approval_details['farming_experience']; ?> years</span>
                                </div>
                                <div class="info-item">
                                    <label>Primary Crops:</label>
                                    <span><?php echo htmlspecialchars($approval_details['primary_crops']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Organic Certified:</label>
                                    <span><?php echo $approval_details['organic_certified'] ? 'Yes' : 'No'; ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Aadhar Number:</label>
                                    <span><?php echo htmlspecialchars($approval_details['aadhar_number']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>PAN Number:</label>
                                    <span><?php echo htmlspecialchars($approval_details['pan_number']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($approval_details['status'] === 'pending'): ?>
                            <div class="approval-actions">
                                <h3>Approval Decision</h3>
                                <form method="POST" action="">
                                    <input type="hidden" name="approval_id" value="<?php echo $approval_details['id']; ?>">
                                    
                                    <div class="form-group">
                                        <label for="comments">Comments</label>
                                        <textarea id="comments" name="comments" rows="4" placeholder="Add any comments or notes about this application"></textarea>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <button type="submit" name="action" value="approve" class="btn btn-primary">
                                            <i class="fas fa-check"></i>
                                            Approve Registration
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-outline" onclick="return confirm('Are you sure you want to reject this registration?')">
                                            <i class="fas fa-times"></i>
                                            Reject Registration
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="approval-result">
                                <h3>Approval Result</h3>
                                <p><strong>Status:</strong> <?php echo ucfirst($approval_details['status']); ?></p>
                                <?php if ($approval_details['comments']): ?>
                                    <p><strong>Comments:</strong> <?php echo htmlspecialchars($approval_details['comments']); ?></p>
                                <?php endif; ?>
                                <?php if ($approval_details['approved_at']): ?>
                                    <p><strong>Processed on:</strong> <?php echo date('M d, Y H:i', strtotime($approval_details['approved_at'])); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Approvals List -->
                <div class="approvals-list">
                    <h2>Pending Farmer Registrations</h2>
                    
                    <?php if (empty($pending_approvals)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h3>No pending approvals</h3>
                            <p>All farmer registrations have been processed</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Contact</th>
                                        <th>Location</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_approvals as $approval): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($approval['full_name']); ?></strong>
                                                <br><small><?php echo htmlspecialchars($approval['email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($approval['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($approval['city'] . ', ' . $approval['state']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($approval['registration_date'])); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $approval['status']; ?>">
                                                    <?php echo ucfirst($approval['status']); ?>
                                                </span>
                                            </td>
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
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>
