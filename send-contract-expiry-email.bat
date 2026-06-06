@echo off
REM =================================================================
REM Script untuk mengirim email reminder kontrak yang akan berakhir
REM =================================================================

echo.
echo ====================================================
echo  EMAIL REMINDER KONTRAK YANG AKAN BERAKHIR
echo ====================================================
echo.

REM Navigasi ke folder project
cd /d "c:\inetpub\wwwroot\dakar-avi"

REM Jalankan command artisan untuk mengirim email
php artisan email:contract-expiry-reminder

echo.
echo ====================================================
echo  Proses selesai!
echo ====================================================
echo.
pause
