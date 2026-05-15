@echo off
REM ============================================================
REM Laravel Schedule Runner for Dakar Project
REM Runs: php artisan schedule:run
REM Time: Setiap tanggal 1 bulan jam 08:00 (Asia/Jakarta)
REM ============================================================

REM Set working directory ke project root
cd /d C:\inetpub\wwwroot\dakar

REM Run Laravel scheduler
php artisan schedule:run >> storage\logs\scheduler.log 2>&1

REM Exit dengan code 0 jika sukses
exit /b 0
