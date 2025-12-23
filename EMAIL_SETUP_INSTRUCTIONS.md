# Email Setup Instructions for Admission Form

## Current Status
The admission form is configured to send emails to: **wiruqoleqec072@gmail.com**

However, PHP's `mail()` function doesn't work on XAMPP without proper SMTP configuration.

## Solution: Configure XAMPP to Send Emails

### Option 1: Configure PHP mail() with Gmail SMTP (Recommended)

1. **Edit PHP Configuration File**
   - Open: `C:\xampp\php\php.ini`
   - Find the `[mail function]` section

2. **Update the mail function settings:**
   ```ini
   [mail function]
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = wiruqoleqec072@gmail.com
   sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
   ```

3. **Configure Sendmail (if using sendmail)**
   - Edit: `C:\xampp\sendmail\sendmail.ini`
   - Set:
   ```ini
   [sendmail]
   smtp_server=smtp.gmail.com
   smtp_port=587
   auth_username=wiruqoleqec072@gmail.com
   auth_password=YOUR_GMAIL_APP_PASSWORD
   ```

4. **Get Gmail App Password:**
   - Go to: https://myaccount.google.com/apppasswords
   - Enable 2-Step Verification if not already enabled
   - Generate an App Password for "Mail"
   - Use that 16-character password in sendmail.ini

5. **Restart Apache** in XAMPP Control Panel

### Option 2: Use PHPMailer (More Reliable)

1. Download PHPMailer from: https://github.com/PHPMailer/PHPMailer
2. Extract to: `C:\xampp\htdocs\ASTERGrupHome\vendor\PHPMailer\`
3. Update `environment-handler.php` to use PHPMailer

### Option 3: Use a Form Service (Easiest for Testing)

- **Formspree.io** - Free form handling
- **EmailJS** - Client-side email service
- **Google Forms** - Simple alternative

## Current Workaround

The form currently **saves all submissions to files** in the `submissions/` folder as a backup. You can check these files even if email doesn't work.

## Testing

1. Submit the admission form
2. Check the `submissions/` folder for saved files
3. If SMTP is configured, check your email inbox

## Need Help?

The form data is being saved successfully. To enable actual email sending, you need to configure SMTP settings as described above.











