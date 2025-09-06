<?php
// Enhanced email functions for hosting environments

function sendOTPEmailHosting($email, $otp, $type) {
    try {
        // Load primary configuration
        $primary_config = require __DIR__ . '/../config/email.php';
        
        // Try primary configuration first
        error_log("Trying primary email config for lionsdesignltd.com");
        if (sendWithConfig($email, $otp, $type, $primary_config)) {
            error_log("Email sent successfully with primary config");
            return true;
        }
        
        // Try backup configurations if primary fails
        if (isset($primary_config['backup_configs'])) {
            foreach ($primary_config['backup_configs'] as $index => $backup_config) {
                error_log("Trying backup config #" . ($index + 1));
                if (sendWithConfig($email, $otp, $type, $backup_config)) {
                    error_log("Email sent successfully with backup config #" . ($index + 1));
                    return true;
                }
            }
        }
        
        // Try alternative SMTP settings
        error_log("Trying alternative SMTP settings");
        if (sendWithAlternativeSettings($email, $otp, $type)) {
            return true;
        }
        
        // Try Brevo as last resort
        error_log("Trying Brevo as last resort");
        return sendWithBrevo($email, $otp, $type);
        
    } catch (Exception $e) {
        error_log("Email sending exception on lionsdesignltd.com: " . $e->getMessage());
        return false;
    }
}

function sendWithConfig($email, $otp, $type, $email_config) {
    try {
        // Include PHPMailer
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $email_config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $email_config['smtp_username'];
        $mail->Password = $email_config['smtp_password'];
        
        // Handle different encryption methods
        if (isset($email_config['smtp_secure'])) {
            $mail->SMTPSecure = $email_config['smtp_secure'];
        } else {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        }
        
        $mail->Port = $email_config['smtp_port'];
        
        // Timeout and connection settings
        $mail->Timeout = isset($email_config['timeout']) ? $email_config['timeout'] : 30;
        $mail->SMTPKeepAlive = true;
        $mail->SMTPDebug = isset($email_config['debug_level']) ? $email_config['debug_level'] : 0;
        
        // SSL options
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => isset($email_config['verify_peer']) ? $email_config['verify_peer'] : false,
                'verify_peer_name' => isset($email_config['verify_peer_name']) ? $email_config['verify_peer_name'] : false,
                'allow_self_signed' => isset($email_config['allow_self_signed']) ? $email_config['allow_self_signed'] : true,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
            ],
        ];
        
        // Recipients
        $mail->setFrom($email_config['smtp_username'], 'Lions Design');
        $mail->addAddress($email);
        
        // Content
        $subject = $type === 'signup' ? 'Email Verification - Lions Design' : 'Password Reset - Lions Design';
        $message = $type === 'signup' 
            ? "Your verification code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.<br><br>If you didn't request this, please ignore this email."
            : "Your password reset code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.<br><br>If you didn't request this, please ignore this email.";
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        // Send email
        if ($mail->send()) {
            error_log("Email sent successfully to: $email with OTP: $otp");
            return true;
        } else {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Email sending exception: " . $e->getMessage());
        return false;
    }
}

function sendWithAlternativeSettings($email, $otp, $type) {
    try {
        // Try with different port and encryption
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'shyakayvany@gmail.com';
        $mail->Password = 'uaur ahxe gqvb iemd';
        $mail->SMTPSecure = 'ssl'; // Try SSL instead of STARTTLS
        $mail->Port = 465; // Try port 465 instead of 587
        
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        
        $mail->setFrom('shyakayvany@gmail.com', 'Lions Design');
        $mail->addAddress($email);
        
        $subject = $type === 'signup' ? 'Email Verification - Lions Design' : 'Password Reset - Lions Design';
        $message = $type === 'signup' 
            ? "Your verification code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes."
            : "Your password reset code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.";
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Alternative email sending failed: " . $e->getMessage());
        return false;
    }
}

function sendWithBrevo($email, $otp, $type) {
    try {
        // Try Brevo configuration
        $brevo_config = require __DIR__ . '/../config/email_brevo.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = $brevo_config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $brevo_config['smtp_username'];
        $mail->Password = $brevo_config['smtp_password'];
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $brevo_config['smtp_port'];
        
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        
        $mail->setFrom($brevo_config['smtp_username'], 'Lions Design');
        $mail->addAddress($email);
        
        $subject = $type === 'signup' ? 'Email Verification - Lions Design' : 'Password Reset - Lions Design';
        $message = $type === 'signup' 
            ? "Your verification code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes."
            : "Your password reset code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.";
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Brevo email sending failed: " . $e->getMessage());
        return false;
    }
}
?>