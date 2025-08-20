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
    // Gmail SMTP (recommended - requires App Password)
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'shyakayvany@gmail.com',
    'smtp_password' => 'uaur ahxe gqvb iemd', // Replace with your Gmail App Password
    
    // Alternative: Brevo (formerly Sendinblue) - Free tier allows 300 emails/day
    // 'smtp_host' => 'smtp-relay.brevo.com',
    // 'smtp_port' => 587,
    // 'smtp_username' => 'shyakayvany@gmail.com',
    // 'smtp_password' => 'your-brevo-api-key', // Replace with your actual Brevo API key
    
    // Alternative: Outlook SMTP
    // 'smtp_host' => 'smtp-mail.outlook.com',
    // 'smtp_port' => 587,
    // 'smtp_username' => 'your-outlook-email@outlook.com',
    // 'smtp_password' => 'your-outlook-password',
];

// Log the config for debugging
error_log("Email config loaded: " . print_r($config, true));

return $config;
?>
