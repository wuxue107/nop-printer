@echo off

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%

echo\
echo "#######################################################"
echo "############  解压缩 php7.3 独立 安装包   ##############"
echo "#######################################################"
.\archive\unzip .\archive\php7.3.4nts.zip -d .\archive\
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts
set PATH=%PHP_PATH%;%PATH%

echo\
echo "#######################################################"
echo "############  正则安装 php composer 依赖  ##############"
echo "#######################################################"
composer.bat install
copy /Y .env.example .env

echo\
echo "#######################################################"
echo "############  启动 printer api service   ##############"
echo "#######################################################"
start start.bat
timeout /T 5
echo\
echo "#######################################################"
echo "############  打开 printer setting web page ###########"
echo "#######################################################"

start http://localhost:8077/printer-setting
