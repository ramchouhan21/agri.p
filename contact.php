<?php
session_start();
include 'includes/language.php';

$message = '';
$message_type = '';

if ($_POST) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message_text = $_POST['message'] ?? '';
    
    if ($name && $email && $subject && $message_text) {
        // In a real application, you would send an email or save to database
        $message = $lang['contact_success'];
        $message_type = 'success';
    } else {
        $message = $lang['contact_error'];
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['contact_title']; ?> - Smart Agriculture System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1><?php echo $lang['contact_title']; ?></h1>
                <p><?php echo $lang['contact_subtitle']; ?></p>
            </div>

            <div class="contact-content">
                <div class="contact-info">
                    <h2><?php echo $lang['get_in_touch']; ?></h2>
                    <div class="contact-methods">
                        <div class="contact-method">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h3><?php echo $lang['address']; ?></h3>
                                <p>123 Agriculture Street<br>Farm District, State 12345</p>
                            </div>
                        </div>
                        <div class="contact-method">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h3><?php echo $lang['phone']; ?></h3>
                                <p>+91 98765 43210</p>
                            </div>
                        </div>
                        <div class="contact-method">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h3><?php echo $lang['email']; ?></h3>
                                <p>info@smartagriculture.com</p>
                            </div>
                        </div>
                        <div class="contact-method">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h3><?php echo $lang['working_hours']; ?></h3>
                                <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <h2><?php echo $lang['send_message']; ?></h2>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name"><?php echo $lang['name']; ?> *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><?php echo $lang['email']; ?> *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject"><?php echo $lang['subject']; ?> *</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message"><?php echo $lang['message']; ?> *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary"><?php echo $lang['send_message']; ?></button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>
