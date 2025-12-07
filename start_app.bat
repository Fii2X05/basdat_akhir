@echo off
echo ========================================
echo Starting SIAKAD Application
echo ========================================
echo.
echo Application will be available at:
echo http://localhost:8000
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

cd bdakhir
php -S localhost:8000

pause
