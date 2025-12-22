<?php
/**
 * Web-based Email Configuration Page
 * Use this page to configure email settings through your browser
 */

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['configure_email'])) {
    $smtp_password = trim($_POST['smtp_password'] ?? '');
    $smtp_enabled = isset($_POST['smtp_enabled']) ? true : false;
    
    // Remove spaces from password
    $smtp_password = preg_replace('/\s+/', '', $smtp_password);
    
    if ($smtp_enabled && empty($smtp_password)) {
        $message = "Error: Please enter your Gmail App Password when enabling SMTP.";
        $message_type = 'error';
    } else {
        // Read current config file
        $config_file = __DIR__ . '/email-config.php';
        $config_content = file_get_contents($config_file);
        
        // Update SMTP_PASSWORD
        if (!empty($smtp_password)) {
            $config_content = preg_replace(
                "/define\('SMTP_PASSWORD',\s*'[^']*'\);/",
                "define('SMTP_PASSWORD', '" . addslashes($smtp_password) . "');",
                $config_content
            );
        }
        
        // Update SMTP_ENABLED
        $enabled_value = $smtp_enabled ? 'true' : 'false';
        $config_content = preg_replace(
            "/define\('SMTP_ENABLED',\s*(true|false)\);/",
            "define('SMTP_ENABLED', $enabled_value);",
            $config_content
        );
        
        // Write updated config
        if (file_put_contents($config_file, $config_content)) {
            $message = "Configuration updated successfully! ";
            if ($smtp_enabled) {
                $message .= "SMTP is now enabled. You can test it using the test-smtp.php page.";
            } else {
                $message .= "SMTP is now disabled.";
            }
            $message_type = 'success';
        } else {
            $message = "Error: Could not write to configuration file. Please check file permissions.";
            $message_type = 'error';
        }
    }
}

// Read current configuration
require_once __DIR__ . '/email-config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Configure Email Settings</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-top: 0; }
        .success { 
            color: #155724; 
            background: #d4edda; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border: 1px solid #c3e6cb;
        }
        .error { 
            color: #721c24; 
            background: #f8d7da; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border: 1px solid #f5c6cb;
        }
        .info { 
            color: #004085; 
            background: #cce5ff; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border: 1px solid #b3d7ff;
        }
        .warning { 
            color: #856404; 
            background: #fff3cd; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border: 1px solid #ffeaa7;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        button {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #0056b3;
        }
        code { 
            background: #f4f4f4; 
            padding: 2px 6px; 
            border-radius: 3px; 
            font-family: 'Courier New', monospace;
        }
        .current-config {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .current-config strong {
            color: #333;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .help-link {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Configure Email Settings</h1>
        
        <?php if ($message): ?>
            <div class="<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="current-config">
            <strong>Current Configuration:</strong><br>
            Recipient Email: <code><?php echo htmlspecialchars(RECIPIENT_EMAIL); ?></code><br>
            SMTP Enabled: <code><?php echo SMTP_ENABLED ? 'true' : 'false'; ?></code><br>
            SMTP Username: <code><?php echo htmlspecialchars(SMTP_USERNAME); ?></code><br>
            SMTP Password: <code><?php echo !empty(SMTP_PASSWORD) ? '***SET***' : 'NOT SET'; ?></code>
        </div>
        
        <div class="info">
            <strong>📋 How to Get Gmail App Password:</strong><br>
            1. Go to <a href="https://myaccount.google.com/apppasswords" target="_blank">Google App Passwords</a><br>
            2. Make sure 2-Step Verification is enabled on your Gmail account<br>
            3. Select "Mail" as the app and generate a password<br>
            4. Copy the 16-character password (it may have spaces - that's OK)<br>
            5. Paste it in the form below
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" name="smtp_enabled" id="smtp_enabled" value="1" <?php echo SMTP_ENABLED ? 'checked' : ''; ?>>
                    <label for="smtp_enabled">Enable SMTP Email Sending</label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="smtp_password">Gmail App Password (16 characters):</label>
                <input 
                    type="password" 
                    id="smtp_password" 
                    name="smtp_password" 
                    placeholder="Enter your Gmail App Password here"
                    value=""
                    autocomplete="off"
                >
                <small style="color: #666; display: block; margin-top: 5px;">
                    This password will be saved securely in the configuration file.
                </small>
            </div>
            
            <button type="submit" name="configure_email">Save Configuration</button>
        </form>
        
        <hr style="margin: 30px 0;">
        
        <div style="margin-top: 20px;">
            <h3>Quick Links:</h3>
            <ul>
                <li><a href="test-smtp.php">Test SMTP Configuration</a></li>
                <li><a href="environment.html">Admission Form</a></li>
                <li><a href="index.html">Home Page</a></li>
            </ul>
        </div>
        
        <?php if (SMTP_ENABLED && !empty(SMTP_PASSWORD)): ?>
            <div class="success" style="margin-top: 20px;">
                <strong>✅ Email is configured!</strong><br>
                Form submissions will be sent to: <code><?php echo htmlspecialchars(RECIPIENT_EMAIL); ?></code><br>
                <a href="test-smtp.php" class="help-link">Test email sending →</a>
            </div>
        <?php else: ?>
            <div class="warning" style="margin-top: 20px;">
                <strong>⚠️ Email is not configured</strong><br>
                Form submissions are being saved to files, but emails are not being sent.<br>
                Configure SMTP above to enable email sending.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>




