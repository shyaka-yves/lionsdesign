<?php
// Brevo (Sendinblue) Email Configuration via .env
// Keys expected in .env (kept out of repo):
//   BREVO_SMTP_USERNAME=verified@yourdomain.com
//   BREVO_SMTP_PASSWORD=your_api_key

require_once __DIR__ . '/env.php';

$config = [
    'smtp_host' => 'smtp-relay.brevo.com',
    'smtp_port' => 587,
    // Secure: read strictly from environment (no fallbacks)
    'smtp_username' => ($_ENV['BREVO_SMTP_USERNAME'] ?? getenv('BREVO_SMTP_USERNAME') ?: ''),
    'smtp_password' => ($_ENV['BREVO_SMTP_PASSWORD'] ?? getenv('BREVO_SMTP_PASSWORD') ?: ''),
    
    // Hosting environment settings optimized for lionsdesignltd.com
    'timeout' => 60,
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true,
    'debug_level' => 0,
];

return $config;
?>