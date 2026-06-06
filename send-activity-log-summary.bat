@echo off
REM =================================================================
REM Script untuk mengirim rekap activity log harian
REM (untuk aktivitas hari kemarin)
REM =================================================================

echo.
echo ====================================================
echo  REKAP ACTIVITY LOG HARIAN
echo ====================================================
echo.

REM Navigasi ke folder project
cd /d "c:\inetpub\wwwroot\dakar-avi"

REM Jalankan command artisan untuk mengirim summary
echo Mengirimkan rekap aktivitas hari kemarin...
echo.
php artisan email:activity-log-summary

echo.
echo ====================================================
echo  Proses selesai!
echo ====================================================
echo.
pause
