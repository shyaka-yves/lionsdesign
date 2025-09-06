<?php
// Specific email test for lionsdesignltd.com hosting environment
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
        
        // Environment information
        $debug_info .= "<strong>Environment:</strong> Hosting (lionsdesignltd.com)<br>";
        $debug_info .= "<strong>Server:</strong> " . $_SERVER['HTTP_HOST'] . "<br>";
        $debug_info .= "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
        $debug_info .= "<strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
        
        // Check PHPMailer availability
        if (file_exists('vendor/autoload.php')) {
            $debug_info .= "<strong>PHPMailer:</strong> Available via Composer ✅<br>";
        } elseif (file_exists('src/PHPMailer.php')) {
            $debug_info .= "<strong>PHPMailer:</strong> Available via src/ directory ✅<br>";
        } else {
            $debug_info .= "<strong>PHPMailer:</strong> Not found ❌<br>";
        }
        
        // Check if we're in hosting environment
        $is_hosting = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']);
        $debug_info .= "<strong>Environment Detection:</strong> " . ($is_hosting ? 'Hosting' : 'Local') . "<br>";
        
        // Test email sending
        $start_time = microtime(true);
        if (sendOTPEmail($test_email, $otp, 'signup')) {
            $end_time = microtime(true);
            $execution_time = round(($end_time - $start_time) * 1000, 2);
            $message = "✅ Test email sent successfully to $test_email with OTP: $otp<br><small>Execution time: {$execution_time}ms</small>";
        } else {
            $error = "❌ Failed to send test email.";
            $last_error = isset($GLOBALS['LAST_EMAIL_ERROR']) ? $GLOBALS['LAST_EMAIL_ERROR'] : '';
            if ($last_error) {
                $error .= "<br><strong>Error Details:</strong> " . htmlspecialchars($last_error);
            }
            
            // Additional debugging for hosting
            $debug_info .= "<strong>Gmail App Password:</strong> " . (strlen('uaur ahxe gqvb iemd') > 0 ? 'Configured ✅' : 'Not configured ❌') . "<br>";
            $debug_info .= "<strong>SMTP Port 587:</strong> " . (checkPort('smtp.gmail.com', 587) ? 'Open ✅' : 'Blocked/Filtered ❌') . "<br>";
            $debug_info .= "<strong>SMTP Port 465:</strong> " . (checkPort('smtp.gmail.com', 465) ? 'Open ✅' : 'Blocked/Filtered ❌') . "<br>";
        }
    }
}

// Function to check if port is accessible
function checkPort($host, $port) {
    $connection = @fsockopen($host, $port, $errno, $errstr, 5);
    if ($connection) {
        fclose($connection);
        return true;
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - Lions Design Hosting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .debug-section { background: #f8f9fa; padding: 1rem; border-radius: 0.375rem; margin: 1rem 0; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .hosting-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header hosting-info">
                        <h3 class="mb-0">🚀 Lions Design Email Test - lionsdesignltd.com</h3>
                        <small>Optimized for your hosting environment</small>
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
                            <h5>🔍 Hosting Environment Debug</h5>
                            <?php echo $debug_info; ?>
                        </div>
                        
                        <div class="alert alert-info">
                            <h5>📋 Troubleshooting for lionsdesignltd.com:</h5>
                            <ol>
                                <li><strong>Gmail App Password:</strong> Ensure 2FA is enabled and App Password is correct</li>
                                <li><strong>Hosting SMTP Restrictions:</strong> Some hosts block SMTP ports - check with your hosting provider</li>
                                <li><strong>Firewall Settings:</strong> Ensure outbound connections to smtp.gmail.com are allowed</li>
                                <li><strong>PHP Extensions:</strong> Verify openssl and curl extensions are enabled</li>
                                <li><strong>Alternative Solution:</strong> Consider using Brevo (Sendinblue) which works better on some hosts</li>
                            </ol>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5>⚡ Quick Fixes to Try:</h5>
                            <ul>
                                <li>Try the test with a different email provider (Gmail, Yahoo, Outlook)</li>
                                <li>Check your hosting control panel for SMTP settings</li>
                                <li>Contact your hosting provider about SMTP restrictions</li>
                                <li>Consider using a dedicated email service like Brevo or SendGrid</li>
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