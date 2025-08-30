# Smart Agriculture System

A comprehensive digital platform connecting farmers, buyers, and government officials for transparent and efficient agricultural trade.

## 🌟 Features

### For Farmers
- **Registration & Verification**: Secure farmer registration with government approval
- **Crop Management**: Add, edit, and manage crop listings
- **Sales Tracking**: Monitor orders, sales history, and earnings
- **Quality Assurance**: Grade crops and provide detailed descriptions
- **Direct Communication**: Connect directly with buyers

### For Buyers
- **Easy Registration**: Quick buyer registration with business details
- **Crop Browsing**: Advanced filtering and search capabilities
- **Order Management**: Place orders, track deliveries, and manage history
- **Quality Information**: Access detailed crop specifications and farmer details
- **Secure Transactions**: Multiple payment options and secure processing

### For Government Officials
- **Farmer Approval**: Review and approve farmer registrations
- **Crop Verification**: Approve crop listings for quality and compliance
- **Price Management**: Set and approve Minimum Support Prices (MSP)
- **Reports & Analytics**: Generate comprehensive reports on trade activities
- **Logistics Management**: Monitor and manage transportation

### For Administrators
- **User Management**: Manage all user accounts and permissions
- **Dispute Resolution**: Handle complaints and resolve conflicts
- **System Analytics**: Monitor platform performance and usage
- **Content Management**: Manage system settings and configurations

## 🚀 Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with responsive design
- **Icons**: Font Awesome 6.0
- **Fonts**: Inter (Google Fonts)

## 📁 Project Structure

```
SmartAgricultureSystem/
│
├── index.php                → Homepage (multilingual option)
├── about.php                → About Project
├── contact.php              → Contact Page
├── logout.php               → Logout functionality
│
├── config/
│   └── db.php               → Database connection file
│
├── assets/                  → Static files
│   ├── css/
│   │   └── style.css        → Main stylesheet
│   ├── js/
│   │   └── script.js        → JavaScript functions
│   └── images/              → Logo, banners, crop images
│
├── includes/                → Reusable components
│   ├── header.php           → Common header
│   ├── footer.php           → Common footer
│   ├── navbar.php           → Navigation bar
│   └── language.php         → Language selection logic
│
├── farmer/                  → Farmer Module
│   ├── register.php         → Farmer Registration
│   ├── login.php            → Farmer Login
│   ├── dashboard.php        → Farmer Dashboard
│   ├── add_crop.php         → Add crop details
│   ├── view_status.php      → Check approval status
│   └── sales_history.php    → Farmer's crop sales record
│
├── buyer/                   → Consumer/Buyer Module
│   ├── register.php         → Buyer Registration
│   ├── login.php            → Buyer Login
│   ├── dashboard.php        → Buyer Dashboard
│   ├── browse_crops.php     → View available crops
│   ├── place_order.php      → Place order
│   └── order_history.php    → Past orders
│
├── government/              → Government Module
│   ├── login.php            → Govt login
│   ├── dashboard.php        → Govt dashboard
│   ├── approve_farmers.php  → Approve farmer registrations
│   ├── approve_prices.php   → Approve/Set prices (MSP)
│   ├── reports.php          → Generate reports
│   ├── logistics.php        → Manage transport
│   └── monitor.php          → Monitor sales & transactions
│
├── admin/                   → Admin Dashboard
│   ├── login.php            → Admin login
│   ├── dashboard.php        → Admin dashboard
│   ├── manage_users.php     → Manage all users
│   ├── disputes.php         → Handle disputes/complaints
│   └── analytics.php        → System-wide statistics
│
├── features/                → Extra/Advanced Features
│   ├── price_recommend.php  → Price Recommendation System
│   ├── prediction.php       → AI/ML Crop prediction (optional)
│   ├── payment.php          → Payment Simulation
│   ├── dashboard_live.php   → Real-time Market Dashboard
│   └── multilingual.php     → Language translations (Hindi, Telugu, etc.)
│
└── database/
    └── smart_agriculture.sql → Database dump (all tables & data)
```

