@echo off
chcp 65001 >nul
echo Checking Node.js, PHP, and Composer installation...
echo.

REM Store the current directory path
set "ORIGINAL_DIR=%CD%"

REM Check if Node.js is installed
node -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Node.js is not installed!
    pause
    exit /b
) else (
    echo Node.js: Installed
)

REM Check if PHP is installed
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed!
    pause
    exit /b
) else (
    echo PHP: Installed
)

REM Check if Composer is installed
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Composer is not installed or not found in PATH!
    pause
    exit /b
) else (
    echo Composer: Installed
)

REM Check for XAMPP MySQL first
echo.
echo Checking for XAMPP MySQL...
if exist "C:\xampp\mysql\bin\mysql.exe" (
    set "MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe"
    echo Found XAMPP MySQL.
) else (
    echo XAMPP MySQL not found. Checking for standalone MySQL...

    REM Check for standalone MySQL in default locations
    if exist "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" (
        set "MYSQL_PATH=C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe"
        echo Found standalone MySQL.
    ) else if exist "C:\Program Files\MySQL\MySQL Server 5.7\bin\mysql.exe" (
        set "MYSQL_PATH=C:\Program Files\MySQL\MySQL Server 5.7\bin\mysql.exe"
        echo Found standalone MySQL 5.7.
    ) else (
        echo ERROR: MySQL executable not found in XAMPP or default MySQL locations!
        pause
        exit /b
    )
)

REM Restore MySQL Database
echo.
echo Ensuring MySQL database exists...

REM Set MySQL credentials
set MYSQL_USER=root
set MYSQL_PASSWORD=
set MYSQL_DATABASE=empmonitor
set SQL_FILE="%CD%\Backend\Database_Configuration\db.sql"

REM Ensure MySQL service is running
if exist "C:\xampp\mysql\bin\mysql.exe" (
    echo Starting XAMPP MySQL service...
    net start mysql >nul 2>&1
) else (
    echo Starting standalone MySQL service...
    net start mysql >nul 2>&1
)

echo Using SQL File: %SQL_FILE%

REM Create database if it doesn't exist
echo Creating database if not exists...
echo CREATE DATABASE IF NOT EXISTS %MYSQL_DATABASE%; | "%MYSQL_PATH%" -u %MYSQL_USER% -p%MYSQL_PASSWORD%

if %errorlevel% neq 0 (
    echo ERROR: Failed to create database!
    pause
    exit /b
) else (
    echo Database ensured.
)

REM Run the MySQL restore command (handling spaces)
echo Restoring database...
"%MYSQL_PATH%" -u %MYSQL_USER% -p%MYSQL_PASSWORD% %MYSQL_DATABASE% < "%SQL_FILE%"

if %errorlevel% neq 0 (
    echo ERROR: Database restoration failed!
    pause
    exit /b
) else (
    echo Database restored successfully.
)

echo.
echo All dependencies are installed. Proceeding with PM2 installation...
echo.

REM Install PM2 globally
call npm install -g pm2 >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PM2 installation failed!
    pause
    exit /b
)

echo PM2 installation completed successfully.
echo.

REM Start EmpMonitor from QT
echo Navigating to "QT" directory to start EmpMonitor...
if exist "QT\Windows\empmonitor.exe" (
    cd /d "QT\Windows"
    echo Found empmonitor.exe, starting it...
    start "" "empmonitor.exe"
    echo empmonitor.exe started successfully.
    cd /d "%ORIGINAL_DIR%"
) else (
    echo ERROR: empmonitor.exe not found in QT directory!
    pause
    exit /b
)

REM Navigate to Backend and process subdirectories
echo Navigating to "Backend" directory...
if exist "Backend" (
    cd /d "Backend"
    echo Navigated to Backend directory.

    REM Process subdirectories within Backend
    for %%D in (Agent Main Report Store) do (
        echo Navigating to %%D directory...
        if exist "%%D" (
            cd /d "%%D"
            echo Navigated to %%D directory.

            REM Create .env file if it doesn't exist
            if not exist ".env" (
                if exist "sample.env" (
                    echo Creating .env file from sample.env in %%D...
                    copy "sample.env" ".env" >nul
                    echo .env file created successfully.
                ) else (
                    echo WARNING: sample.env not found in %%D. Skipping .env creation.
                )
            ) else (
                echo .env file already exists in %%D.
            )

            REM Install dependencies
            call npm install >nul 2>&1
            if %errorlevel% neq 0 (
                echo ERROR: npm install failed in %%D!
                pause
                exit /b
            )

            REM Start application
            call npm run start >nul 2>&1
            if %errorlevel% neq 0 (
                echo ERROR: npm run start failed in %%D!
                pause
                exit /b
            )

            REM Return to Backend directory
            cd /d "%ORIGINAL_DIR%\Backend"
        ) else (
            echo ERROR: %%D directory not found!
            pause
            exit /b
        )
    )

    REM Return to the original directory
    cd /d "%ORIGINAL_DIR%"
    echo Returned to the original directory.
) else (
    echo ERROR: "Backend" directory not found!
    pause
    exit /b
)

REM Navigate to Frontend and run Laravel server
echo Navigating to "Frontend" directory...
if exist "Frontend" (
    cd /d "Frontend"
    echo Navigated to Frontend directory.

    REM Ensure storage\sessions directory exists
    if not exist "storage\framework\sessions\" (
        echo Creating storage\framework\sessions\ directory...
        mkdir "storage\framework\sessions\"
        echo storage\framework\sessions\ directory created.
    ) else (
        echo storage\framework\sessions\ directory already exists.
    )

    REM Create .env file if it doesn't exist
    if not exist ".env" (
        if exist "sample.env" (
            echo Creating .env file from sample.env in Frontend...
            copy "sample.env" ".env" >nul
            echo .env file created successfully.
        ) else (
            echo WARNING: sample.env not found in Frontend. Skipping .env creation.
        )
    ) else (
        echo .env file already exists in Frontend.
    )

    REM Run Composer install
    call composer install
    if %errorlevel% neq 0 (
        echo ERROR: Composer install failed!
        pause
        exit /b
    )


    echo Please use the default credentials:
    echo.
    echo - Admin
    echo   - Email: admin@mail.com
    echo   - Password: Admin@123
    echo.
    echo - Employee
    echo   - Email: first_user@mail.com
    echo   - Password: User@123

    REM Start Laravel development server
    call php artisan serve
    if %errorlevel% neq 0 (
        echo ERROR: php artisan serve failed!
        pause
        exit /b
    )

    REM Return to the original directory
    cd /d "%ORIGINAL_DIR%"
    echo Returned to the original directory.
) else (
    echo ERROR: "Frontend" directory not found!
    pause
    exit /b
)

pause
