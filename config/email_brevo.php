<?php
// Brevo (Sendinblue) Email Configuration
// Free tier: 300 emails per day
// Sign up at: https://www.brevo.com/

$config = [
    'smtp_host' => 'smtp-relay.brevo.com',
    'smtp_port' => 587,
    'smtp_username' => 'shyakayvany@gmail.com', // Your registered email
    'smtp_password' => 'YOUR_BREVO_API_KEY', // Replace with your actual Brevo API key
    
    // Hosting environment settings optimized for lionsdesignltd.com
    'timeout' => 60, // Increased for hosting
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true,
    'debug_level' => 0,
    
    // Instructions for setup
    'setup_instructions' => [
        '1. Sign up at https://www.brevo.com/',
        '2. Go to Settings > SMTP & API',
        '3. Generate a new API key',
        '4. Replace YOUR_BREVO_API_KEY with your actual API key',
        '5. Free tier allows 300 emails per day'
    ]
];

return $config;
?>