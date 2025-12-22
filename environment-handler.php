<?php
/**
 * Admission Form Handler for Aster Group Home
 * Sends admission form submissions to the configured email address
 */

// Load email configuration
require_once __DIR__ . '/email-config.php';
require_once __DIR__ . '/smtp-mail.php';

// Set the recipient email address from config
$to_email = RECIPIENT_EMAIL;
$to_name = "Aster Group Home";

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data
    $admission_day = isset($_POST['admission_day']) ? trim(htmlspecialchars($_POST['admission_day'])) : '';
    $admission_month = isset($_POST['admission_month']) ? trim(htmlspecialchars($_POST['admission_month'])) : '';
    $admission_year = isset($_POST['admission_year']) ? trim(htmlspecialchars($_POST['admission_year'])) : '';
    
    $full_name = isset($_POST['full_name']) ? trim(htmlspecialchars($_POST['full_name'])) : '';
    $nickname = isset($_POST['nickname']) ? trim(htmlspecialchars($_POST['nickname'])) : '';
    $birth_day = isset($_POST['birth_day']) ? trim(htmlspecialchars($_POST['birth_day'])) : '';
    $birth_month = isset($_POST['birth_month']) ? trim(htmlspecialchars($_POST['birth_month'])) : '';
    $birth_year = isset($_POST['birth_year']) ? trim(htmlspecialchars($_POST['birth_year'])) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    // Sanitize email but preserve format for validation
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $gender = isset($_POST['gender']) ? trim(htmlspecialchars($_POST['gender'])) : '';
    $country = isset($_POST['country']) ? trim(htmlspecialchars($_POST['country'])) : '';
    $phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : '';
    
    $place_of_birth = isset($_POST['place_of_birth']) ? trim(htmlspecialchars($_POST['place_of_birth'])) : '';
    $nationality = isset($_POST['nationality']) ? trim(htmlspecialchars($_POST['nationality'])) : '';
    $address = isset($_POST['address']) ? trim(htmlspecialchars($_POST['address'])) : '';
    $city = isset($_POST['city']) ? trim(htmlspecialchars($_POST['city'])) : '';
    $zip_code = isset($_POST['zip_code']) ? trim(htmlspecialchars($_POST['zip_code'])) : '';
    $present_state = isset($_POST['present_state']) ? trim(htmlspecialchars($_POST['present_state'])) : '';
    $ssn = isset($_POST['ssn']) ? trim(htmlspecialchars($_POST['ssn'])) : '';
    
    $emergency_name = isset($_POST['emergency_name']) ? trim(htmlspecialchars($_POST['emergency_name'])) : '';
    $emergency_phone = isset($_POST['emergency_phone']) ? trim(htmlspecialchars($_POST['emergency_phone'])) : '';
    $emergency_relationship = isset($_POST['emergency_relationship']) ? trim(htmlspecialchars($_POST['emergency_relationship'])) : '';
    $emergency_address = isset($_POST['emergency_address']) ? trim(htmlspecialchars($_POST['emergency_address'])) : '';
    $emergency_address2 = isset($_POST['emergency_address2']) ? trim(htmlspecialchars($_POST['emergency_address2'])) : '';
    $emergency_alternate_phone = isset($_POST['emergency_alternate_phone']) ? trim(htmlspecialchars($_POST['emergency_alternate_phone'])) : '';
    
    $physician_name = isset($_POST['physician_name']) ? trim(htmlspecialchars($_POST['physician_name'])) : '';
    $physician_phone = isset($_POST['physician_phone']) ? trim(htmlspecialchars($_POST['physician_phone'])) : '';
    $physician_clinic = isset($_POST['physician_clinic']) ? trim(htmlspecialchars($_POST['physician_clinic'])) : '';
    
    // Validation
    $errors = [];
    
    if (empty($full_name)) {
        $errors[] = "Full Name is required.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // More detailed error message
        $errors[] = "Invalid email format. Please enter a valid email address (e.g., name@example.com).";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone Number is required.";
    }
    
    if (empty($emergency_name)) {
        $errors[] = "Emergency Contact Name is required.";
    }
    
    if (empty($emergency_phone)) {
        $errors[] = "Emergency Contact Phone is required.";
    }
    
    if (empty($physician_name)) {
        $errors[] = "Primary Care Physician Name is required.";
    }
    
    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $error_message = implode(" ", $errors);
        header("Location: environment.html?error=" . urlencode($error_message));
        exit;
    }
    
    // Format dates
    $admission_date = $admission_day . '/' . $admission_month . '/' . $admission_year;
    $birth_date = $birth_day . '/' . $birth_month . '/' . $birth_year;
    
    // Prepare email content
    $email_subject = "Aster Group Home - New Admission Form Submission";
    
    $email_body = "NEW ADMISSION FORM SUBMISSION\n";
    $email_body .= "================================\n\n";
    $email_body .= "Submitted on: " . date('F j, Y, g:i a') . "\n\n";
    
    $email_body .= "DATE OF ADMISSION: " . $admission_date . "\n\n";
    
    $email_body .= "--- PERSONAL INFORMATION ---\n";
    $email_body .= "Full Name: " . $full_name . "\n";
    $email_body .= "Nickname: " . ($nickname ? $nickname : "Not provided") . "\n";
    $email_body .= "Date of Birth: " . $birth_date . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Gender: " . $gender . "\n";
    $email_body .= "Country: " . $country . "\n";
    $email_body .= "Phone Number: " . $phone . "\n\n";
    
    $email_body .= "--- FULL RESIDENTIAL ADDRESS ---\n";
    $email_body .= "Place Of Birth: " . $place_of_birth . "\n";
    $email_body .= "Nationality: " . $nationality . "\n";
    $email_body .= "Address: " . $address . "\n";
    $email_body .= "City: " . $city . "\n";
    $email_body .= "Zip Code: " . $zip_code . "\n";
    $email_body .= "Present State: " . $present_state . "\n";
    $email_body .= "Social Security Number: " . ($ssn ? $ssn : "Not provided") . "\n\n";
    
    $email_body .= "--- EMERGENCY CONTACT INFORMATION ---\n";
    $email_body .= "Name: " . $emergency_name . "\n";
    $email_body .= "Phone: " . $emergency_phone . "\n";
    $email_body .= "Relationship to Patient: " . $emergency_relationship . "\n";
    $email_body .= "Address: " . $emergency_address . "\n";
    if ($emergency_address2) {
        $email_body .= "Address (Line 2): " . $emergency_address2 . "\n";
    }
    $email_body .= "Alternate Phone: " . ($emergency_alternate_phone ? $emergency_alternate_phone : "Not provided") . "\n\n";
    
    $email_body .= "--- PRIMARY CARE PHYSICIAN ---\n";
    $email_body .= "Name: " . $physician_name . "\n";
    $email_body .= "Phone: " . $physician_phone . "\n";
    $email_body .= "Clinic/Hospital: " . $physician_clinic . "\n\n";
    
    $email_body .= "---\n";
    $email_body .= "This admission form was submitted from the Aster Group Home website.\n";
    $email_body .= "Please contact the applicant at: " . $email . " or " . $phone . "\n";
    
    // For testing: Save to file first (always save as backup)
    $submissions_dir = __DIR__ . '/submissions';
    if (!file_exists($submissions_dir)) {
        mkdir($submissions_dir, 0755, true);
    }
    
    // Save submission to file for backup (always save)
    $filename = $submissions_dir . '/admission_' . date('Y-m-d_His') . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $full_name) . '.txt';
    $file_saved = file_put_contents($filename, $email_body);
    
    // Try to send email
    $mail_sent = false;
    
    if (SMTP_ENABLED && !empty(SMTP_PASSWORD)) {
        // Use SMTP to send email
        $mail_sent = sendSMTPEmail(
            $to_email,
            $email_subject,
            $email_body,
            SMTP_FROM_EMAIL,
            SMTP_FROM_NAME,
            SMTP_USERNAME,
            SMTP_PASSWORD
        );
        
        // Log SMTP result for debugging
        if (!$mail_sent) {
            error_log("SMTP email sending failed for admission form from: $email");
            error_log("SMTP Config - Enabled: " . (SMTP_ENABLED ? 'true' : 'false') . ", Password Set: " . (!empty(SMTP_PASSWORD) ? 'yes' : 'no'));
        } else {
            error_log("SMTP email sent successfully for admission form from: $email");
        }
    } else {
        // Fallback to PHP mail() function (may not work on XAMPP)
        $headers = "From: " . $full_name . " <" . $email . ">\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $mail_sent = @mail($to_email, $email_subject, $email_body, $headers);
    }
    
    // Check if form was saved successfully
    if ($file_saved !== false) {
        // Success - redirect to environment page with success message
        // Form data is always saved to file, even if email fails
        if ($mail_sent) {
            header("Location: environment.html?success=1");
        } else {
            // File saved but email didn't send
            $error_note = "Form submitted successfully and saved. ";
            if (!SMTP_ENABLED || empty(SMTP_PASSWORD)) {
                $error_note .= "Email not sent - SMTP is not configured. Please run setup-email.bat to enable email sending.";
            } else {
                $error_note .= "Email sending failed. Please check your SMTP configuration or contact support at (240) 833-8151.";
            }
            header("Location: environment.html?success=1&note=" . urlencode($error_note));
        }
        exit;
    } else {
        // Error saving file
        header("Location: environment.html?error=" . urlencode("Sorry, there was an error saving your admission form. Please try calling us at (240) 833-8151."));
        exit;
    }
    
} else {
    // If accessed directly without POST, redirect to environment page
    header("Location: environment.html");
    exit;
}
?>







