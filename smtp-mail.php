<?php
/**
 * Improved SMTP Mail Function for Gmail
 * This function sends emails using Gmail SMTP with proper TLS/SSL support
 */

function sendSMTPEmail($to_email, $subject, $message, $from_email, $from_name, $smtp_username, $smtp_password) {
    // Gmail SMTP settings
    $smtp_host = 'smtp.gmail.com';
    $smtp_port = 587;
    
    // Create SSL context for secure connection
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
    
    // Create socket connection with SSL context
    $smtp = @stream_socket_client(
        "tcp://{$smtp_host}:{$smtp_port}",
        $errno,
        $errstr,
        30,
        STREAM_CLIENT_CONNECT,
        $context
    );
    
    if (!$smtp) {
        $error_msg = "SMTP Connection failed: $errstr ($errno)";
        error_log($error_msg);
        $log_file = __DIR__ . '/smtp-errors.log';
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $error_msg . "\n", FILE_APPEND);
        return false;
    }
    
    // Set timeout
    stream_set_timeout($smtp, 30);
    
    // Read initial server response
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '220') {
        error_log("SMTP Initial response error: $response");
        fclose($smtp);
        return false;
    }
    
    // Send EHLO
    fputs($smtp, "EHLO " . $smtp_host . "\r\n");
    $response = '';
    while ($line = fgets($smtp, 515)) {
        $response .= $line;
        if (substr($line, 3, 1) == ' ') break;
    }
    
    // Start TLS
    fputs($smtp, "STARTTLS\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '220') {
        error_log("SMTP STARTTLS error: $response");
        fclose($smtp);
        return false;
    }
    
    // Enable crypto
    if (!stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        error_log("SMTP TLS encryption failed");
        fclose($smtp);
        return false;
    }
    
    // Send EHLO again after TLS
    fputs($smtp, "EHLO " . $smtp_host . "\r\n");
    $response = '';
    while ($line = fgets($smtp, 515)) {
        $response .= $line;
        if (substr($line, 3, 1) == ' ') break;
    }
    
    // Authenticate
    fputs($smtp, "AUTH LOGIN\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '334') {
        error_log("SMTP AUTH LOGIN error: $response");
        fclose($smtp);
        return false;
    }
    
    fputs($smtp, base64_encode($smtp_username) . "\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '334') {
        error_log("SMTP Username error: $response");
        fclose($smtp);
        return false;
    }
    
    fputs($smtp, base64_encode($smtp_password) . "\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '235') {
        $error_msg = "SMTP Authentication failed. Response: " . trim($response);
        error_log($error_msg);
        // Also log to a local file for easier debugging
        $log_file = __DIR__ . '/smtp-errors.log';
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $error_msg . "\n", FILE_APPEND);
        fclose($smtp);
        return false;
    }
    
    // Set sender
    fputs($smtp, "MAIL FROM: <" . $from_email . ">\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '250') {
        error_log("SMTP MAIL FROM error: $response");
        fclose($smtp);
        return false;
    }
    
    // Set recipient
    fputs($smtp, "RCPT TO: <" . $to_email . ">\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '250') {
        error_log("SMTP RCPT TO error: $response");
        fclose($smtp);
        return false;
    }
    
    // Send data
    fputs($smtp, "DATA\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '354') {
        error_log("SMTP DATA error: $response");
        fclose($smtp);
        return false;
    }
    
    // Email headers and body
    $email_content = "From: " . $from_name . " <" . $from_email . ">\r\n";
    $email_content .= "To: <" . $to_email . ">\r\n";
    $email_content .= "Subject: " . $subject . "\r\n";
    $email_content .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $email_content .= "\r\n";
    $email_content .= $message . "\r\n";
    $email_content .= ".\r\n";
    
    fputs($smtp, $email_content);
    $response = fgets($smtp, 515);
    
    // Quit
    fputs($smtp, "QUIT\r\n");
    fclose($smtp);
    
    // Check if email was sent successfully
    if (substr($response, 0, 3) == '250') {
        return true;
    } else {
        $error_msg = "SMTP Send error: " . trim($response);
        error_log($error_msg);
        $log_file = __DIR__ . '/smtp-errors.log';
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $error_msg . "\n", FILE_APPEND);
        return false;
    }
}
?>



