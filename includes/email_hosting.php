<?php
// Enhanced email functions for hosting environments

function sendOTPEmailHosting($email, $otp, $type) {
    try {
        // Skip SMTP attempts and go directly to PHP mail() for speed
        // SMTP is failing with SSL errors, so use the working fallback immediately
        error_log("Using PHP mail() directly for speed");
        return sendWithPhpMail($email, $otp, $type);
        
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
        
        // Handle different encryption methods with sane defaults per port
        if (isset($email_config['smtp_secure'])) {
            $mail->SMTPSecure = $email_config['smtp_secure'];
        } else {
            // Default: STARTTLS on 587, SMTPS on 465
            if (!empty($email_config['smtp_port']) && (int)$email_config['smtp_port'] === 465) {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
        }

        $mail->Port = $email_config['smtp_port'];
        
        // Timeout and connection settings (reduced for faster fallback)
        $mail->Timeout = isset($email_config['timeout']) ? $email_config['timeout'] : 5;
        $mail->SMTPKeepAlive = true;
        $mail->SMTPDebug = isset($email_config['debug_level']) ? $email_config['debug_level'] : 0;
        
        // TLS/SSL options — prefer TLSv1.2+ to avoid WRONG_VERSION_NUMBER
        $preferredCrypto = defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')
            ? STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
            : STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;

        $mail->SMTPAutoTLS = true; // allow opportunistic TLS on 587

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => isset($email_config['verify_peer']) ? $email_config['verify_peer'] : false,
                'verify_peer_name' => isset($email_config['verify_peer_name']) ? $email_config['verify_peer_name'] : false,
                'allow_self_signed' => isset($email_config['allow_self_signed']) ? $email_config['allow_self_signed'] : true,
                'crypto_method' => $preferredCrypto,
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
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // SMTPS on 465
        $mail->Port = 465; // Try port 465 instead of 587
        
        $mail->SMTPAutoTLS = false; // not needed on implicit TLS
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'crypto_method' => (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')
                    ? STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
                    : STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT),
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
        
        $mail->SMTPAutoTLS = true;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'crypto_method' => (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')
                    ? STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
                    : STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT),
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

function sendWithPhpMail($email, $otp, $type) {
    // Simple HTML email via PHP mail(), relies on hosting MTA
    $subject = $type === 'signup' ? 'Email Verification - Lions Design' : 'Password Reset - Lions Design';
    $message = $type === 'signup'
        ? "<html><body style=\"font-family:Arial, sans-serif\">Your verification code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.<br><br>If you didn't request this, please ignore this email.</body></html>"
        : "<html><body style=\"font-family:Arial, sans-serif\">Your password reset code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.<br><br>If you didn't request this, please ignore this email.</body></html>";

    $from = 'no-reply@' . (isset($_SERVER['HTTP_HOST']) ? preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']) : 'lionsdesignltd.com');
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: Lions Design <' . $from . '>';
    $headers[] = 'Reply-To: ' . $from;
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    $result = @mail($email, $subject, $message, implode("\r\n", $headers));
    if (!$result) {
        error_log('PHP mail() fallback failed');
    }
    return (bool)$result;
}
?>