<?php
// Hosting Email Configuration (secure, env-driven)
require_once __DIR__ . '/env.php';

$host = $_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST') ?: '';
$port = (int)($_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT') ?: 0);
$username = $_ENV['MAIL_USERNAME'] ?? getenv('MAIL_USERNAME') ?: '';
$password = $_ENV['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD') ?: '';
$encryption = $_ENV['MAIL_ENCRYPTION'] ?? getenv('MAIL_ENCRYPTION') ?: 'tls';

$config = [
    'smtp_host' => $host,
    'smtp_port' => $port ?: 587,
    'smtp_username' => $username,
    'smtp_password' => $password,
    'smtp_secure' => $encryption,
    'timeout' => (int)($_ENV['MAIL_TIMEOUT'] ?? getenv('MAIL_TIMEOUT') ?: 30),
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true,
    'disable_verify_peer' => true,
    'debug_level' => 0,
];

return $config;
?>