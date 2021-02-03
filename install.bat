@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%

.\archive\unzip .\archive\php7.3.4nts.zip

cd ..
set PHP_PATH=%SCRIPT_PATH%\archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

php -m
