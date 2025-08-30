<?php
session_start();
include 'includes/language.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['home_title']; ?> - Smart Agriculture System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1><?php echo $lang['hero_title']; ?></h1>
                <p><?php echo $lang['hero_subtitle']; ?></p>
                <div class="hero-buttons">
                    <a href="farmer/register.php" class="btn btn-primary"><?php echo $lang['register_farmer']; ?></a>
                    <a href="buyer/register.php" class="btn btn-secondary"><?php echo $lang['register_buyer']; ?></a>
                </div>
            </div>
            <div class="hero-image">
                <img src="assets/images/hero-agriculture.jpg" alt="Smart Agriculture">
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="container">
                <h2><?php echo $lang['features_title']; ?></h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-seedling"></i>
                        <h3><?php echo $lang['feature_crop_management']; ?></h3>
                        <p><?php echo $lang['feature_crop_desc']; ?></p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-chart-line"></i>
                        <h3><?php echo $lang['feature_price_tracking']; ?></h3>
                        <p><?php echo $lang['feature_price_desc']; ?></p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-truck"></i>
                        <h3><?php echo $lang['feature_logistics']; ?></h3>
                        <p><?php echo $lang['feature_logistics_desc']; ?></p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3><?php echo $lang['feature_government']; ?></h3>
                        <p><?php echo $lang['feature_gov_desc']; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <h3>1000+</h3>
                        <p><?php echo $lang['registered_farmers']; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p><?php echo $lang['active_buyers']; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>50+</h3>
                        <p><?php echo $lang['crop_varieties']; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>₹10M+</h3>
                        <p><?php echo $lang['total_transactions']; ?></p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>
