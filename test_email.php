<?php
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
        // Generate test OTP
        $otp = generateOTP();
        
        // Try to send email
        if (sendOTPEmail($test_email, $otp, 'signup')) {
            $message = "Test email sent successfully to $test_email with OTP: $otp";
        } else {
            $error = "Failed to send test email.";
            $last_error = isset($GLOBALS['LAST_EMAIL_ERROR']) ? $GLOBALS['LAST_EMAIL_ERROR'] : '';
            $last_debug = isset($GLOBALS['LAST_EMAIL_DEBUG']) ? nl2br(htmlspecialchars($GLOBALS['LAST_EMAIL_DEBUG'])) : '';
            $debug_info = "Please make sure you have configured your email credentials in config/email.php";
            if ($last_error) {
                $debug_info .= "<br><strong>Mailer Error:</strong> " . htmlspecialchars($last_error);
            }
            if ($last_debug) {
                $debug_info .= "<br><strong>SMTP Debug:</strong><br>" . $last_debug;
            }
        }
    }
}

// Load email config for display with error handling
$config_file = __DIR__ . '/config/email.php';
$debug_info .= "<br><strong>Debug Info:</strong><br>";
$debug_info .= "Config file path: " . $config_file . "<br>";
$debug_info .= "Config file exists: " . (file_exists($config_file) ? 'Yes' : 'No') . "<br>";

if (file_exists($config_file)) {
    $email_config = require $config_file;
    if (is_array($email_config)) {
        $debug_info .= "Config loaded successfully<br>";
        $debug_info .= "Config array keys: " . implode(', ', array_keys($email_config)) . "<br>";
        $debug_info .= "SMTP Host value: '" . $email_config['smtp_host'] . "'<br>";
        $debug_info .= "SMTP Port value: '" . $email_config['smtp_port'] . "'<br>";
        $debug_info .= "Username value: '" . $email_config['smtp_username'] . "'<br>";
    } else {
        $debug_info .= "Config file loaded but returned: " . gettype($email_config) . "<br>";
        $email_config = [
            'smtp_host' => 'Error loading config',
            'smtp_port' => 'Error loading config',
            'smtp_username' => 'Error loading config',
            'smtp_password' => 'Error loading config'
        ];
    }
} else {
    $debug_info .= "Config file not found!<br>";
    $email_config = [
        'smtp_host' => 'Config file missing',
        'smtp_port' => 'Config file missing',
        'smtp_username' => 'Config file missing',
        'smtp_password' => 'Config file missing'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Email Test - Lions Design</h3>
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
                        
                        <div class="alert alert-info">
                            <h5>Email Configuration:</h5>
                            <p><strong>SMTP Host:</strong> <?php echo htmlspecialchars($email_config['smtp_host']); ?></p>
                            <p><strong>SMTP Port:</strong> <?php echo htmlspecialchars($email_config['smtp_port']); ?></p>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($email_config['smtp_username']); ?></p>
                            <p><strong>Password:</strong> <?php echo $email_config['smtp_password'] === 'your-gmail-app-password' ? 'Not configured' : 'Configured'; ?></p>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5>Setup Instructions:</h5>
                            <ol>
                                <li>For Gmail SMTP:
                                    <ul>
                                        <li>Enable 2-factor authentication on your Gmail account</li>
                                        <li>Generate an App Password: <a href="https://myaccount.google.com/apppasswords" target="_blank">https://myaccount.google.com/apppasswords</a></li>
                                        <li>Replace 'your-gmail-app-password' in config/email.php with your actual App Password</li>
                                    </ul>
                                </li>
                                <li>For Brevo (Sendinblue):
                                    <ul>
                                        <li>Sign up at <a href="https://www.brevo.com/" target="_blank">https://www.brevo.com/</a></li>
                                        <li>Get your API key from the dashboard</li>
                                        <li>Update the config/email.php file with your Brevo credentials</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                        
                        <?php if ($debug_info): ?>
                            <div class="alert alert-secondary">
                                <h5>Debug Information:</h5>
                                <p><?php echo $debug_info; ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
