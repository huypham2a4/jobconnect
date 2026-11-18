@echo off
setlocal
title Kiem thu tat ca chuc nang JobConnect
powershell.exe -NoLogo -NoProfile -ExecutionPolicy Bypass -File "%~dp0scratch\jobconnect-local.ps1" -Test
set "JOBCONNECT_TEST_EXIT=%ERRORLEVEL%"
echo.
if "%JOBCONNECT_TEST_EXIT%"=="0" (echo TAT CA BAI TEST DA QUA.) else (echo CO BAI TEST CHUA QUA. Hay xem ket qua o tren.)
pause
exit /b %JOBCONNECT_TEST_EXIT%
