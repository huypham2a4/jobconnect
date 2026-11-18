@echo off
setlocal
title Dung web JobConnect
powershell.exe -NoLogo -NoProfile -ExecutionPolicy Bypass -File "%~dp0scratch\jobconnect-local.ps1" -Stop
if errorlevel 1 (
    pause
    exit /b 1
)
endlocal
