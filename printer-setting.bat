@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

::
:: Open then printer setting url
::

echo/
echo #######################################################
echo ############  open printer setting web page ###########
echo #######################################################

echo http://localhost:8077/printer-setting
start "http://localhost:8077/printer-setting"
