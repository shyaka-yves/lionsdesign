<?php
// Working email test with proper error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$message = '';
$error = '';
$test_results = [];

// Test basic functionality first
$test_results[] = "🔍 Starting email test for lionsdesignltd.com...";
$test_results[] = "PHP Version: " . phpversion();
$test_results[] = "Current time: " . date('Y-m-d H:i:s');

// Check if files exist
$required_files = [
    'config/database.php',
    'includes/functions.php',
    'vendor/autoload.php',
    'config/email.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        $test_results[] = "✅ $file exists";
    } else {
        $test_results[] = "❌ $file missing";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_email = $_POST['test_email'] ?? '';
    
    if (empty($test_email)) {
        $error = 'Please enter an email address.';
    } elseif (!filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $test_results[] = "📧 Testing email: $test_email";
        
        try {
            // Load database connection
            require_once 'config/database.php';
            $test_results[] = "✅ Database config loaded";
            
            // Load functions
            require_once 'includes/functions.php';
            $test_results[] = "✅ Functions loaded";
            
            // Generate OTP
            $otp = generateOTP();
            $test_results[] = "✅ OTP generated: $otp";
            
            // Load email config
            $email_config = require 'config/email.php';
            $test_results[] = "✅ Email config loaded";
            $test_results[] = "SMTP Host: " . $email_config['smtp_host'];
            $test_results[] = "SMTP Port: " . $email_config['smtp_port'];
            
            // Test PHPMailer
            require_once 'vendor/autoload.php';
            $test_results[] = "✅ PHPMailer loaded";
            
            // Try to send email
            $test_results[] = "📤 Attempting to send email...";
            $start_time = microtime(true);
            
            if (sendOTPEmail($test_email, $otp, 'signup')) {
                $end_time = microtime(true);
                $execution_time = round(($end_time - $start_time) * 1000, 2);
                $message = "✅ SUCCESS! Email sent to $test_email<br>OTP: $otp<br>Time: {$execution_time}ms";
                $test_results[] = "✅ Email sent successfully in {$execution_time}ms";
            } else {
                $end_time = microtime(true);
                $execution_time = round(($end_time - $start_time) * 1000, 2);
                $error = "❌ FAILED to send email after {$execution_time}ms";
                $test_results[] = "❌ Email sending failed after {$execution_time}ms";
                
                // Check for last error
                if (isset($GLOBALS['LAST_EMAIL_ERROR'])) {
                    $error .= "<br><strong>Error:</strong> " . htmlspecialchars($GLOBALS['LAST_EMAIL_ERROR']);
                    $test_results[] = "Error: " . $GLOBALS['LAST_EMAIL_ERROR'];
                }
            }
            
        } catch (Exception $e) {
            $error = "❌ Exception: " . $e->getMessage();
            $test_results[] = "❌ Exception: " . $e->getMessage();
            $test_results[] = "File: " . $e->getFile() . " Line: " . $e->getLine();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Working Email Test - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result { margin: 0.5rem 0; padding: 0.5rem; border-radius: 0.25rem; }
        .test-result.success { background: #d4edda; color: #155724; }
        .test-result.error { background: #f8d7da; color: #721c24; }
        .test-result.info { background: #d1ecf1; color: #0c5460; }
        /* New theme colors for visual confirmation on live */
        .testing-header { background: linear-gradient(90deg, #7c3aed, #06b6d4); color: #ffffff; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header testing-header">
                        <h3 class="mb-0">✅ Working Email Test</h3>
                        <small>Fixed version for lionsdesignltd.com</small>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" class="mb-4">
                            <div class="mb-3">
                                <label for="test_email" class="form-label">Test Email Address:</label>
                                <input type="email" class="form-control" id="test_email" name="test_email" 
                                       placeholder="Enter email address to test" required>
                            </div>
                            <button type="submit" class="btn btn-warning">Send Test Email</button>
                        </form>
                        
                        <?php if (!empty($test_results)): ?>
                        <div class="mt-4">
                            <h5>🔍 Test Results:</h5>
                            <?php foreach ($test_results as $result): ?>
                                <div class="test-result <?php 
                                    if (strpos($result, '✅') !== false) echo 'success';
                                    elseif (strpos($result, '❌') !== false) echo 'error';
                                    else echo 'info';
                                ?>">
                                    <?php echo htmlspecialchars($result); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info mt-4">
                            <h5>📋 What This Test Does:</h5>
                            <ol>
                                <li>Checks all required files exist</li>
                                <li>Loads database and function files</li>
                                <li>Generates a test OTP</li>
                                <li>Loads email configuration</li>
                                <li>Tests PHPMailer functionality</li>
                                <li>Attempts to send actual email</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>