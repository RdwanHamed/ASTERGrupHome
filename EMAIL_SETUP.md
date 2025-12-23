# Email Setup Guide for Contact Form

## ✅ What's Been Set Up

The contact form is now configured to send emails to: **aster.grouphome@outlook.com**

When someone fills out the form and clicks "Send Message", it will:
1. Validate all required fields
2. Send an email to your Outlook email address
3. Show a success message to the user
4. Clear the form

## ⚙️ XAMPP Email Configuration

**Important:** XAMPP's PHP `mail()` function needs to be configured to actually send emails. Here are your options:

### Option 1: Configure PHP mail() (For Local Testing)

1. **Edit `php.ini` file** (usually in `C:\xampp\php\php.ini`)

2. **Find the `[mail function]` section** and configure:

```ini
[mail function]
SMTP = smtp.office365.com
smtp_port = 587
sendmail_from = aster.grouphome@outlook.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
```

3. **Configure sendmail** (if using sendmail):
   - Edit `C:\xampp\sendmail\sendmail.ini`
   - Set your Outlook SMTP settings

### Option 2: Use PHPMailer (Recommended for Production)

For a more reliable solution, consider using PHPMailer library which works better with Outlook/Office365.

### Option 3: Use a Form Service (Easiest)

For production websites, consider using:
- **Formspree** - Free form handling service
- **EmailJS** - Client-side email service
- **Google Forms** - Simple alternative

## 🧪 Testing the Form

1. Make sure XAMPP Apache and PHP are running
2. Open `http://localhost/ASTERGrupHome/contact.html`
3. Fill out the form
4. Submit and check your email inbox

## 📧 Email Format

The email you'll receive will look like this:

```
Subject: Aster Group Home - [Subject Type]

New Contact Form Submission from Aster Group Home Website

Name: [Visitor's Name]
Email: [Visitor's Email]
Phone: [Phone Number or "Not provided"]
Subject: [Selected Subject]

Message:
[Visitor's Message]

---
This message was sent from the Aster Group Home contact form.
Submitted on: [Date and Time]
```

## 🔒 Security Notes

- The form includes basic validation
- Input is sanitized to prevent XSS attacks
- For production, consider adding:
  - CAPTCHA (to prevent spam)
  - Rate limiting
  - CSRF token protection
  - Additional server-side validation

## 🚀 For Production Hosting

When you move to a live hosting server:
1. The PHP mail() function should work automatically
2. Or configure SMTP settings for your hosting provider
3. Test thoroughly before going live

## ❓ Troubleshooting

**Emails not sending?**
- Check PHP error logs in `C:\xampp\php\logs\`
- Verify SMTP settings in php.ini
- Check spam/junk folder
- Test with a simple PHP mail script first

**Need help?**
- Check XAMPP documentation
- Contact your hosting provider for SMTP settings
- Consider using a third-party form service for easier setup











