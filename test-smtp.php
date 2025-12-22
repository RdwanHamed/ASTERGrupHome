<?php
/**
 * SMTP Test Script
 * This will test if your email configuration is working
 */

require_once __DIR__ . '/email-config.php';
require_once __DIR__ . '/smtp-mail.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Configuration Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #004085; background: #cce5ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h2>Email Configuration Test</h2>
    
    <?php
    // Check configuration
    echo "<div class='info'><strong>Current Configuration:</strong><br>";
    echo "Recipient Email: <code>" . RECIPIENT_EMAIL . "</code><br>";
    echo "SMTP Enabled: <code>" . (SMTP_ENABLED ? 'true' : 'false') . "</code><br>";
    echo "SMTP Username: <code>" . SMTP_USERNAME . "</code><br>";
    echo "SMTP Password: <code>" . (!empty(SMTP_PASSWORD) ? '***SET***' : 'NOT SET') . "</code><br>";
    echo "</div>";
    
    if (!SMTP_ENABLED) {
        echo "<div class='warning'><strong>⚠️ SMTP is NOT enabled!</strong><br>";
        echo "Please run <code>setup-email.bat</code> to configure email sending.</div>";
    } elseif (empty(SMTP_PASSWORD)) {
        echo "<div class='warning'><strong>⚠️ SMTP Password is NOT set!</strong><br>";
        echo "Please run <code>setup-email.bat</code> and enter your Gmail App Password.</div>";
    } else {
        echo "<div class='info'><strong>Testing email sending...</strong></div>";
        
        // Test email
        $test_subject = "Test Email from Aster Group Home";
        $test_message = "This is a test email to verify that email sending is working correctly.\n\n";
        $test_message .= "If you receive this email, your SMTP configuration is working!";
        
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
            echo "Email was sent successfully to <code>" . RECIPIENT_EMAIL . "</code><br>";
            echo "Please check your inbox (and spam folder) for the test email.</div>";
        } else {
            echo "<div class='error'><strong>❌ FAILED!</strong><br>";
            echo "Email could not be sent. Possible issues:<br>";
            echo "1. Gmail App Password is incorrect<br>";
            echo "2. 2-Step Verification is not enabled<br>";
            echo "3. Firewall or network blocking SMTP connection<br>";
            echo "4. Check PHP error logs for more details</div>";
            
            // Check error log
            $error_log = ini_get('error_log');
            if ($error_log && file_exists($error_log)) {
                $errors = file_get_contents($error_log);
                if (strpos($errors, 'SMTP') !== false) {
                    echo "<div class='info'><strong>Recent SMTP errors from log:</strong><br>";
                    echo "<pre>" . htmlspecialchars(substr($errors, -500)) . "</pre></div>";
                }
            }
        }
    }
    ?>
    
    <hr>
    <p><a href="environment.html">← Back to Admission Form</a></p>
    <p><a href="test-email.php">Test PHP mail() function</a></p>
</body>
</html>



