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
$crop_name = $_GET['crop_name'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$quality_grade = $_GET['quality_grade'] ?? '';
$location = $_GET['location'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'created_at';
$sort_order = $_GET['sort_order'] ?? 'DESC';

try {
    $pdo = getDBConnection();
    
    // Build query with filters
    $where_conditions = ["c.status = 'available'", "u.status = 'approved'"];
    $params = [];
    
    if ($crop_name) {
        $where_conditions[] = "c.crop_name LIKE ?";
        $params[] = "%$crop_name%";
    }
    
    if ($min_price) {
        $where_conditions[] = "c.price_per_unit >= ?";
        $params[] = $min_price;
    }
    
    if ($max_price) {
        $where_conditions[] = "c.price_per_unit <= ?";
        $params[] = $max_price;
    }
    
    if ($quality_grade) {
        $where_conditions[] = "c.quality_grade = ?";
        $params[] = $quality_grade;
    }
    
    if ($location) {
        $where_conditions[] = "(c.location LIKE ? OR u.city LIKE ? OR u.state LIKE ?)";
        $params[] = "%$location%";
        $params[] = "%$location%";
        $params[] = "%$location%";
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get crops
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name as farmer_name, u.city, u.state, u.phone as farmer_phone
        FROM crops c
        JOIN users u ON c.farmer_id = u.id
        WHERE $where_clause
        ORDER BY c.$sort_by $sort_order
        LIMIT 20
    ");
    $stmt->execute($params);
    $crops = $stmt->fetchAll();
    
    // Get unique crop names for filter
    $stmt = $pdo->prepare("SELECT DISTINCT crop_name FROM crops WHERE status = 'available' ORDER BY crop_name");
    $stmt->execute();
    $crop_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get price range
    $stmt = $pdo->prepare("SELECT MIN(price_per_unit) as min_price, MAX(price_per_unit) as max_price FROM crops WHERE status = 'available'");
    $stmt->execute();
    $price_range = $stmt->fetch();
    
} catch (Exception $e) {
    $error_message = "Error loading crops data";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Crops - Smart Agriculture System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Browse Crops</h1>
                <p>Find fresh, quality crops from verified farmers</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="filters-section">
                <h2>Filter Crops</h2>
                <form method="GET" class="filter-form">
                    <div class="filter-row">
                        <div class="form-group">
                            <label for="crop_name">Crop Name</label>
                            <select id="crop_name" name="crop_name">
                                <option value="">All Crops</option>
                                <?php foreach ($crop_names as $name): ?>
                                    <option value="<?php echo htmlspecialchars($name); ?>" <?php echo $crop_name === $name ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="min_price">Min Price (₹)</label>
                            <input type="number" id="min_price" name="min_price" step="0.01" min="0" value="<?php echo htmlspecialchars($min_price); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="max_price">Max Price (₹)</label>
                            <input type="number" id="max_price" name="max_price" step="0.01" min="0" value="<?php echo htmlspecialchars($max_price); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="quality_grade">Quality Grade</label>
                            <select id="quality_grade" name="quality_grade">
                                <option value="">All Grades</option>
                                <option value="A" <?php echo $quality_grade === 'A' ? 'selected' : ''; ?>>Grade A</option>
                                <option value="B" <?php echo $quality_grade === 'B' ? 'selected' : ''; ?>>Grade B</option>
                                <option value="C" <?php echo $quality_grade === 'C' ? 'selected' : ''; ?>>Grade C</option>
                                <option value="Organic" <?php echo $quality_grade === 'Organic' ? 'selected' : ''; ?>>Organic</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-row">
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" placeholder="City, State" value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sort_by">Sort By</label>
                            <select id="sort_by" name="sort_by">
                                <option value="created_at" <?php echo $sort_by === 'created_at' ? 'selected' : ''; ?>>Date Added</option>
                                <option value="price_per_unit" <?php echo $sort_by === 'price_per_unit' ? 'selected' : ''; ?>>Price</option>
                                <option value="crop_name" <?php echo $sort_by === 'crop_name' ? 'selected' : ''; ?>>Crop Name</option>
                                <option value="harvest_date" <?php echo $sort_by === 'harvest_date' ? 'selected' : ''; ?>>Harvest Date</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="sort_order">Order</label>
                            <select id="sort_order" name="sort_order">
                                <option value="DESC" <?php echo $sort_order === 'DESC' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="ASC" <?php echo $sort_order === 'ASC' ? 'selected' : ''; ?>>Oldest First</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="browse_crops.php" class="btn btn-outline">Clear</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Crops Grid -->
            <div class="crops-section">
                <h2>Available Crops</h2>
                
                <?php if (empty($crops)): ?>
                    <div class="empty-state">
                        <i class="fas fa-seedling"></i>
                        <h3>No crops found</h3>
                        <p>Try adjusting your filters or check back later for new listings</p>
                    </div>
                <?php else: ?>
                    <div class="crops-grid">
                        <?php foreach ($crops as $crop): ?>
                            <div class="crop-card">
                                <?php if ($crop['image_url']): ?>
                                    <div class="crop-image">
                                        <img src="../<?php echo htmlspecialchars($crop['image_url']); ?>" alt="<?php echo htmlspecialchars($crop['crop_name']); ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="crop-image placeholder">
                                        <i class="fas fa-seedling"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="crop-content">
                                    <div class="crop-header">
                                        <h3><?php echo htmlspecialchars($crop['crop_name']); ?></h3>
                                        <span class="quality-badge quality-<?php echo strtolower($crop['quality_grade']); ?>">
                                            <?php echo $crop['quality_grade']; ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($crop['variety']): ?>
                                        <p class="crop-variety"><?php echo htmlspecialchars($crop['variety']); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="crop-details">
                                        <div class="detail-item">
                                            <i class="fas fa-weight"></i>
                                            <span><?php echo $crop['quantity'] . ' ' . $crop['unit']; ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-rupee-sign"></i>
                                            <span>₹<?php echo number_format($crop['price_per_unit'], 2); ?>/<?php echo $crop['unit']; ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-calendar"></i>
                                            <span><?php echo date('M d, Y', strtotime($crop['harvest_date'])); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="farmer-info">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($crop['farmer_name']); ?></span>
                                        <small><?php echo htmlspecialchars($crop['city'] . ', ' . $crop['state']); ?></small>
                                    </div>
                                    
                                    <?php if ($crop['description']): ?>
                                        <p class="crop-description"><?php echo htmlspecialchars(substr($crop['description'], 0, 100)) . (strlen($crop['description']) > 100 ? '...' : ''); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="crop-actions">
                                        <a href="place_order.php?id=<?php echo $crop['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart"></i>
                                            Place Order
                                        </a>
                                        <button class="btn btn-outline" onclick="viewCropDetails(<?php echo $crop['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Crop Details Modal -->
    <div id="cropModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Crop Details</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body" id="cropDetails">
                <!-- Crop details will be loaded here -->
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script>
        // Modal functionality
        const modal = document.getElementById('cropModal');
        const closeBtn = document.querySelector('.close');
        
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        function viewCropDetails(cropId) {
            // In a real application, you would fetch crop details via AJAX
            document.getElementById('cropDetails').innerHTML = `
                <p>Loading crop details...</p>
                <p>Crop ID: ${cropId}</p>
                <p>This would show detailed information about the crop, farmer details, quality specifications, etc.</p>
            `;
            modal.style.display = 'block';
        }
    </script>
</body>
</html>
