<?php
// Detailed email test with step-by-step debugging
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$error = '';
$debug_info = '';
$test_results = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_email = $_POST['test_email'] ?? '';
    
    if (empty($test_email)) {
        $error = 'Please enter an email address.';
    } elseif (!filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Test email sending with detailed logging
        $otp = generateOTP();
        
        // Step 1: Test basic PHPMailer functionality
        $test_results[] = "Step 1: Testing PHPMailer basic functionality...";
        try {
            require_once 'vendor/autoload.php';
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $test_results[] = "✅ PHPMailer class loaded successfully";
        } catch (Exception $e) {
            $test_results[] = "❌ PHPMailer error: " . $e->getMessage();
        }
        
        // Step 2: Test configuration loading
        $test_results[] = "Step 2: Testing email configuration...";
        $config_file = 'config/email.php';
        if (file_exists($config_file)) {
            $email_config = require $config_file;
            $test_results[] = "✅ Email config loaded successfully";
            $test_results[] = "Config details: Host=" . $email_config['smtp_host'] . ", Port=" . $email_config['smtp_port'];
        } else {
            $test_results[] = "❌ Email config file not found";
        }
        
        // Step 3: Test SMTP connection
        $test_results[] = "Step 3: Testing SMTP connection...";
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $email_config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $email_config['smtp_username'];
            $mail->Password = $email_config['smtp_password'];
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $email_config['smtp_port'];
            $mail->Timeout = 30;
            
            // Enable debug output
            $mail->SMTPDebug = 2;
            $debug_output = '';
            $mail->Debugoutput = function ($str, $level) use (&$debug_output) {
                $debug_output .= '[' . $level . '] ' . $str . "\n";
            };
            
            $test_results[] = "✅ SMTP settings configured";
            
            // Test connection (without sending)
            $mail->smtpConnect();
            $test_results[] = "✅ SMTP connection successful";
            $mail->smtpClose();
            
        } catch (Exception $e) {
            $test_results[] = "❌ SMTP connection failed: " . $e->getMessage();
            $test_results[] = "Debug output: " . $debug_output;
        }
        
        // Step 4: Test actual email sending
        $test_results[] = "Step 4: Testing email sending...";
        $start_time = microtime(true);
        
        if (sendOTPEmail($test_email, $otp, 'signup')) {
            $end_time = microtime(true);
            $execution_time = round(($end_time - $start_time) * 1000, 2);
            $message = "✅ Test email sent successfully to $test_email with OTP: $otp<br><small>Execution time: {$execution_time}ms</small>";
            $test_results[] = "✅ Email sent successfully in {$execution_time}ms";
        } else {
            $end_time = microtime(true);
            $execution_time = round(($end_time - $start_time) * 1000, 2);
            $error = "❌ Failed to send test email after {$execution_time}ms";
            $test_results[] = "❌ Email sending failed after {$execution_time}ms";
            
            $last_error = isset($GLOBALS['LAST_EMAIL_ERROR']) ? $GLOBALS['LAST_EMAIL_ERROR'] : '';
            if ($last_error) {
                $error .= "<br><strong>Error Details:</strong> " . htmlspecialchars($last_error);
                $test_results[] = "Error: " . $last_error;
            }
        }
        
        // Step 5: Check email logs
        $test_results[] = "Step 5: Checking email logs...";
        $log_file = 'email_debug.log';
        if (file_exists($log_file)) {
            $test_results[] = "✅ Email debug log found";
            $test_results[] = "Last 5 log entries:";
            $logs = file($log_file);
            $recent_logs = array_slice($logs, -5);
            foreach ($recent_logs as $log) {
                $test_results[] = "  " . trim($log);
            }
        } else {
            $test_results[] = "ℹ️ No email debug log found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Email Test - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .debug-section { background: #f8f9fa; padding: 1rem; border-radius: 0.375rem; margin: 1rem 0; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .test-step { margin: 0.5rem 0; padding: 0.5rem; background: #e9ecef; border-radius: 0.25rem; }
        .test-step.success { background: #d4edda; color: #155724; }
        .test-step.error { background: #f8d7da; color: #721c24; }
        .test-step.info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">🔬 Detailed Email Test - lionsdesignltd.com</h3>
                        <small>Step-by-step debugging for your hosting environment</small>
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
                            <button type="submit" class="btn btn-primary">Run Detailed Test</button>
                        </form>
                        
                        <?php if (!empty($test_results)): ?>
                        <div class="debug-section">
                            <h5>🔍 Step-by-Step Test Results</h5>
                            <?php foreach ($test_results as $result): ?>
                                <div class="test-step <?php 
                                    if (strpos($result, '✅') !== false) echo 'success';
                                    elseif (strpos($result, '❌') !== false) echo 'error';
                                    else echo 'info';
                                ?>">
                                    <?php echo htmlspecialchars($result); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info">
                            <h5>📋 What This Test Does:</h5>
                            <ol>
                                <li><strong>PHPMailer Test:</strong> Verifies PHPMailer is working correctly</li>
                                <li><strong>Config Test:</strong> Checks if email configuration loads properly</li>
                                <li><strong>SMTP Test:</strong> Tests connection to Gmail SMTP servers</li>
                                <li><strong>Send Test:</strong> Attempts to send an actual email</li>
                                <li><strong>Log Check:</strong> Reviews any error logs for debugging</li>
                            </ol>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5>🚨 Common Issues on LiteSpeed Hosting:</h5>
                            <ul>
                                <li><strong>SSL/TLS Issues:</strong> Some LiteSpeed configurations have SSL problems</li>
                                <li><strong>Memory Limits:</strong> PHPMailer might need more memory</li>
                                <li><strong>Timeout Issues:</strong> SMTP connections might timeout</li>
                                <li><strong>Firewall Rules:</strong> Outbound connections might be restricted</li>
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