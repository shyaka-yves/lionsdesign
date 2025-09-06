<?php
// Simple email test script
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$error = '';

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
            $error .= "<br><strong>Error:</strong> " . htmlspecialchars($last_error);
            if ($last_debug) {
                $error .= "<br><strong>Debug:</strong><br>" . $last_debug;
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
                            <h5>Instructions:</h5>
                            <ol>
                                <li>Make sure Gmail App Password is correctly configured in config/email.php</li>
                                <li>Check that 2-factor authentication is enabled on the Gmail account</li>
                                <li>Verify the hosting environment allows SMTP connections</li>
                                <li>Check server error logs for additional debugging information</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>