<?php
// Enhanced email functions for hosting environments

function sendOTPEmailHosting($email, $otp, $type) {
    try {
        // Prefer env-driven SMTP first; fall back to PHP mail() if misconfigured
        $configPath = __DIR__ . '/../config/email_hosting.php';
        $email_config = file_exists($configPath) ? require $configPath : null;
        $hasCreds = is_array($email_config)
            && !empty($email_config['smtp_host'])
            && !empty($email_config['smtp_username'])
            && !empty($email_config['smtp_password']);

        if ($hasCreds) {
            $ok = sendWithConfig($email, $otp, $type, $email_config);
            if ($ok) {
                return true;
            }
        }
        // Fallback: PHP mail()
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
        
        // Sender identity
        $fromAddress = isset($email_config['from_address']) && $email_config['from_address'] !== ''
            ? $email_config['from_address']
            : $email_config['smtp_username'];
        $fromName = isset($email_config['from_name']) && $email_config['from_name'] !== ''
            ? $email_config['from_name']
            : 'Lions Design';

        // Recipients
        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($email);
        $mail->addReplyTo($fromAddress, $fromName);
        if (!empty($email_config['bounce_address'])) {
            $mail->Sender = $email_config['bounce_address'];
        }

        // Deliverability headers
        $listUnsub = [];
        $listUnsub[] = '<mailto:' . $fromAddress . '>';
        $mail->addCustomHeader('List-Unsubscribe', implode(', ', $listUnsub));

        // DKIM (optional)
        if (!empty($email_config['dkim']) && !empty($email_config['dkim']['enabled'])) {
            $dk = $email_config['dkim'];
            if (!empty($dk['private_key_path']) && file_exists($dk['private_key_path'])) {
                $mail->DKIM_domain = $dk['domain'] ?? '';
                $mail->DKIM_selector = $dk['selector'] ?? '';
                $mail->DKIM_private = $dk['private_key_path'];
                $mail->DKIM_passphrase = $dk['passphrase'] ?? '';
                $mail->DKIM_identity = $fromAddress;
            }
        }
        
        // Content
        $subject = $type === 'signup' ? 'Email Verification - Lions Design' : 'Password Reset - Lions Design';
        $message = $type === 'signup' 
            ? "Your verification code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.<br><br>If you didn't request this, please ignore this email."
            : "Your password reset code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.<br><br>If you didn't request this, please ignore this email.";
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $message)));
        
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
        // Remove hardcoded alternative creds. Use env-driven config variations only.
        $configPath = __DIR__ . '/../config/email_hosting.php';
        $email_config = file_exists($configPath) ? require $configPath : null;
        if (!is_array($email_config)) {
            return false;
        }
        // Try implicit TLS on 465 if primary failed
        $alt = $email_config;
        $alt['smtp_secure'] = 'ssl';
        $alt['smtp_port'] = 465;
        return sendWithConfig($email, $otp, $type, $alt);
        
    } catch (Exception $e) {
        error_log("Alternative email sending failed: " . $e->getMessage());
        return false;
    }
}

// Brevo provider removed per project policy (professional host email only)

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