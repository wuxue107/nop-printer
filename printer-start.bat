@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

::
:: start the api service
::

if not exist "%PHP_PATH%" (
    call install.bat
)

echo/
echo #######################################################
echo ############  start printer api service   ##############
echo #######################################################
%PHP_PATH%\php artisan serve --port=8077