## 🛠️ Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (optional, for future enhancements)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd SmartAgricultureSystem
   ```

2. **Database Setup**
   - Create a MySQL database named `smart_agriculture`
   - Import the database schema:
     ```bash
     mysql -u username -p smart_agriculture < database/smart_agriculture.sql
     ```

3. **Configuration**
   - Update database credentials in `config/db.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'smart_agriculture');
     ```

4. **Web Server Setup**
   - Point your web server document root to the project directory
   - Ensure PHP has proper permissions to read/write files
   - Create upload directories if needed:
     ```bash
     mkdir -p assets/images/crops
     chmod 755 assets/images/crops
     ```

5. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost/SmartAgricultureSystem`
   - Default admin credentials:
     - Username: `admin`
     - Password: `admin123`
   - Default government credentials:
     - Username: `govt`
     - Password: `govt123`

## 🌐 Multilingual Support

The system supports multiple languages:
- English (default)
- Hindi (हिन्दी)
- Telugu (తెలుగు)
- Tamil (தமிழ்)
- Bengali (বাংলা)

Language switching is available in the navigation bar.

## 🔐 User Types & Permissions

### Farmers
- Register and get government approval
- Add crop listings
- Manage sales and orders
- View earnings and statistics

### Buyers
- Register and start browsing immediately
- Search and filter crops
- Place orders and track deliveries
- View order history

### Government Officials
- Approve farmer registrations
- Verify crop listings
- Set Minimum Support Prices (MSP)
- Generate reports and analytics
- Manage logistics

### Administrators
- Full system access
- User management
- Dispute resolution
- System configuration
- Analytics and monitoring

## 📊 Database Schema

The system includes comprehensive database tables:

- **users**: User accounts and basic information
- **farmer_details**: Farmer-specific information
- **buyer_details**: Buyer-specific information
- **crops**: Crop listings and details
- **orders**: Order management and tracking
- **government_approvals**: Approval workflow
- **disputes**: Dispute management
- **price_recommendations**: Price guidance
- **logistics**: Transportation management
- **notifications**: System notifications
- **system_settings**: Configuration settings

## 🎨 Design Features

- **Responsive Design**: Works on desktop, tablet, and mobile
- **Modern UI**: Clean, professional interface
- **Accessibility**: WCAG compliant design
- **Performance**: Optimized for fast loading
- **Cross-browser**: Compatible with all modern browsers

## 🔧 Customization

### Adding New Languages
1. Update `includes/language.php` with new language arrays
2. Add language option to the navigation
3. Translate all user-facing strings

### Styling Customization
- Modify `assets/css/style.css` for visual changes
- Update color scheme by changing CSS variables
- Add custom fonts in the header

### Functionality Extensions
- Add new modules in respective directories
- Extend database schema as needed
- Implement additional features in the `features/` directory

## 🚀 Future Enhancements

- **Mobile App**: Native iOS and Android applications
- **AI Integration**: Machine learning for price predictions
- **IoT Integration**: Sensor data from farms
- **Blockchain**: Secure transaction records
- **API Development**: RESTful API for third-party integrations
- **Advanced Analytics**: Business intelligence dashboard
- **Payment Gateway**: Integrated payment processing
- **SMS/Email Notifications**: Automated communication

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Support

For support and questions:
- Email: info@smartagriculture.com
- Phone: +91 98765 43210
- Website: [Smart Agriculture System](http://localhost/SmartAgricultureSystem)

## 🙏 Acknowledgments

- Government of India for agricultural policies
- Farmers and agricultural experts for insights
- Open source community for tools and libraries
- Contributors and testers

---

**Smart Agriculture System** - Connecting farmers, buyers, and government for a better tomorrow.
