@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

::
:: 删除缓存文件
::


php artisan cache:clear
php artisan view:clear
php artisan route:clear 

del /f /s /q storage\runtime\image\*.png
del /f /s /q storage\runtime\image\*.jpg
del /f /s /q storage\runtime\*.log
del /f /s /q storage\logs\*.log
