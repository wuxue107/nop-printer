@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

echo/
echo #######################################################
echo ############  unzip php-7.3 package   ##############
echo #######################################################
.\archive\unzip -o .\archive\php7.3.4nts.zip -d .\archive\

echo/
echo #######################################################
echo ############  unzip phantomjs-2.1.1 package   ##############
echo #######################################################
.\archive\unzip -o .\archive\phantomjs-2.1.1-windows.zip -d .\archive\

copy /Y .env.example .env

php -r "touch('database/database.sqlite');"
php artisan migrate

