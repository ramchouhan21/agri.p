<?php
/**
 * Farmer Login Page
 * 
 * This page handles farmer authentication with email and password.
 * Includes form validation, remember me functionality, and password recovery.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in
if (isset($_SESSION['farmer_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Include database connection
require_once '../config/db.php';

// Initialize variables
$email = '';
$remember = false;
$error = '';
$success = '';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Initialize PDO connection
    $pdo = getDBConnection();
    // Validate input
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        try {
            // Prepare SQL statement
            $stmt = $pdo->prepare("SELECT id, name, email, password, status FROM farmers WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            // Verify user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
                // Check if account is active
                if ($user['status'] !== 'active') {
                    $error = 'Your account is not active. Please contact support.';
                } else {
                    // Set session variables
                    $_SESSION['farmer_id'] = $user['id'];
                    $_SESSION['farmer_name'] = $user['name'];
                    $_SESSION['farmer_email'] = $user['email'];
                    
                    // Set remember me cookie if requested (valid for 30 days)
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $expires = time() + (30 * 24 * 60 * 60); // 30 days
                        
                        // Store token in database
                        $stmt = $pdo->prepare("UPDATE farmers SET remember_token = ?, token_expires_at = ? WHERE id = ?");
                        $stmt->execute([$token, date('Y-m-d H:i:s', $expires), $user['id']]);
                        
                        // Set cookie
                        setcookie('remember_farmer', $token, $expires, '/', '', true, true);
                    }
                    
                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit();
                }
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}

// Check for password reset success message
if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
    $success = 'Your password has been reset successfully. You can now log in with your new password.';
}

// Check for registration success message
if (isset($_GET['registered']) && $_GET['registered'] === 'true') {
    $success = 'Registration successful! You can now log in with your credentials.';
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Login - SmartAgri</title>
    <meta name="description" content="Login to your SmartAgri farmer account to manage your crops, orders, and more.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom styles -->
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        
        .form-input:focus, .form-checkbox:focus {
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
            border-color: #10B981;
        }
        
        .btn-primary {
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        /* Animation for form */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
        
        /* Loading spinner */
        .spinner {
            width: 1.5rem;
            height: 1.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            display: inline-block;
            vertical-align: middle;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #9CA3AF;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #6B7280;
        }
        
        /* Dark mode styles */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #111827;
                color: #E5E7EB;
            }
            
            .bg-white {
                background-color: #1F2937;
                color: #E5E7EB;
            }
            
            .text-gray-700 {
                color: #D1D5DB;
            }
            
            .text-gray-900 {
                color: #F3F4F6;
            }
            
            .border-gray-200 {
                border-color: #374151;
            }
            
            .bg-gray-50 {
                background-color: #111827;
            }
            
            .form-input {
                background-color: #1F2937;
                border-color: #4B5563;
                color: #E5E7EB;
            }
            
            .form-input:focus {
                border-color: #10B981;
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
            }
            
            .form-checkbox {
                background-color: #1F2937;
                border-color: #4B5563;
            }
            
            .form-checkbox:checked {
                background-color: #10B981;
                border-color: #10B981;
            }
            
            .text-gray-500 {
                color: #9CA3AF;
            }
            
            .text-gray-400 {
                color: #9CA3AF;
            }
            
            .bg-gray-100 {
                background-color: #1F2937;
            }
        }
    </style>
</head>
<body class="h-full">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 animate-fade-in-up">
            <div class="text-center">
                <div class="flex justify-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">Welcome back</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Sign in to your farmer account
                </p>
                
                <?php if ($error): ?>
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-md text-sm">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-md text-sm">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <form class="mt-8 space-y-6" action="" method="POST" id="loginForm">
                <input type="hidden" name="remember" value="true">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email address</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                   class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm transition duration-150 ease-in-out" 
                                   placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>"
                                   oninvalid="this.setCustomValidity('Please enter a valid email address')" 
                                   oninput="this.setCustomValidity('')">
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                            <div class="text-sm">
                                <a href="forgot-password.php" class="font-medium text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300">
                                    Forgot password?
                                </a>
                            </div>
                        </div>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm transition duration-150 ease-in-out" 
                                   placeholder="••••••••"
                                   oninvalid="this.setCustomValidity('Please enter your password')" 
                                   oninput="this.setCustomValidity('')">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" id="togglePassword">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" id="eyeIcon">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" id="eyeOffIcon">
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center
                    ">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 dark:border-gray-600 rounded form-checkbox" <?php echo $remember ? 'checked' : ''; ?>>
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Remember me
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 btn-primary">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-green-500 group-hover:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span id="submit-text">Sign in</span>
                        <span id="loading-spinner" class="hidden">
                            <span class="spinner"></span>
                            Signing in...
                        </span>
                    </button>
                </div>
            </form>
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                            New to SmartAgri?
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="register.php" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        Create your farmer account
                    </a>
                </div>
            </div>
            
            <div class="mt-6 text-center text-sm">
                <a href="/" class="font-medium text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300">
                    &larr; Back to Home
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript for form handling -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const submitText = document.getElementById('submit-text');
            const loadingSpinner = document.getElementById('loading-spinner');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            // Toggle password visibility
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Toggle eye icons
                    if (type === 'password') {
                        eyeIcon.classList.remove('hidden');
                        eyeOffIcon.classList.add('hidden');
                    } else {
                        eyeIcon.classList.add('hidden');
                        eyeOffIcon.classList.remove('hidden');
                    }
                });
            }
            
            // Form submission
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Prevent double submission
                    if (submitBtn.getAttribute('data-submitting') === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Show loading state
                    submitBtn.setAttribute('data-submitting', 'true');
                    submitBtn.disabled = true;
                    submitText.classList.add('hidden');
                    loadingSpinner.classList.remove('hidden');
                    
                    // You can add additional client-side validation here if needed
                    
                    // If everything is valid, the form will submit
                    // If there are errors, the page will reload with error messages
                });
            }
            
            // Auto-hide success/error messages after 5 seconds
            const alertMessages = document.querySelectorAll('.bg-red-50, .bg-green-50');
            alertMessages.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
            
            // Add animation to form inputs on focus
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                // Add focus styles
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-green-500', 'ring-opacity-50');
                });
                
                // Remove focus styles
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-green-500', 'ring-opacity-50');
                });
                
                // Add animation on first interaction
                input.addEventListener('input', function() {
                    this.classList.add('animated');
                }, { once: true });
            });
        });
    </script>
</body>
</html>
