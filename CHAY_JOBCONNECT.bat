@echo off
setlocal
title Khoi dong JobConnect
powershell.exe -NoLogo -NoProfile -ExecutionPolicy Bypass -File "%~dp0scratch\jobconnect-local.ps1" %*
if errorlevel 1 (
    echo.
    echo Khong khoi dong duoc. Hay doc thong bao loi o tren.
    pause
    exit /b 1
)
endlocal
