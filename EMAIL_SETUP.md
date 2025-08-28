# Email Setup Guide for Lions Design

This guide will help you set up email functionality for OTP (One-Time Password) sending in your Lions Design application.

## Current Status

✅ **Fixed Issues:**
- Replaced manual SMTP implementation with proper PHPMailer library
- Updated email configuration to use Gmail SMTP (more reliable)
- Improved error handling and logging
- Added HTML email formatting for better appearance

## Setup Instructions

### Option 1: Gmail SMTP (Recommended)

1. **Enable 2-Factor Authentication**
   - Go to your Google Account settings
   - Navigate to Security → 2-Step Verification
   - Enable 2-Step Verification if not already enabled

2. **Generate App Password**
   - Go to: https://myaccount.google.com/apppasswords
   - Select "Mail" from the dropdown
   - Click "Generate"
   - Copy the 16-character password

3. **Update Configuration**
   - Open `config/email.php`
   - Replace `'your-gmail-app-password'` with your actual App Password
   - Example:
     ```php
     'smtp_password' => 'abcd efgh ijkl mnop', // Your actual App Password
     ```

### Option 2: Brevo (Sendinblue)

1. **Sign up for Brevo**
   - Go to: https://www.brevo.com/
   - Create a free account (300 emails/day)

2. **Get API Key**
   - Log into your Brevo dashboard
   - Go to Settings → API Keys
   - Copy your API key

3. **Update Configuration**
   - Open `config/email.php`
   - Uncomment the Brevo section and comment out Gmail
   - Replace `'your-brevo-api-key'` with your actual API key

## Testing

1. **Access Test Page**
   - Navigate to: `http://localhost/liondesign/test_email.php`
   - Enter an email address to test

2. **Check Results**
   - If successful: You'll see a success message with the OTP
   - If failed: Check the error message and configuration

## Troubleshooting

### Common Issues:

1. **"Authentication failed"**
   - Make sure you're using an App Password (not your regular Gmail password)
   - Verify 2-factor authentication is enabled

2. **"Connection failed"**
   - Check your internet connection
   - Verify SMTP host and port are correct

3. **"Email not received"**
   - Check spam/junk folder
   - Verify the email address is correct

### Debug Mode

To enable debug mode for troubleshooting:
1. Open `includes/functions.php`
2. Find the line: `$mail->SMTPDebug = 0;`
3. Change it to: `$mail->SMTPDebug = 2;`
4. Test again and check the output

## Files Modified

- `config/email.php` - Updated email configuration
- `includes/functions.php` - Replaced manual SMTP with PHPMailer
- `test_email.php` - Enhanced test page with better UI and instructions

## Security Notes

- Never commit real passwords to version control
- Use environment variables for production
- Regularly rotate App Passwords
- Monitor email sending logs

## Next Steps

1. Configure your email credentials
2. Test the email functionality
3. Try the registration and password reset features
4. Monitor error logs if issues persist

For additional help, check the error logs in your XAMPP installation or contact support.
