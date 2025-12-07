@echo off
color 0A
title SIAKAD Database Setup

echo.
echo ========================================
echo    SIAKAD Database Setup Wizard
echo ========================================
echo.

:ASK_PASSWORD
echo Please enter your PostgreSQL password:
echo (Common defaults: postgres, root, admin, 220306)
echo.
set /p PGPASS="Password: "

if "%PGPASS%"=="" (
    echo Error: Password cannot be empty!
    goto ASK_PASSWORD
)

echo.
echo Testing connection...
set PGPASSWORD=%PGPASS%

:TRY_PORT_5432
echo Trying port 5432...
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -p 5432 -c "SELECT 1;" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PGPORT=5432
    goto CONNECTION_SUCCESS
)

:TRY_PORT_5433
echo Trying port 5433...
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -p 5433 -c "SELECT 1;" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PGPORT=5433
    goto CONNECTION_SUCCESS
)

echo.
echo ========================================
echo ERROR: Cannot connect to PostgreSQL!
echo ========================================
echo.
echo Possible reasons:
echo 1. Wrong password
echo 2. PostgreSQL is not running
echo 3. PostgreSQL is on a different port
echo.
echo Please:
echo 1. Check PostgreSQL service is running
echo 2. Verify your password
echo 3. Try again
echo.
pause
goto ASK_PASSWORD

:CONNECTION_SUCCESS
echo.
echo ========================================
echo SUCCESS! Connected to PostgreSQL
echo ========================================
echo Port: %PGPORT%
echo.

echo Updating config file...
powershell -Command "(Get-Content 'bdakhir\config\database.php') -replace '\$port = \"[0-9]+\";', '\$port = \"%PGPORT%\";' -replace '\$pass = \".*\";', '\$pass = \"%PGPASS%\";' | Set-Content 'bdakhir\config\database.php'"

echo.
echo Creating database 'db_kampus'...
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -p %PGPORT% -c "DROP DATABASE IF EXISTS db_kampus;" 2>nul
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -p %PGPORT% -c "CREATE DATABASE db_kampus;"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to create database!
    pause
    exit /b 1
)

echo.
echo Importing schema and sample data...
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -p %PGPORT% -d db_kampus -f database_schema.sql

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to import schema!
    pause
    exit /b 1
)

echo.
echo Verifying tables...
"C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -p %PGPORT% -d db_kampus -c "\dt"

echo.
echo ========================================
echo    SETUP COMPLETE! 
echo ========================================
echo.
echo Database: db_kampus
echo Port: %PGPORT%
echo Tables: 6 tables created
echo Sample Data: Inserted successfully
echo.
echo Configuration updated in:
echo bdakhir\config\database.php
echo.
echo ========================================
echo    Ready to run the application!
echo ========================================
echo.
echo Choose how to run:
echo.
echo 1. Using XAMPP (Recommended)
echo    - Start Apache in XAMPP Control Panel
echo    - Open: http://localhost/basdat_akhir/bdakhir/
echo.
echo 2. Using PHP Built-in Server
echo    - Run: start_app.bat
echo    - Open: http://localhost:8000
echo.
pause
