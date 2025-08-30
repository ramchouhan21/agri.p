<?php
session_start();
include 'includes/language.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['about_title']; ?> - Smart Agriculture System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1><?php echo $lang['about_title']; ?></h1>
                <p><?php echo $lang['about_subtitle']; ?></p>
            </div>

            <section class="about-content">
                <div class="about-text">
                    <h2><?php echo $lang['our_mission']; ?></h2>
                    <p><?php echo $lang['mission_text']; ?></p>
                    
                    <h2><?php echo $lang['our_vision']; ?></h2>
                    <p><?php echo $lang['vision_text']; ?></p>
                    
                    <h2><?php echo $lang['key_benefits']; ?></h2>
                    <ul class="benefits-list">
                        <li><i class="fas fa-check"></i> <?php echo $lang['benefit_transparency']; ?></li>
                        <li><i class="fas fa-check"></i> <?php echo $lang['benefit_fair_pricing']; ?></li>
                        <li><i class="fas fa-check"></i> <?php echo $lang['benefit_direct_connection']; ?></li>
                        <li><i class="fas fa-check"></i> <?php echo $lang['benefit_government_support']; ?></li>
                        <li><i class="fas fa-check"></i> <?php echo $lang['benefit_quality_assurance']; ?></li>
                    </ul>
                </div>
                
                <div class="about-image">
                    <img src="assets/images/about-agriculture.jpg" alt="About Smart Agriculture">
                </div>
            </section>

            <section class="team-section">
                <h2><?php echo $lang['our_team']; ?></h2>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3><?php echo $lang['tech_team']; ?></h3>
                        <p><?php echo $lang['tech_team_desc']; ?></p>
                    </div>
                    <div class="team-member">
                        <div class="member-avatar">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3><?php echo $lang['agriculture_experts']; ?></h3>
                        <p><?php echo $lang['agriculture_experts_desc']; ?></p>
                    </div>
                    <div class="team-member">
                        <div class="member-avatar">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3><?php echo $lang['government_partners']; ?></h3>
                        <p><?php echo $lang['government_partners_desc']; ?></p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>
