<?php
// Simple email test with error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";

// Test if we can include files
echo "Testing file includes...<br>";

try {
    if (file_exists('config/database.php')) {
        echo "✅ config/database.php exists<br>";
    } else {
        echo "❌ config/database.php not found<br>";
    }
    
    if (file_exists('includes/functions.php')) {
        echo "✅ includes/functions.php exists<br>";
    } else {
        echo "❌ includes/functions.php not found<br>";
    }
    
    if (file_exists('vendor/autoload.php')) {
        echo "✅ vendor/autoload.php exists<br>";
    } else {
        echo "❌ vendor/autoload.php not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br>Basic test completed!<br>";
?>