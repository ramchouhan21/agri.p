# Smart Agriculture System - Installation Guide

## Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Basic knowledge of PHP and MySQL

### Step 1: Download and Setup
1. Download or clone the project files
2. Place the files in your web server directory (e.g., `htdocs`, `www`, or `public_html`)
3. Ensure proper file permissions

### Step 2: Database Setup
1. Create a MySQL database named `smart_agriculture`
2. Import the database schema:
   ```bash
   mysql -u your_username -p smart_agriculture < database/smart_agriculture.sql
   ```

### Step 3: Configuration
1. Open `config/db.php`
2. Update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'smart_agriculture');
   ```

### Step 4: File Permissions
Create upload directories and set permissions:
```bash
mkdir -p assets/images/crops
chmod 755 assets/images/crops
```

### Step 5: Access the System
1. Open your web browser
2. Navigate to your project URL
3. Default login credentials:
   - **Admin**: username: `admin`, password: `admin123`
   - **Government**: username: `govt`, password: `govt123`

## User Registration Flow

### For Farmers:
1. Register at `/farmer/register.php`
2. Wait for government approval
3. Once approved, login and add crops

### For Buyers:
1. Register at `/buyer/register.php`
2. Start browsing crops immediately
3. Place orders and track deliveries

### For Government Officials:
1. Login with government credentials
2. Approve farmer registrations
3. Verify crop listings
4. Set Minimum Support Prices (MSP)

## Features Overview

### ✅ Implemented Features
- **Multilingual Support**: English, Hindi, Telugu, Tamil, Bengali
- **User Management**: Registration, login, profiles
- **Farmer Module**: Crop listing, sales tracking, approval workflow
- **Buyer Module**: Crop browsing, ordering, order history
- **Government Module**: Approval system, price management
- **Admin Module**: System monitoring, user management
- **Responsive Design**: Works on all devices
- **Database Integration**: Complete MySQL schema

### 🔄 Workflow
1. **Farmers** register and get government approval
2. **Government** verifies farmer credentials and crop listings
3. **Buyers** browse approved crops and place orders
4. **System** tracks orders, deliveries, and payments
5. **Admin** monitors platform performance and resolves disputes

## Troubleshooting

### Common Issues

**Database Connection Error**
- Check database credentials in `config/db.php`
- Ensure MySQL service is running
- Verify database exists

**File Upload Issues**
- Check directory permissions
- Ensure `assets/images/crops/` exists
- Set proper PHP upload limits

**Login Issues**
- Use default credentials: admin/admin123, govt/govt123
- Check user status in database
- Clear browser cache

**Language Issues**
- Language files are in `includes/language.php`
- Add new languages by extending the language arrays
- Ensure proper UTF-8 encoding

## Security Notes

- Change default passwords immediately
- Use HTTPS in production
- Regular database backups
- Keep PHP and MySQL updated
- Implement proper file upload validation

## Support

For technical support:
- Check the README.md for detailed documentation
- Review the database schema in `database/smart_agriculture.sql`
- Examine the code structure for customization

## Next Steps

After installation:
1. Change default passwords
2. Configure email settings (if needed)
3. Set up proper file upload handling
4. Customize the interface as needed
5. Add additional features as required

---

**Smart Agriculture System** - Ready to connect farmers, buyers, and government officials!
