@echo off

::
:: start the api service
:: 启动打印机API服务
::


set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts

%PHP_PATH%\php artisan serve --port=8077

