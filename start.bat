@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%

start php artisan serve --port=8077
