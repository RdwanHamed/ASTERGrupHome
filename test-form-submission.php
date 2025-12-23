<?php
/**
 * Test Form Submission
 * This page helps diagnose email sending issues
 */

require_once __DIR__ . '/email-config.php';
require_once __DIR__ . '/smtp-mail.php';

// Test email data
$test_email = "test@example.com";
$test_subject = "Test Admission Form Submission";
$test_message = "This is a test message to verify email sending is working.";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #004085; background: #cce5ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        h2 { color: #333; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h2>Email Configuration Test</h2>
    
    <?php
    echo "<div class='info'><strong>Current Configuration:</strong><br>";
    echo "SMTP Enabled: <code>" . (SMTP_ENABLED ? 'true' : 'false') . "</code><br>";
    echo "SMTP Username: <code>" . htmlspecialchars(SMTP_USERNAME) . "</code><br>";
    echo "SMTP Password: <code>" . (!empty(SMTP_PASSWORD) ? 'SET (' . strlen(SMTP_PASSWORD) . ' chars)' : 'NOT SET') . "</code><br>";
    echo "Recipient: <code>" . htmlspecialchars(RECIPIENT_EMAIL) . "</code><br>";
    echo "</div>";
    
    // Test email validation
    echo "<div class='info'><strong>Testing Email Validation:</strong><br>";
    $test_emails = [
        "test@example.com",
        "user.name@example.com",
        "user+tag@example.co.uk",
        "invalid-email",
        "test@",
        "@example.com"
    ];
    
    foreach ($test_emails as $test) {
        $is_valid = filter_var($test, FILTER_VALIDATE_EMAIL);
        $sanitized = filter_var($test, FILTER_SANITIZE_EMAIL);
        echo "Email: <code>$test</code> → ";
        if ($is_valid) {
            echo "<span style='color: green;'>Valid</span>";
        } else {
            echo "<span style='color: red;'>Invalid</span> (sanitized: <code>$sanitized</code>)";
        }
        echo "<br>";
    }
    echo "</div>";
    
    if (SMTP_ENABLED && !empty(SMTP_PASSWORD)) {
        echo "<div class='info'><strong>Testing SMTP Connection...</strong></div>";
        
        // Test SMTP sending
        $result = sendSMTPEmail(
            RECIPIENT_EMAIL,
            $test_subject,
            $test_message,
            SMTP_FROM_EMAIL,
            SMTP_FROM_NAME,
            SMTP_USERNAME,
            SMTP_PASSWORD
        );
        
        if ($result) {
            echo "<div class='success'><strong>✅ SUCCESS!</strong><br>";
            echo "Test email was sent successfully to <code>" . htmlspecialchars(RECIPIENT_EMAIL) . "</code><br>";
            echo "Please check your inbox (and spam folder) for the test email.</div>";
        } else {
            echo "<div class='error'><strong>❌ FAILED!</strong><br>";
            echo "Email could not be sent. Possible issues:<br>";
            echo "1. Gmail App Password is incorrect<br>";
            echo "2. 2-Step Verification is not enabled<br>";
            echo "3. Firewall or network blocking SMTP connection (port 587)<br>";
            echo "4. Check PHP error logs for detailed error messages</div>";
            
            // Show recent errors
            $error_log_file = ini_get('error_log');
            if ($error_log_file && file_exists($error_log_file)) {
                $log_content = file_get_contents($error_log_file);
                $smtp_errors = [];
                $lines = explode("\n", $log_content);
                foreach (array_reverse($lines) as $line) {
                    if (stripos($line, 'SMTP') !== false) {
                        $smtp_errors[] = $line;
                        if (count($smtp_errors) >= 5) break;
                    }
                }
                if (!empty($smtp_errors)) {
                    echo "<div class='warning'><strong>Recent SMTP errors from log:</strong><br>";
                    echo "<pre>" . htmlspecialchars(implode("\n", array_reverse($smtp_errors))) . "</pre></div>";
                }
            }
        }
    } else {
        echo "<div class='warning'><strong>⚠️ SMTP is not configured!</strong><br>";
        echo "Please configure SMTP settings first using <a href='configure-email.php'>configure-email.php</a></div>";
    }
    ?>
    
    <hr>
    <p><a href="environment.html">← Back to Admission Form</a></p>
    <p><a href="configure-email.php">Configure Email Settings</a></p>
    <p><a href="test-smtp.php">Test SMTP Configuration</a></p>
</body>
</html>











