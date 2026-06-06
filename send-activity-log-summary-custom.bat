@echo off
REM =================================================================
REM Script untuk mengirim rekap activity log harian dengan tanggal custom
REM =================================================================

echo.
echo ====================================================
echo  REKAP ACTIVITY LOG - CUSTOM DATE
echo ====================================================
echo.

REM Navigasi ke folder project
cd /d "c:\inetpub\wwwroot\dakar-avi"

REM Tanyakan tanggal kepada user
set /p tanggal=Masukkan tanggal (format: YYYY-MM-DD) atau tekan Enter untuk kemarin: 

REM Jika kosong, gunakan kemarin
if "%tanggal%"=="" (
    echo Menggunakan tanggal hari kemarin...
    php artisan email:activity-log-summary
) else (
    echo Mengirimkan rekap untuk tanggal: %tanggal%...
    php artisan email:activity-log-summary --date=%tanggal%
)

echo.
echo ====================================================
echo  Proses selesai!
echo ====================================================
echo.
pause
