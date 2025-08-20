<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';
$success = '';
$show_otp_form = false;

// Clean up expired OTPs
cleanupExpiredOTPs($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send_otp'])) {
        // Step 1: Send OTP
        $first_name = sanitizeInput($_POST['first_name']);
        $last_name = sanitizeInput($_POST['last_name']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);
        
        // Validation
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email address already registered.';
            } else {
                // Generate and send OTP
                $otp = generateOTP();
                if (storeOTP($conn, $email, $otp, 'signup')) {
                    // Store user data in session for later use
                    $_SESSION['temp_user_data'] = [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'password' => $password,
                        'phone' => $phone,
                        'address' => $address
                    ];
                    
                    // Send OTP email
                    if (sendOTPEmail($email, $otp, 'signup')) {
                        $success = 'Verification code has been sent to your email address.';
                        $show_otp_form = true;
                    } else {
                        $error = 'Failed to send verification code. Please try again.';
                    }
                } else {
                    $error = 'Error generating verification code. Please try again.';
                }
            }
        }
    } elseif (isset($_POST['verify_otp'])) {
        // Step 2: Verify OTP and create account
        $email = $_SESSION['temp_user_data']['email'];
        $otp = sanitizeInput($_POST['otp']);
        
        if (empty($otp)) {
            $error = 'Please enter the verification code.';
            $show_otp_form = true;
        } elseif (validateOTP($conn, $email, $otp, 'signup')) {
            // OTP is valid, create the account
            $user_data = $_SESSION['temp_user_data'];
            if (registerUser($conn, $user_data['email'], $user_data['password'], $user_data['first_name'], $user_data['last_name'], $user_data['phone'], $user_data['address'])) {
                $success = 'Account created successfully! You can now login.';
                unset($_SESSION['temp_user_data']); // Clear temporary data
            } else {
                $error = 'Error creating account. Please try again.';
                $show_otp_form = true;
            }
        } else {
            $error = 'Invalid or expired verification code. Please try again.';
            $show_otp_form = true;
        }
    } elseif (isset($_POST['resend_otp'])) {
        // Resend OTP
        $email = $_SESSION['temp_user_data']['email'];
        $otp = generateOTP();
        if (storeOTP($conn, $email, $otp, 'signup') && sendOTPEmail($email, $otp, 'signup')) {
            $success = 'New verification code has been sent to your email address.';
            $show_otp_form = true;
        } else {
            $error = 'Failed to send verification code. Please try again.';
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
    <title>Register - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Registration Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$show_otp_form): ?>
                        <!-- Registration Form -->
                        <form method="POST" action="" id="registerForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Minimum 6 characters</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="send_otp" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Verification Code
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                        <!-- OTP Verification Form -->
                        <form method="POST" action="" id="otpForm">
                            <div class="text-center mb-4">
                                <i class="fas fa-envelope-open-text fa-3x text-success mb-3"></i>
                                <h5>Email Verification</h5>
                                <p class="text-muted">We've sent a verification code to <strong><?php echo htmlspecialchars($_SESSION['temp_user_data']['email']); ?></strong></p>
                            </div>

                            <div class="mb-4">
                                <label for="otp" class="form-label">Verification Code *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input type="text" class="form-control text-center" id="otp" name="otp" maxlength="6" placeholder="Enter 6-digit code" required>
                                </div>
                                <div class="form-text">Enter the 6-digit code sent to your email</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="verify_otp" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Verify & Create Account
                                </button>
                                <button type="submit" name="resend_otp" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Resend Code
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">Already have an account?</p>
                            <a href="login.php" class="btn btn-outline-dark mt-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        document.getElementById('toggleConfirmPassword')?.addEventListener('click', function() {
            const password = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // OTP input formatting
        document.getElementById('otp')?.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
        });

        // Form validation
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const agreeTerms = document.getElementById('agreeTerms').checked;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                showAlert('Passwords do not match', 'danger');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                showAlert('Password must be at least 6 characters long', 'danger');
                return false;
            }
            
            if (!agreeTerms) {
                e.preventDefault();
                showAlert('Please agree to the terms and conditions', 'danger');
                return false;
            }
        });

        // Real-time password confirmation check
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html> 