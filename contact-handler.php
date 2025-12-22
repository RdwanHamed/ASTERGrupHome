<?php
/**
 * Contact Form Handler for Aster Group Home
 * Sends form submissions to aster.grouphome@outlook.com
 */

// Set the recipient email address
$to_email = "aster.grouphome@outlook.com";
$to_name = "Aster Group Home";

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data
    $name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : '';
    $subject = isset($_POST['subject']) ? trim(htmlspecialchars($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : '';
    $consent = isset($_POST['consent']) ? true : false;
    
    // Subject mapping for email subject line
    $subject_map = [
        'general' => 'General Inquiry',
        'services' => 'Services Information Request',
        'consultation' => 'Consultation Request',
        'pricing' => 'Pricing Information Request',
        'other' => 'Other Inquiry'
    ];
    
    $email_subject = isset($subject_map[$subject]) ? $subject_map[$subject] : 'Contact Form Submission';
    $email_subject = "Aster Group Home - " . $email_subject;
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required.";
    }
    
    if (!$consent) {
        $errors[] = "Consent is required.";
    }
    
    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $error_message = implode(" ", $errors);
        header("Location: contact.html?error=" . urlencode($error_message));
        exit;
    }
    
    // Prepare email content
    $email_body = "New Contact Form Submission from Aster Group Home Website\n\n";
    $email_body .= "Name: " . $name . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Phone: " . ($phone ? $phone : "Not provided") . "\n";
    $email_body .= "Subject: " . $email_subject . "\n\n";
    $email_body .= "Message:\n" . $message . "\n\n";
    $email_body .= "---\n";
    $email_body .= "This message was sent from the Aster Group Home contact form.\n";
    $email_body .= "Submitted on: " . date('F j, Y, g:i a') . "\n";
    
    // Email headers
    $headers = "From: " . $name . " <" . $email . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send email
    $mail_sent = mail($to_email, $email_subject, $email_body, $headers);
    
    if ($mail_sent) {
        // Success - redirect to thank you page or contact page with success message
        header("Location: contact.html?success=1");
        exit;
    } else {
        // Error sending email
        header("Location: contact.html?error=" . urlencode("Sorry, there was an error sending your message. Please try calling us at (240) 833-8151."));
        exit;
    }
    
} else {
    // If accessed directly without POST, redirect to contact page
    header("Location: contact.html");
    exit;
}
?>





