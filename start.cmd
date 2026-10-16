@echo off
REM launch without cd - Cyrillic paths break cmd after chcp 65001
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0bitrix-notes\start.ps1"
if errorlevel 1 pause
pause
