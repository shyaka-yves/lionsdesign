<?php
// Email Configuration
require_once __DIR__ . '/env.php';
// For Gmail SMTP, you need to:
// 1. Enable 2-factor authentication on your Gmail account
// 2. Generate an App Password: https://myaccount.google.com/apppasswords
// 3. Use the App Password instead of your regular password

// Check for any errors before returning
if (error_get_last()) {
    error_log("Email config errors: " . print_r(error_get_last(), true));
}

$config = [
    // Professional mailbox (primary)
    'smtp_host' => 'mail.lionsdesignltd.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => 'admin@lionsdesignltd.com',
    'smtp_password' => 'VlNRg[jC*ED-',
    
    // Hosting environment specific settings for lionsdesignltd.com
    'timeout' => 60, // Increased timeout for hosting
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true,
    'disable_verify_peer' => true,
    'debug_level' => 2, // temporarily enable SMTP debug to capture server response
    
    // Alternative configurations for fallback (Brevo first)
    'backup_configs' => [
        // Brevo (primary fallback) via .env
        [
            'smtp_host' => 'smtp-relay.brevo.com',
            'smtp_port' => 587,
            'smtp_secure' => 'tls',
            'smtp_username' => $_ENV['BREVO_SMTP_USERNAME'] ?? '',
            'smtp_password' => $_ENV['BREVO_SMTP_PASSWORD'] ?? '',
            'debug_level' => 0,
        ],
        // Same professional mailbox via SMTPS 465
        [
            'smtp_host' => 'mail.lionsdesignltd.com',
            'smtp_port' => 465,
            'smtp_secure' => 'ssl',
            'smtp_username' => 'admin@lionsdesignltd.com',
            'smtp_password' => 'VlNRg[jC*ED-',
            'debug_level' => 2,
        ],
        // Gmail STARTTLS on port 587
        [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_secure' => 'tls',
            'smtp_username' => 'shyakayvany@gmail.com',
            'smtp_password' => 'uaur ahxe gqvb iemd',
            'debug_level' => 0,
        ]
    ]
];

// Log the config for debugging
error_log("Email config loaded: " . print_r($config, true));

return $config;
?>
