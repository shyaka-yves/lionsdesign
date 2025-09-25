## Email Setup (Secure)

Configure email sending securely using environment variables. Do not commit real credentials to the repository.

### 1) Create a .env file (not committed)
Create `.env` at the project root (one level above `config/`). Use:

```
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-inbox-or-smtp-user
MAIL_PASSWORD=your-strong-password-or-api-key
MAIL_ENCRYPTION=tls
MAIL_TIMEOUT=30

# Optional sender identity and bounce
MAIL_FROM_ADDRESS=you@yourdomain.com
MAIL_FROM_NAME=Lions Design
MAIL_BOUNCE_ADDRESS=bounce@yourdomain.com

# Optional DKIM (recommended)
MAIL_DKIM_ENABLED=true
MAIL_DKIM_DOMAIN=yourdomain.com
MAIL_DKIM_SELECTOR=default
MAIL_DKIM_PRIVATE_KEY_PATH=/absolute/path/to/dkim-private.key
MAIL_DKIM_PASSPHRASE=
```

Ensure `.env` is excluded from version control. On hosting, set these via the control panel or server environment.

### 2) How configuration is loaded
- `config/env.php` loads `.env` into `$_ENV` and process env.
- `config/email.php` and `config/email_hosting.php` read only from env. No plaintext credentials remain in code.
- `includes/email_hosting.php` prefers env-driven SMTP and falls back to PHP `mail()` if SMTP is unavailable.

### 3) Provider notes
- Gmail: enable 2FA and use an App Password.
- cPanel/hosted mailboxes: use provider SMTP host and app-specific password where possible.

### 4) Improve deliverability (highly recommended)
- SPF: add a TXT record like `v=spf1 a mx include:_spf.yourhost.com ~all` (check host docs).
- DKIM: enable in your hosting/email panel or upload the public key for your selector.
- DMARC: add a TXT record `_dmarc.yourdomain.com` with `v=DMARC1; p=quarantine; rua=mailto:dmarc@yourdomain.com`.
- From and Return-Path: set MAIL_FROM_ADDRESS to your domain and MAIL_BOUNCE_ADDRESS to a valid mailbox.
- Content: both HTML and plain-text are sent automatically.

### 5) Testing
- Use `test_email.php` pages if available, or trigger flows that send OTPs.
- Check server mail logs or `email_debug.log` if present.

### 6) Security
- Do not log configs or secrets.
- Rotate any credentials that were previously committed.
