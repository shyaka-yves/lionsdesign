<?php
// Alternative Email Configuration for Hosting Environments
// This configuration is optimized for shared hosting environments

$config = [
    // Primary: Gmail SMTP with enhanced settings for hosting
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'shyakayvany@gmail.com',
    'smtp_password' => 'uaur ahxe gqvb iemd',
    
    // Alternative: Try port 465 with SSL if 587 fails
    'smtp_port_ssl' => 465,
    'smtp_secure_ssl' => 'ssl',
    
    // Hosting environment specific settings
    'timeout' => 60,
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true,
    'disable_verify_peer' => true,
    
    // Alternative SMTP providers for backup
    'backup_smtp' => [
        'host' => 'smtp-relay.brevo.com',
        'port' => 587,
        'username' => 'shyakayvany@gmail.com',
        'password' => 'your-brevo-api-key', // Replace with actual Brevo API key
    ],
    
    // Debug settings
    'debug_level' => 0, // 0 = off, 1 = client messages, 2 = client and server messages
    'log_errors' => true,
];

// Log configuration loading
error_log("Hosting email config loaded successfully");

return $config;
?>