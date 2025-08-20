<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$error = '';
$show_otp_form = false;

// Clean up expired OTPs
cleanupExpiredOTPs($conn);

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_otp'])) {
        $email = trim($_POST['email']);
        
        if (empty($email)) {
            $error = "Please enter your email address.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            // Check if email exists in database
            $stmt = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Generate and send OTP
                $otp = generateOTP();
                if (storeOTP($conn, $email, $otp, 'password_reset')) {
                    if (sendOTPEmail($email, $otp, 'password_reset')) {
                        $message = "Password reset code has been sent to your email address.";
                        $_SESSION['reset_email'] = $email;
                        $show_otp_form = true;
                    } else {
                        $error = "Failed to send reset code. Please try again.";
                    }
                } else {
                    $error = "Error generating reset code. Please try again.";
                }
            } else {
                $error = "No account found with that email address.";
            }
        }
    } elseif (isset($_POST['verify_otp'])) {
        $email = $_SESSION['reset_email'];
        $otp = sanitizeInput($_POST['otp']);
        
        if (empty($otp)) {
            $error = "Please enter the reset code.";
            $show_otp_form = true;
        } elseif (validateOTP($conn, $email, $otp, 'password_reset')) {
            // OTP is valid, redirect to reset password page
            header("Location: reset_password.php?email=" . urlencode($email));
            exit();
        } else {
            $error = "Invalid or expired reset code. Please try again.";
            $show_otp_form = true;
        }
    } elseif (isset($_POST['resend_otp'])) {
        $email = $_SESSION['reset_email'];
        $otp = generateOTP();
        if (storeOTP($conn, $email, $otp, 'password_reset') && sendOTPEmail($email, $otp, 'password_reset')) {
            $message = "New reset code has been sent to your email address.";
            $show_otp_form = true;
        } else {
            $error = "Failed to send reset code. Please try again.";
            $show_otp_form = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .forgot-password-container {
            min-height: 100vh;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .forgot-password-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .forgot-password-header {
            background: #28a745;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .forgot-password-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn-primary {
            background: #28a745;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #218838;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <div class="forgot-password-header">
                <i class="fas fa-lock fa-3x mb-3"></i>
                <h2 class="mb-2">Forgot Password?</h2>
                <p class="mb-0">Enter your email address to reset your password</p>
            </div>
            
            <div class="forgot-password-body">
                <!-- Alert Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!$show_otp_form): ?>
                <!-- Email Form -->
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
                        </div>
                        <small class="text-muted">We'll send you a verification code to reset your password</small>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" name="send_otp" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Reset Code
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <!-- OTP Verification Form -->
                <form method="POST" action="">
                    <div class="text-center mb-4">
                        <i class="fas fa-envelope-open-text fa-3x text-success mb-3"></i>
                        <h5>Password Reset Verification</h5>
                        <p class="text-muted">We've sent a reset code to <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong></p>
                    </div>

                    <div class="mb-4">
                        <label for="otp" class="form-label">Reset Code *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="text" class="form-control text-center" id="otp" name="otp" maxlength="6" placeholder="Enter 6-digit code" required>
                        </div>
                        <div class="form-text">Enter the 6-digit code sent to your email</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="verify_otp" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>Verify & Reset Password
                        </button>
                        <button type="submit" name="resend_otp" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Resend Code
                        </button>
                    </div>
                </form>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-2">Remember your password?</p>
                    <a href="login.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        // OTP input formatting
        document.getElementById('otp')?.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
        });
    </script>
</body>
</html> 