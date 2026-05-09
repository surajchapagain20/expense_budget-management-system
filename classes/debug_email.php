<?php
// Temporary debug script - DELETE after fixing
ini_set('display_errors', 1);
error_reporting(E_ALL);

$base_dir = dirname(__DIR__);
echo "Base dir: $base_dir\n";
echo "Config exists: " . (file_exists($base_dir . '/config.php') ? 'YES' : 'NO') . "\n";
echo "EmailHelper exists: " . (file_exists($base_dir . '/classes/EmailHelper.php') ? 'YES' : 'NO') . "\n";
echo "PHPMailer exists: " . (file_exists($base_dir . '/classes/PHPMailer/PHPMailer.php') ? 'YES' : 'NO') . "\n";
echo "SMTP exists: " . (file_exists($base_dir . '/classes/PHPMailer/SMTP.php') ? 'YES' : 'NO') . "\n";
echo "Exception exists: " . (file_exists($base_dir . '/classes/PHPMailer/Exception.php') ? 'YES' : 'NO') . "\n\n";

try {
    require_once $base_dir . '/config.php';
    echo "Config loaded OK\n";
    
    require_once $base_dir . '/classes/EmailHelper.php';
    echo "EmailHelper loaded OK\n";
    
    echo "SMTP_HOST: " . SMTP_HOST . "\n";
    echo "SMTP_PORT: " . SMTP_PORT . "\n";
    echo "SMTP_USERNAME: " . SMTP_USERNAME . "\n";
    
    // Check DB for email column
    global $conn;
    $col = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
    echo "Email column exists: " . ($col->num_rows > 0 ? 'YES' : 'NO - Run migration!') . "\n";
    $col2 = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
    echo "Reset_token column exists: " . ($col2->num_rows > 0 ? 'YES' : 'NO - Run migration!') . "\n";
    
} catch(Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>
