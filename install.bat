@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

echo/
echo #######################################################
echo ############  unzip php7.3 package   ##############
echo #######################################################
.\archive\unzip -o .\archive\php7.3.4nts.zip -d .\archive\

copy /Y .env.example .env
echo/
echo #######################################################
echo ############  start printer api service   ##############
echo #######################################################
start printer-start.bat

echo/
echo #######################################################
echo ############  open printer setting web page ###########
echo #######################################################
printer-setting.bat
