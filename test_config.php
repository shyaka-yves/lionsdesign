<?php
// Simple test to debug email config loading
echo "Testing email config loading...<br>";

$config_file = __DIR__ . '/config/email.php';
echo "Config file path: $config_file<br>";
echo "File exists: " . (file_exists($config_file) ? 'Yes' : 'No') . "<br>";

if (file_exists($config_file)) {
    // Capture any output
    ob_start();
    
    $config = require_once $config_file;
    
    $output = ob_get_clean();
    if ($output) {
        echo "Output captured: " . htmlspecialchars($output) . "<br>";
    }
    
    echo "Config type: " . gettype($config) . "<br>";
    if (is_array($config)) {
        echo "Config keys: " . implode(', ', array_keys($config)) . "<br>";
        echo "SMTP Host: " . $config['smtp_host'] . "<br>";
        echo "SMTP Port: " . $config['smtp_port'] . "<br>";
        echo "Username: " . $config['smtp_username'] . "<br>";
    } else {
        echo "Config value: " . var_export($config, true) . "<br>";
    }
} else {
    echo "Config file not found!<br>";
}
?>
