<?php
/**
 * Test Configuration File Loading
 * This will verify that email-config.php is being loaded correctly
 */

echo "<h2>Configuration File Test</h2>";
echo "<pre>";

// Check if file exists
$config_file = __DIR__ . '/email-config.php';
echo "Config file path: $config_file\n";
echo "File exists: " . (file_exists($config_file) ? 'YES' : 'NO') . "\n";
echo "File readable: " . (is_readable($config_file) ? 'YES' : 'NO') . "\n";
echo "File size: " . filesize($config_file) . " bytes\n\n";

// Read file contents
echo "File contents:\n";
echo "================================\n";
$file_content = file_get_contents($config_file);
echo htmlspecialchars($file_content);
echo "\n================================\n\n";

// Try to include the file
echo "Attempting to include config file...\n";
try {
    require_once $config_file;
    echo "✓ Config file included successfully\n\n";
} catch (Exception $e) {
    echo "✗ Error including config file: " . $e->getMessage() . "\n\n";
}

// Check if constants are defined
echo "Checking constants:\n";
echo "RECIPIENT_EMAIL: " . (defined('RECIPIENT_EMAIL') ? RECIPIENT_EMAIL : 'NOT DEFINED') . "\n";
echo "SMTP_ENABLED: " . (defined('SMTP_ENABLED') ? (SMTP_ENABLED ? 'true' : 'false') : 'NOT DEFINED') . "\n";
echo "SMTP_USERNAME: " . (defined('SMTP_USERNAME') ? SMTP_USERNAME : 'NOT DEFINED') . "\n";
echo "SMTP_PASSWORD: " . (defined('SMTP_PASSWORD') ? (strlen(SMTP_PASSWORD) . ' chars - ' . (empty(SMTP_PASSWORD) ? 'EMPTY' : 'SET')) : 'NOT DEFINED') . "\n";
echo "SMTP_FROM_EMAIL: " . (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'NOT DEFINED') . "\n";
echo "SMTP_FROM_NAME: " . (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'NOT DEFINED') . "\n";

// Check for PHP errors
echo "\nPHP Errors:\n";
$errors = error_get_last();
if ($errors) {
    print_r($errors);
} else {
    echo "No PHP errors detected\n";
}

echo "</pre>";
?>





