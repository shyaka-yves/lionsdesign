<?php
// Brevo (Sendinblue) Email Configuration
// Free tier: 300 emails per day
// Sign up at: https://www.brevo.com/

$config = [
    'smtp_host' => 'smtp-relay.brevo.com',
    'smtp_port' => 587,
    'smtp_username' => 'shyakayvany@gmail.com', // Your registered email
    'smtp_password' => 'YOUR_BREVO_API_KEY', // Replace with your actual Brevo API key
    
    // Hosting environment settings
    'timeout' => 30,
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true,
    'debug_level' => 0,
];

return $config;
?>