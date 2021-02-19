@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

::
:: quick start 
::

start printer-start.bat

timeout /T 5
printer-setting.bat
