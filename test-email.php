<?php
/**
 * Test Email Script
 * Use this to test if email is working on your XAMPP server
 */

// Set your email here
$to_email = "wiruqoleqec072@gmail.com";
$subject = "Test Email from Aster Group Home";
$message = "This is a test email to verify that email sending is working on your XAMPP server.\n\n";
$message .= "If you receive this email, your PHP mail() function is configured correctly.";

$headers = "From: Aster Group Home <noreply@astergrouphome.com>\r\n";
$headers .= "Reply-To: noreply@astergrouphome.com\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

echo "<h2>Email Test</h2>";
echo "<p>Attempting to send email to: <strong>$to_email</strong></p>";

$result = @mail($to_email, $subject, $message, $headers);

if ($result) {
    echo "<p style='color: green;'><strong>SUCCESS:</strong> Email was sent successfully!</p>";
    echo "<p>Check your inbox at: <strong>$to_email</strong></p>";
} else {
    echo "<p style='color: red;'><strong>ERROR:</strong> Email could not be sent.</p>";
    echo "<h3>To fix this, you need to configure SMTP:</h3>";
    echo "<ol>";
    echo "<li>Edit <code>C:\\xampp\\php\\php.ini</code></li>";
    echo "<li>Find the <code>[mail function]</code> section</li>";
    echo "<li>Set: <code>SMTP = smtp.gmail.com</code></li>";
    echo "<li>Set: <code>smtp_port = 587</code></li>";
    echo "<li>Set: <code>sendmail_from = wiruqoleqec072@gmail.com</code></li>";
    echo "<li>Restart Apache in XAMPP</li>";
    echo "</ol>";
    echo "<p><strong>Note:</strong> For Gmail, you'll also need to:</p>";
    echo "<ul>";
    echo "<li>Enable 2-Step Verification on your Gmail account</li>";
    echo "<li>Generate an App Password at: <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a></li>";
    echo "<li>Configure sendmail.ini with your App Password</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='environment.html'>Back to Admission Form</a></p>";
?>











