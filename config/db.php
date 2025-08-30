<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smart_agriculture');

// Create connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Test connection
function testConnection() {
    try {
        $pdo = getDBConnection();
        return true;
    } catch(Exception $e) {
        return false;
    }
}

// Initialize database tables if they don't exist
function initializeDatabase() {
    $pdo = getDBConnection();
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        phone VARCHAR(15),
        address TEXT,
        user_type ENUM('farmer', 'buyer', 'government', 'admin') NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Create crops table
    $sql = "CREATE TABLE IF NOT EXISTS crops (
        id INT AUTO_INCREMENT PRIMARY KEY,
        farmer_id INT NOT NULL,
        crop_name VARCHAR(100) NOT NULL,
        variety VARCHAR(100),
        quantity DECIMAL(10,2) NOT NULL,
        unit VARCHAR(20) NOT NULL,
        price_per_unit DECIMAL(10,2) NOT NULL,
        harvest_date DATE,
        quality_grade VARCHAR(20),
        description TEXT,
        image_url VARCHAR(255),
        status ENUM('available', 'sold', 'pending_approval') DEFAULT 'pending_approval',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        buyer_id INT NOT NULL,
        crop_id INT NOT NULL,
        quantity DECIMAL(10,2) NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        delivery_address TEXT,
        payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
        FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Create price_recommendations table
    $sql = "CREATE TABLE IF NOT EXISTS price_recommendations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_name VARCHAR(100) NOT NULL,
        recommended_price DECIMAL(10,2) NOT NULL,
        market_price DECIMAL(10,2),
        msp_price DECIMAL(10,2),
        region VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Create government_approvals table
    $sql = "CREATE TABLE IF NOT EXISTS government_approvals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        approval_type ENUM('farmer_registration', 'price_approval', 'crop_approval') NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        comments TEXT,
        approved_by INT,
        approved_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    
    // Create disputes table
    $sql = "CREATE TABLE IF NOT EXISTS disputes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        complainant_id INT NOT NULL,
        respondent_id INT NOT NULL,
        order_id INT,
        dispute_type ENUM('quality', 'delivery', 'payment', 'other') NOT NULL,
        description TEXT NOT NULL,
        status ENUM('open', 'investigating', 'resolved', 'closed') DEFAULT 'open',
        resolution TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        resolved_at TIMESTAMP NULL,
        FOREIGN KEY (complainant_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (respondent_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    
    // Create admin user if not exists
    $sql = "INSERT IGNORE INTO users (username, email, password, full_name, user_type, status) 
            VALUES ('admin', 'admin@smartagriculture.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'System Administrator', 'admin', 'approved')";
    $pdo->exec($sql);
    
    // Create government user if not exists
    $sql = "INSERT IGNORE INTO users (username, email, password, full_name, user_type, status) 
            VALUES ('govt', 'govt@smartagriculture.com', '" . password_hash('govt123', PASSWORD_DEFAULT) . "', 'Government Official', 'government', 'approved')";
    $pdo->exec($sql);
}

// Call initialize function
initializeDatabase();
?>
