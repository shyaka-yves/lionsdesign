<?php
// Email Configuration (secure)
// Loads strictly from environment via config/env.php. No hardcoded credentials.
require_once __DIR__ . '/env.php';

// Required keys (documented in .env.example):
// MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION (tls|ssl|none), MAIL_TIMEOUT

// Read from environment only
$host = $_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST') ?: '';
$port = (int)($_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT') ?: 0);
$username = $_ENV['MAIL_USERNAME'] ?? getenv('MAIL_USERNAME') ?: '';
$password = $_ENV['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD') ?: '';
$encryption = $_ENV['MAIL_ENCRYPTION'] ?? getenv('MAIL_ENCRYPTION') ?: 'tls';
$timeout = (int)($_ENV['MAIL_TIMEOUT'] ?? getenv('MAIL_TIMEOUT') ?: 30);

// Optional sender identity
$fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? getenv('MAIL_FROM_ADDRESS') ?: $username;
$fromName = $_ENV['MAIL_FROM_NAME'] ?? getenv('MAIL_FROM_NAME') ?: 'Lions Design';

// Optional bounce/return-path address
$bounceAddress = $_ENV['MAIL_BOUNCE_ADDRESS'] ?? getenv('MAIL_BOUNCE_ADDRESS') ?: '';

// Optional DKIM configuration
$dkimEnabled = strtolower($_ENV['MAIL_DKIM_ENABLED'] ?? getenv('MAIL_DKIM_ENABLED') ?: 'false') === 'true';
$dkimDomain = $_ENV['MAIL_DKIM_DOMAIN'] ?? getenv('MAIL_DKIM_DOMAIN') ?: '';
$dkimSelector = $_ENV['MAIL_DKIM_SELECTOR'] ?? getenv('MAIL_DKIM_SELECTOR') ?: '';
$dkimPrivateKeyPath = $_ENV['MAIL_DKIM_PRIVATE_KEY_PATH'] ?? getenv('MAIL_DKIM_PRIVATE_KEY_PATH') ?: '';
$dkimPassphrase = $_ENV['MAIL_DKIM_PASSPHRASE'] ?? getenv('MAIL_DKIM_PASSPHRASE') ?: '';

// Build config
$config = [
    'smtp_host' => $host,
    'smtp_port' => $port ?: 587,
    'smtp_secure' => $encryption,
    'smtp_username' => $username,
    'smtp_password' => $password,
    'timeout' => $timeout,
    'from_address' => $fromAddress,
    'from_name' => $fromName,
    'bounce_address' => $bounceAddress,
    'dkim' => [
        'enabled' => $dkimEnabled,
        'domain' => $dkimDomain,
        'selector' => $dkimSelector,
        'private_key_path' => $dkimPrivateKeyPath,
        'passphrase' => $dkimPassphrase,
    ],
    // Conservative SSL options defaults; do not verify self-signed to improve compat on shared hosts
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true,
    'debug_level' => 0,
];

// Do not log full config to avoid leaking secrets
return $config;
?>
