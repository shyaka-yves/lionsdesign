<?php
// Comprehensive email test for hosting environments
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$error = '';
$debug_info = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_email = $_POST['test_email'] ?? '';
    
    if (empty($test_email)) {
        $error = 'Please enter an email address.';
    } elseif (!filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Test email sending
        $otp = generateOTP();
        
        // Check environment
        $is_hosting = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']);
        $debug_info .= "<strong>Environment:</strong> " . ($is_hosting ? 'Hosting' : 'Local') . "<br>";
        $debug_info .= "<strong>Server:</strong> " . $_SERVER['HTTP_HOST'] . "<br>";
        $debug_info .= "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
        
        // Check PHPMailer availability
        if (file_exists('vendor/autoload.php')) {
            $debug_info .= "<strong>PHPMailer:</strong> Available via Composer<br>";
        } elseif (file_exists('src/PHPMailer.php')) {
            $debug_info .= "<strong>PHPMailer:</strong> Available via src/ directory<br>";
        } else {
            $debug_info .= "<strong>PHPMailer:</strong> Not found<br>";
        }
        
        // Test email sending
        if (sendOTPEmail($test_email, $otp, 'signup')) {
            $message = "✅ Test email sent successfully to $test_email with OTP: $otp";
        } else {
            $error = "❌ Failed to send test email.";
            $last_error = isset($GLOBALS['LAST_EMAIL_ERROR']) ? $GLOBALS['LAST_EMAIL_ERROR'] : '';
            if ($last_error) {
                $error .= "<br><strong>Error Details:</strong> " . htmlspecialchars($last_error);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Hosting Test - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .debug-section { background: #f8f9fa; padding: 1rem; border-radius: 0.375rem; margin: 1rem 0; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">🔧 Email Hosting Test - Lions Design</h3>
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
                            <button type="submit" class="btn btn-primary">Send Test Email</button>
                        </form>
                        
                        <div class="debug-section">
                            <h5>🔍 Debug Information</h5>
                            <?php echo $debug_info; ?>
                        </div>
                        
                        <div class="alert alert-info">
                            <h5>📋 Troubleshooting Steps for Hosting:</h5>
                            <ol>
                                <li><strong>Gmail App Password:</strong> Ensure 2FA is enabled and App Password is correct</li>
                                <li><strong>SMTP Ports:</strong> Check if hosting allows ports 587 and 465</li>
                                <li><strong>SSL/TLS:</strong> Verify SSL certificate verification settings</li>
                                <li><strong>Firewall:</strong> Ensure outbound SMTP connections are allowed</li>
                                <li><strong>PHP Extensions:</strong> Check if openssl and curl extensions are enabled</li>
                                <li><strong>Alternative Providers:</strong> Consider using your host's professional mailbox only</li>
                            </ol>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5>⚙️ Alternative Email Providers:</h5>
                            <p>If Gmail SMTP doesn't work on your hosting, try these alternatives:</p>
                            <ul>
                                <li>Use your hosting provider's SMTP with proper credentials.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>