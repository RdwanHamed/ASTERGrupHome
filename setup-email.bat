@echo off
REM Email Setup Script for Aster Group Home
REM This batch file runs the PowerShell setup script

echo.
echo ========================================
echo   Aster Group Home - Email Setup
echo ========================================
echo.
echo Starting email configuration...
echo.

REM Change to the script directory
cd /d "%~dp0"

REM Run the PowerShell script
powershell.exe -ExecutionPolicy Bypass -File "setup-email.ps1"

pause






