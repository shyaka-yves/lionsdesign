<?php
// Email Configuration
// For Gmail SMTP, you need to:
// 1. Enable 2-factor authentication on your Gmail account
// 2. Generate an App Password: https://myaccount.google.com/apppasswords
// 3. Use the App Password instead of your regular password

// Check for any errors before returning
if (error_get_last()) {
    error_log("Email config errors: " . print_r(error_get_last(), true));
}

$config = [
    // Gmail SMTP (optimized for hosting environment)
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'shyakayvany@gmail.com',
    'smtp_password' => 'uaur ahxe gqvb iemd', // Gmail App Password
    
    // Hosting environment specific settings for lionsdesignltd.com
    'timeout' => 60, // Increased timeout for hosting
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true,
    'disable_verify_peer' => true,
    
    // Alternative configurations for fallback
    'backup_configs' => [
        // Try port 465 with SSL if 587 fails
        [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 465,
            'smtp_secure' => 'ssl',
            'smtp_username' => 'shyakayvany@gmail.com',
            'smtp_password' => 'uaur ahxe gqvb iemd',
        ],
        // Brevo configuration as backup
        [
            'smtp_host' => 'smtp-relay.brevo.com',
            'smtp_port' => 587,
            'smtp_username' => 'shyakayvany@gmail.com',
            'smtp_password' => 'YOUR_BREVO_API_KEY', // Replace with actual Brevo API key
        ]
    ]
];

// Log the config for debugging
error_log("Email config loaded: " . print_r($config, true));

return $config;
?>
