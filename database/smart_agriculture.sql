-- Smart Agriculture System Database Schema
-- Created for connecting farmers, buyers, and government officials

-- Create database
CREATE DATABASE IF NOT EXISTS smart_agriculture;
USE smart_agriculture;

-- Users table - stores all user types (farmers, buyers, government, admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    user_type ENUM('farmer', 'buyer', 'government', 'admin') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_type (user_type),
    INDEX idx_status (status),
    INDEX idx_email (email)
);

-- Farmer-specific information
CREATE TABLE farmer_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    land_size DECIMAL(10,2),
    land_unit ENUM('acres', 'hectares') DEFAULT 'acres',
    farming_experience INT,
    primary_crops TEXT,
    organic_certified BOOLEAN DEFAULT FALSE,
    certification_number VARCHAR(100),
    bank_account VARCHAR(20),
    ifsc_code VARCHAR(15),
    aadhar_number VARCHAR(12),
    pan_number VARCHAR(10),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Buyer-specific information
CREATE TABLE buyer_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_name VARCHAR(100),
    business_type ENUM('retailer', 'wholesaler', 'processor', 'exporter', 'other') NOT NULL,
    gst_number VARCHAR(15),
    license_number VARCHAR(50),
    preferred_crops TEXT,
    max_order_quantity DECIMAL(10,2),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Crops table - stores crop information
CREATE TABLE crops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT NOT NULL,
    crop_name VARCHAR(100) NOT NULL,
    variety VARCHAR(100),
    quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    harvest_date DATE,
    expiry_date DATE,
    quality_grade ENUM('A', 'B', 'C', 'Organic') DEFAULT 'A',
    description TEXT,
    image_url VARCHAR(255),
    location VARCHAR(100),
    status ENUM('available', 'sold', 'pending_approval', 'rejected') DEFAULT 'pending_approval',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_crop_name (crop_name),
    INDEX idx_status (status),
    INDEX idx_farmer_id (farmer_id)
);

-- Orders table - stores order information
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    buyer_id INT NOT NULL,
    crop_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_date DATE,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'disputed') DEFAULT 'pending',
    delivery_address TEXT,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method ENUM('cash', 'bank_transfer', 'upi', 'card') DEFAULT 'bank_transfer',
    notes TEXT,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE,
    INDEX idx_buyer_id (buyer_id),
    INDEX idx_crop_id (crop_id),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date)
);

-- Price recommendations table
CREATE TABLE price_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    variety VARCHAR(100),
    recommended_price DECIMAL(10,2) NOT NULL,
    market_price DECIMAL(10,2),
    msp_price DECIMAL(10,2),
    region VARCHAR(100),
    season VARCHAR(20),
    quality_grade ENUM('A', 'B', 'C', 'Organic') DEFAULT 'A',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_crop_name (crop_name),
    INDEX idx_region (region)
);

-- Government approvals table
CREATE TABLE government_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    crop_id INT,
    approval_type ENUM('farmer_registration', 'price_approval', 'crop_approval', 'order_approval') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    comments TEXT,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_approval_type (approval_type),
    INDEX idx_status (status)
);

-- Disputes table
CREATE TABLE disputes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispute_number VARCHAR(20) UNIQUE NOT NULL,
    complainant_id INT NOT NULL,
    respondent_id INT NOT NULL,
    order_id INT,
    dispute_type ENUM('quality', 'delivery', 'payment', 'quantity', 'other') NOT NULL,
    description TEXT NOT NULL,
    evidence_urls TEXT,
    status ENUM('open', 'investigating', 'resolved', 'closed') DEFAULT 'open',
    resolution TEXT,
    resolved_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (complainant_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (respondent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_dispute_type (dispute_type)
);

-- Logistics table
CREATE TABLE logistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    transporter_name VARCHAR(100),
    vehicle_number VARCHAR(20),
    driver_name VARCHAR(100),
    driver_phone VARCHAR(15),
    pickup_date DATE,
    delivery_date DATE,
    status ENUM('scheduled', 'picked_up', 'in_transit', 'delivered') DEFAULT 'scheduled',
    tracking_number VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
);

-- Market data table
CREATE TABLE market_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    variety VARCHAR(100),
    market_name VARCHAR(100),
    region VARCHAR(100),
    min_price DECIMAL(10,2),
    max_price DECIMAL(10,2),
    avg_price DECIMAL(10,2),
    quantity_available DECIMAL(10,2),
    unit VARCHAR(20),
    data_date DATE,
    source VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_crop_name (crop_name),
    INDEX idx_region (region),
    INDEX idx_data_date (data_date)
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    related_id INT,
    related_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
);

