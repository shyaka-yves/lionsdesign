<?php
// Simple autoloader for PHPMailer
spl_autoload_register(function ($class) {
    // PHPMailer namespace
    if (strpos($class, 'PHPMailer\\PHPMailer\\') === 0) {
        $class = str_replace('PHPMailer\\PHPMailer\\', '', $class);
        $file = __DIR__ . '/PHPMailer/PHPMailer/src/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
?>
