@echo off
color 0E
title Enable PostgreSQL in XAMPP

echo ========================================
echo  Enable PostgreSQL Driver in XAMPP
echo ========================================
echo.

set PHP_INI=C:\xampp\php\php.ini

if not exist "%PHP_INI%" (
    echo ERROR: php.ini not found at %PHP_INI%
    echo.
    echo Please check if XAMPP is installed correctly.
    pause
    exit /b 1
)

echo Creating backup of php.ini...
copy "%PHP_INI%" "%PHP_INI%.backup" >nul

echo.
echo Enabling PostgreSQL extensions...

powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=pdo_pgsql', 'extension=pdo_pgsql' -replace ';extension=pgsql', 'extension=pgsql' | Set-Content '%PHP_INI%'"

echo.
echo ========================================
echo  SUCCESS! Extensions Enabled
echo ========================================
echo.
echo Changes made:
echo - extension=pdo_pgsql (enabled)
echo - extension=pgsql (enabled)
echo.
echo Backup saved: %PHP_INI%.backup
echo.
echo ========================================
echo  IMPORTANT: Restart Apache!
echo ========================================
echo.
echo 1. Open XAMPP Control Panel
echo 2. Stop Apache
echo 3. Start Apache again
echo.
echo Then refresh your browser.
echo.
pause
