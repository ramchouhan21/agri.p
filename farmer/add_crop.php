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
$message = '';
$message_type = '';

if ($_POST) {
    $crop_name = trim($_POST['crop_name'] ?? '');
    $variety = trim($_POST['variety'] ?? '');
    $quantity = $_POST['quantity'] ?? '';
    $unit = $_POST['unit'] ?? 'kg';
    $price_per_unit = $_POST['price_per_unit'] ?? '';
    $harvest_date = $_POST['harvest_date'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $quality_grade = $_POST['quality_grade'] ?? 'A';
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($crop_name)) $errors[] = 'Crop name is required';
    if (empty($quantity) || $quantity <= 0) $errors[] = 'Valid quantity is required';
    if (empty($price_per_unit) || $price_per_unit <= 0) $errors[] = 'Valid price per unit is required';
    if (empty($harvest_date)) $errors[] = 'Harvest date is required';
    
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Handle image upload
            $image_url = '';
            if (isset($_FILES['crop_image']) && $_FILES['crop_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assets/images/crops/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['crop_image']['name'], PATHINFO_EXTENSION);
                $filename = 'crop_' . $farmer_id . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['crop_image']['tmp_name'], $upload_path)) {
                    $image_url = 'assets/images/crops/' . $filename;
                }
            }
            
            // Insert crop
            $stmt = $pdo->prepare("
                INSERT INTO crops (farmer_id, crop_name, variety, quantity, unit, price_per_unit, harvest_date, expiry_date, quality_grade, description, image_url, location, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_approval')
            ");
            $stmt->execute([$farmer_id, $crop_name, $variety, $quantity, $unit, $price_per_unit, $harvest_date, $expiry_date, $quality_grade, $description, $image_url, $location]);
            
            // Create approval record
            $crop_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("
                INSERT INTO government_approvals (crop_id, approval_type, status) 
                VALUES (?, 'crop_approval', 'pending')
            ");
            $stmt->execute([$crop_id]);
            
            $message = 'Crop added successfully! It is pending government approval.';
            $message_type = 'success';
            
            // Clear form data
            $_POST = [];
            
        } catch (Exception $e) {
            $message = 'Failed to add crop. Please try again.';
            $message_type = 'error';
        }
    }
    
    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    }
}

// Get price recommendations for the form
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT DISTINCT crop_name FROM price_recommendations ORDER BY crop_name");
    $stmt->execute();
    $crop_suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $crop_suggestions = [];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Crop - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Add New Crop</h1>
                <p>List your crop for sale on the platform</p>
            </div>

            <div class="add-crop-form">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-section">
                        <h2>Crop Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="crop_name">Crop Name *</label>
                                <input type="text" id="crop_name" name="crop_name" list="crop_suggestions" value="<?php echo htmlspecialchars($_POST['crop_name'] ?? ''); ?>" required>
                                <datalist id="crop_suggestions">
                                    <?php foreach ($crop_suggestions as $crop): ?>
                                        <option value="<?php echo htmlspecialchars($crop); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="variety">Variety</label>
                                <input type="text" id="variety" name="variety" value="<?php echo htmlspecialchars($_POST['variety'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="quantity">Quantity *</label>
                                <input type="number" id="quantity" name="quantity" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <select id="unit" name="unit">
                                    <option value="kg" <?php echo ($_POST['unit'] ?? 'kg') === 'kg' ? 'selected' : ''; ?>>Kilograms (kg)</option>
                                    <option value="quintal" <?php echo ($_POST['unit'] ?? '') === 'quintal' ? 'selected' : ''; ?>>Quintal</option>
                                    <option value="ton" <?php echo ($_POST['unit'] ?? '') === 'ton' ? 'selected' : ''; ?>>Ton</option>
                                    <option value="bags" <?php echo ($_POST['unit'] ?? '') === 'bags' ? 'selected' : ''; ?>>Bags</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="price_per_unit">Price per Unit (₹) *</label>
                                <input type="number" id="price_per_unit" name="price_per_unit" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['price_per_unit'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="quality_grade">Quality Grade</label>
                                <select id="quality_grade" name="quality_grade">
                                    <option value="A" <?php echo ($_POST['quality_grade'] ?? 'A') === 'A' ? 'selected' : ''; ?>>Grade A (Premium)</option>
                                    <option value="B" <?php echo ($_POST['quality_grade'] ?? '') === 'B' ? 'selected' : ''; ?>>Grade B (Good)</option>
                                    <option value="C" <?php echo ($_POST['quality_grade'] ?? '') === 'C' ? 'selected' : ''; ?>>Grade C (Standard)</option>
                                    <option value="Organic" <?php echo ($_POST['quality_grade'] ?? '') === 'Organic' ? 'selected' : ''; ?>>Organic</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Harvest & Location Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="harvest_date">Harvest Date *</label>
                                <input type="date" id="harvest_date" name="harvest_date" value="<?php echo htmlspecialchars($_POST['harvest_date'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="expiry_date">Expected Expiry Date</label>
                                <input type="date" id="expiry_date" name="expiry_date" value="<?php echo htmlspecialchars($_POST['expiry_date'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" placeholder="e.g., Village, District, State" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Additional Information</h2>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4" placeholder="Describe your crop, farming methods, storage conditions, etc."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="crop_image">Crop Image</label>
                            <input type="file" id="crop_image" name="crop_image" accept="image/*">
                            <small>Upload a clear image of your crop (optional)</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Crop</button>
                        <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script>
        // Auto-calculate expiry date based on harvest date
        document.getElementById('harvest_date').addEventListener('change', function() {
            const harvestDate = new Date(this.value);
            const expiryDate = new Date(harvestDate);
            expiryDate.setDate(expiryDate.getDate() + 30); // Default 30 days
            
            document.getElementById('expiry_date').value = expiryDate.toISOString().split('T')[0];
        });
        
        // Price recommendation based on crop selection
        document.getElementById('crop_name').addEventListener('change', function() {
            const cropName = this.value;
            if (cropName) {
                // In a real application, you would fetch price recommendations via AJAX
                console.log('Fetching price recommendations for:', cropName);
            }
        });
    </script>
</body>
</html>
