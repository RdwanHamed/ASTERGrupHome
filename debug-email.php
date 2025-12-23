<?php
/**
 * Email Debugging Tool
 * This will help identify why emails are not being sent
 */

// Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if config file exists and can be read
$config_file = __DIR__ . '/email-config.php';
if (!file_exists($config_file)) {
    die("ERROR: Config file not found at: $config_file");
}

// Try to include config file
try {
    require_once $config_file;
} catch (Exception $e) {
    die("ERROR loading config file: " . $e->getMessage());
}

// Verify constants are defined
if (!defined('SMTP_ENABLED')) {
    die("ERROR: SMTP_ENABLED constant not defined after loading config file");
}

require_once __DIR__ . '/smtp-mail.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Debugging</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #155724; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb; }
        .error { color: #721c24; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #f5c6cb; }
        .info { color: #004085; background: #cce5ff; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #b3d7ff; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #ffeaa7; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        h2 { color: #333; margin-top: 0; }
        .test-section { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 Email Debugging Tool</h2>
        
        <?php
        // Test 1: Configuration Check
        echo "<div class='test-section'>";
        echo "<h3>1. Configuration Check</h3>";
        echo "<div class='info'>";
        echo "SMTP Enabled: <code>" . (SMTP_ENABLED ? 'true' : 'false') . "</code><br>";
        echo "SMTP Username: <code>" . htmlspecialchars(SMTP_USERNAME) . "</code><br>";
        echo "SMTP Password Length: <code>" . strlen(SMTP_PASSWORD) . " characters</code><br>";
        echo "SMTP Password Set: <code>" . (!empty(SMTP_PASSWORD) ? 'YES' : 'NO') . "</code><br>";
        echo "Recipient Email: <code>" . htmlspecialchars(RECIPIENT_EMAIL) . "</code><br>";
        echo "From Email: <code>" . htmlspecialchars(SMTP_FROM_EMAIL) . "</code><br>";
        echo "</div>";
        echo "</div>";
        
        // Test 2: Network Connection
        echo "<div class='test-section'>";
        echo "<h3>2. Network Connection Test</h3>";
        $smtp_host = 'smtp.gmail.com';
        $smtp_port = 587;
        
        $connection = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 5);
        if ($connection) {
            echo "<div class='success'>✅ Can connect to <code>$smtp_host:$smtp_port</code></div>";
            fclose($connection);
        } else {
            echo "<div class='error'>❌ Cannot connect to <code>$smtp_host:$smtp_port</code><br>";
            echo "Error: $errstr ($errno)<br>";
            echo "Possible causes: Firewall blocking port 587, network issue, or Gmail server down.</div>";
        }
        echo "</div>";
        
        // Test 3: SMTP Function Test
        if (SMTP_ENABLED && !empty(SMTP_PASSWORD)) {
            echo "<div class='test-section'>";
            echo "<h3>3. SMTP Function Test</h3>";
            echo "<div class='info'>Attempting to send test email...</div>";
            
            // Capture any output
            ob_start();
            $test_result = @sendSMTPEmail(
                RECIPIENT_EMAIL,
                "Test Email from Debug Tool - " . date('Y-m-d H:i:s'),
                "This is a test email to verify SMTP is working correctly.\n\nIf you receive this, email sending is working!",
                SMTP_FROM_EMAIL,
                SMTP_FROM_NAME,
                SMTP_USERNAME,
                SMTP_PASSWORD
            );
            $output = ob_get_clean();
            
            if ($test_result) {
                echo "<div class='success'><strong>✅ SUCCESS!</strong><br>";
                echo "Test email was sent successfully!<br>";
                echo "Check your inbox at: <code>" . htmlspecialchars(RECIPIENT_EMAIL) . "</code></div>";
            } else {
                echo "<div class='error'><strong>❌ FAILED!</strong><br>";
                echo "The SMTP function returned false. This means the email was not sent.<br>";
                echo "Check the error log below for details.</div>";
            }
            echo "</div>";
            
            // Test 4: Error Log Check
            echo "<div class='test-section'>";
            echo "<h3>4. Error Log Check</h3>";
            
            // Check local SMTP error log first
            $local_log = __DIR__ . '/smtp-errors.log';
            if (file_exists($local_log)) {
                $log_content = file_get_contents($local_log);
                $lines = explode("\n", $log_content);
                $recent_errors = array_slice(array_reverse($lines), 0, 10);
                $recent_errors = array_filter($recent_errors);
                if (!empty($recent_errors)) {
                    echo "<div class='warning'><strong>Recent SMTP errors from local log:</strong><br>";
                    echo "<pre>" . htmlspecialchars(implode("\n", $recent_errors)) . "</pre></div>";
                } else {
                    echo "<div class='info'>No recent errors in local SMTP log.</div>";
                }
            } else {
                echo "<div class='info'>Local SMTP error log not created yet (will be created on first error).</div>";
            }
            
            // Also check PHP error log
            $error_log_file = ini_get('error_log');
            if ($error_log_file && file_exists($error_log_file)) {
                $log_content = file_get_contents($error_log_file);
                $smtp_errors = [];
                $lines = explode("\n", $log_content);
                foreach (array_reverse($lines) as $line) {
                    if (stripos($line, 'SMTP') !== false || stripos($line, 'mail') !== false) {
                        $smtp_errors[] = $line;
                        if (count($smtp_errors) >= 10) break;
                    }
                }
                if (!empty($smtp_errors)) {
                    echo "<div class='warning'><strong>Recent SMTP-related errors from PHP log:</strong><br>";
                    echo "<pre>" . htmlspecialchars(implode("\n", array_reverse($smtp_errors))) . "</pre></div>";
                }
            }
            echo "</div>";
            
            // Test 5: Password Verification
            echo "<div class='test-section'>";
            echo "<h3>5. Password Verification</h3>";
            echo "<div class='info'>";
            echo "Password length: <code>" . strlen(SMTP_PASSWORD) . "</code> characters<br>";
            echo "Expected: 16 characters (Gmail App Password)<br>";
            if (strlen(SMTP_PASSWORD) != 16) {
                echo "<div class='warning'>⚠️ Password length is not 16 characters. Gmail App Passwords are exactly 16 characters.</div>";
            }
            echo "Password contains special characters: <code>" . (preg_match('/[^a-zA-Z0-9]/', SMTP_PASSWORD) ? 'YES' : 'NO') . "</code><br>";
            echo "</div>";
            echo "</div>";
            
        } else {
            echo "<div class='warning'><strong>⚠️ SMTP is not configured!</strong><br>";
            echo "Please configure SMTP settings first using <a href='configure-email.php'>configure-email.php</a></div>";
        }
        ?>
        
        <hr style="margin: 30px 0;">
        
        <div>
            <h3>Quick Actions:</h3>
            <ul>
                <li><a href="test-smtp.php">Test SMTP Configuration</a></li>
                <li><a href="configure-email.php">Configure Email Settings</a></li>
                <li><a href="environment.html">Admission Form</a></li>
            </ul>
        </div>
        
        <div class="info" style="margin-top: 20px;">
            <strong>💡 Common Issues:</strong><br>
            1. <strong>Wrong App Password:</strong> Make sure you're using a Gmail App Password, not your regular Gmail password<br>
            2. <strong>2-Step Verification:</strong> Must be enabled on your Gmail account<br>
            3. <strong>Firewall:</strong> Port 587 might be blocked by your firewall<br>
            4. <strong>Password Expired:</strong> Generate a new App Password if the old one doesn't work<br>
            5. <strong>Special Characters:</strong> If your password has special characters, make sure they're entered correctly
        </div>
    </div>
</body>
</html>











