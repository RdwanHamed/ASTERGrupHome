# Email Setup Script for Aster Group Home
# This script will help you configure email sending for the admission form

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Aster Group Home - Email Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Open browser to get App Password
Write-Host "Step 1: Getting Gmail App Password" -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow
Write-Host "1. A browser window will open to Google App Passwords page"
Write-Host "2. Make sure 2-Step Verification is enabled on your Gmail account"
Write-Host "3. Generate an App Password for Mail"
Write-Host "4. Copy the 16-character password (it looks like: abcd efgh ijkl mnop)"
Write-Host ""
$response = Read-Host "Press ENTER to open the App Passwords page"

# Open browser to App Passwords page
Start-Process "https://myaccount.google.com/apppasswords"

Write-Host ""
Write-Host "Step 2: Enter Your App Password" -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow
Write-Host "Paste your 16-character App Password below (you can include spaces, they will be removed)"
Write-Host ""

# Get App Password from user
$appPassword = Read-Host "Enter Gmail App Password" -AsSecureString
$appPasswordPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [Runtime.InteropServices.Marshal]::SecureStringToBSTR($appPassword)
)
# Remove spaces and any non-alphanumeric characters (keep only the password)
$appPasswordPlain = $appPasswordPlain -replace '\s+', ''

# Validate password length (should be 16 characters)
if ($appPasswordPlain.Length -lt 16) {
    Write-Host ""
    Write-Host "WARNING: App Password should be 16 characters. You entered: $($appPasswordPlain.Length) characters" -ForegroundColor Red
    $continue = Read-Host "Continue anyway? (y/n)"
    if ($continue -ne 'y' -and $continue -ne 'Y') {
        Write-Host "Setup cancelled." -ForegroundColor Yellow
        exit
    }
}

Write-Host ""
Write-Host "Step 3: Updating Configuration" -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow

# Read the current config file
$configFile = Join-Path $PSScriptRoot "email-config.php"
$configContent = Get-Content $configFile -Raw

# Update SMTP_PASSWORD
$configContent = $configContent -replace "define\('SMTP_PASSWORD',\s*'[^']*'\);", "define('SMTP_PASSWORD', '$appPasswordPlain');"

# Update SMTP_ENABLED to true
$configContent = $configContent -replace "define\('SMTP_ENABLED',\s*false\);", "define('SMTP_ENABLED', true);"

# Write the updated config
Set-Content -Path $configFile -Value $configContent -NoNewline

Write-Host "Configuration file updated!" -ForegroundColor Green
Write-Host ""

# Display summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Setup Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Email Configuration:" -ForegroundColor Yellow
Write-Host "  Recipient: wiruqoleqec072@gmail.com"
Write-Host "  SMTP Enabled: true"
Write-Host "  SMTP Username: wiruqoleqec072@gmail.com"
Write-Host ""
Write-Host "The admission form will now send emails automatically!" -ForegroundColor Green
Write-Host ""
Write-Host "To test, submit the admission form or run: test-email.php" -ForegroundColor Cyan
Write-Host ""

# Ask if they want to test
$test = Read-Host "Would you like to test the email configuration now? (y/n)"
if ($test -eq 'y' -or $test -eq 'Y') {
    Write-Host ""
    Write-Host "Opening test page..." -ForegroundColor Yellow
    Start-Process "http://localhost/ASTERGrupHome/test-email.php"
}

Write-Host ""
Write-Host "Setup complete! Press ENTER to exit..."
Read-Host









