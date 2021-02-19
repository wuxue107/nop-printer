@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

::
:: start the api service
::

if not exist "%PHP_PATH%" (
    start /WAIT /B CMD /C install.bat
    :: call install.bat
)

echo/
echo #######################################################
echo ############  start image-print worker queue ##########
echo #######################################################
start php artisan queue:work --queue=image-print --sleep=1 --tries=1

echo/
echo #######################################################
echo ############  start tpl-print worker queue ##########
echo #######################################################
start php artisan queue:work --queue=tpl-print --sleep=1 --tries=1

echo/
echo #######################################################
echo ############  start printer api service   ##############
echo #######################################################
php artisan serve --port=8077