-- System settings table
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (username, email, password, full_name, user_type, status) 
VALUES ('admin', 'admin@smartagriculture.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'approved');

-- Insert default government user
INSERT INTO users (username, email, password, full_name, user_type, status) 
VALUES ('govt', 'govt@smartagriculture.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Government Official', 'government', 'approved');

-- Insert sample price recommendations
INSERT INTO price_recommendations (crop_name, variety, recommended_price, market_price, msp_price, region, season, quality_grade) VALUES
('Rice', 'Basmati', 45.00, 42.00, 40.00, 'Punjab', 'Kharif', 'A'),
('Wheat', 'Durum', 25.00, 23.00, 22.00, 'Haryana', 'Rabi', 'A'),
('Cotton', 'BT Cotton', 65.00, 62.00, 60.00, 'Gujarat', 'Kharif', 'A'),
('Sugarcane', 'Co 86032', 35.00, 33.00, 32.00, 'Maharashtra', 'Year Round', 'A'),
('Maize', 'Hybrid', 20.00, 18.00, 17.00, 'Karnataka', 'Kharif', 'A');

-- Insert system settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('site_name', 'Smart Agriculture System', 'Name of the website'),
('site_email', 'info@smartagriculture.com', 'Contact email for the site'),
('max_crop_images', '5', 'Maximum number of images per crop'),
('min_order_quantity', '10', 'Minimum order quantity in kg'),
('max_order_quantity', '10000', 'Maximum order quantity in kg'),
('default_currency', 'INR', 'Default currency for the system'),
('commission_rate', '2.5', 'Commission rate percentage'),
('auto_approve_farmers', 'false', 'Auto approve farmer registrations'),
('auto_approve_crops', 'false', 'Auto approve crop listings');

-- Create views for common queries
CREATE VIEW farmer_crop_summary AS
SELECT 
    u.id as farmer_id,
    u.full_name as farmer_name,
    u.city,
    u.state,
    COUNT(c.id) as total_crops,
    SUM(CASE WHEN c.status = 'available' THEN 1 ELSE 0 END) as available_crops,
    SUM(CASE WHEN c.status = 'sold' THEN 1 ELSE 0 END) as sold_crops,
    AVG(c.price_per_unit) as avg_price
FROM users u
LEFT JOIN crops c ON u.id = c.farmer_id
WHERE u.user_type = 'farmer'
GROUP BY u.id, u.full_name, u.city, u.state;

CREATE VIEW buyer_order_summary AS
SELECT 
    u.id as buyer_id,
    u.full_name as buyer_name,
    u.city,
    u.state,
    COUNT(o.id) as total_orders,
    SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(o.total_price) as total_spent
FROM users u
LEFT JOIN orders o ON u.id = o.buyer_id
WHERE u.user_type = 'buyer'
GROUP BY u.id, u.full_name, u.city, u.state;

CREATE VIEW crop_market_summary AS
SELECT 
    c.crop_name,
    c.variety,
    COUNT(c.id) as total_listings,
    AVG(c.price_per_unit) as avg_price,
    MIN(c.price_per_unit) as min_price,
    MAX(c.price_per_unit) as max_price,
    SUM(c.quantity) as total_quantity
FROM crops c
WHERE c.status = 'available'
GROUP BY c.crop_name, c.variety;

-- Create stored procedures
DELIMITER //

CREATE PROCEDURE GetCropRecommendations(IN crop_name VARCHAR(100), IN region VARCHAR(100))
BEGIN
    SELECT 
        pr.crop_name,
        pr.variety,
        pr.recommended_price,
        pr.market_price,
        pr.msp_price,
        pr.region,
        pr.season,
        pr.quality_grade,
        pr.created_at
    FROM price_recommendations pr
    WHERE pr.crop_name = crop_name 
    AND (region IS NULL OR pr.region = region)
    ORDER BY pr.created_at DESC
    LIMIT 10;
END //

CREATE PROCEDURE GetFarmerStats(IN farmer_id INT)
BEGIN
    SELECT 
        COUNT(c.id) as total_crops,
        SUM(CASE WHEN c.status = 'available' THEN 1 ELSE 0 END) as available_crops,
        SUM(CASE WHEN c.status = 'sold' THEN 1 ELSE 0 END) as sold_crops,
        SUM(CASE WHEN c.status = 'sold' THEN c.price_per_unit * c.quantity ELSE 0 END) as total_earnings,
        AVG(c.price_per_unit) as avg_price
    FROM crops c
    WHERE c.farmer_id = farmer_id;
END //

CREATE PROCEDURE GetBuyerStats(IN buyer_id INT)
BEGIN
    SELECT 
        COUNT(o.id) as total_orders,
        SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(o.total_price) as total_spent,
        AVG(o.total_price) as avg_order_value
    FROM orders o
    WHERE o.buyer_id = buyer_id;
END //

DELIMITER ;

-- Create triggers for automatic updates
DELIMITER //

CREATE TRIGGER update_crop_status_after_order
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    UPDATE crops 
    SET status = 'sold' 
    WHERE id = NEW.crop_id AND quantity <= NEW.quantity;
END //

CREATE TRIGGER generate_order_number
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_number IS NULL OR NEW.order_number = '' THEN
        SET NEW.order_number = CONCAT('ORD', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(LAST_INSERT_ID() + 1, 4, '0'));
    END IF;
END //

CREATE TRIGGER generate_dispute_number
BEFORE INSERT ON disputes
FOR EACH ROW
BEGIN
    IF NEW.dispute_number IS NULL OR NEW.dispute_number = '' THEN
        SET NEW.dispute_number = CONCAT('DSP', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(LAST_INSERT_ID() + 1, 4, '0'));
    END IF;
END //

DELIMITER ;

-- Create indexes for better performance
CREATE INDEX idx_crops_harvest_date ON crops(harvest_date);
CREATE INDEX idx_orders_delivery_date ON orders(delivery_date);
CREATE INDEX idx_notifications_created_at ON notifications(created_at);
CREATE INDEX idx_market_data_crop_date ON market_data(crop_name, data_date);

-- Grant permissions (adjust as needed for your setup)
-- GRANT ALL PRIVILEGES ON smart_agriculture.* TO 'agri_user'@'localhost' IDENTIFIED BY 'agri_password';
-- FLUSH PRIVILEGES;
