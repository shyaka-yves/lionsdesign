<?php
// Force error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting error test...<br>";

// Test basic PHP
echo "PHP Version: " . phpversion() . "<br>";

// Test file existence
$files_to_check = [
    'config/database.php',
    'includes/functions.php', 
    'vendor/autoload.php',
    'config/email.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test including files
echo "<br>Testing file includes...<br>";

try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        echo "✅ database.php included successfully<br>";
    }
} catch (Exception $e) {
    echo "❌ Error including database.php: " . $e->getMessage() . "<br>";
}

try {
    if (file_exists('includes/functions.php')) {
        require_once 'includes/functions.php';
        echo "✅ functions.php included successfully<br>";
    }
} catch (Exception $e) {
    echo "❌ Error including functions.php: " . $e->getMessage() . "<br>";
}

echo "<br>Test completed!<br>";
?>