@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%

php artisan serve
