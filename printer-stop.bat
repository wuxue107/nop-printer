@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts

php artisan serve --port=8077
