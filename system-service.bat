@echo off

::
:: register system service in to windows. & Boot up
:: 注册系统服务并开机启动
::
set ACTION=%1
set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%

IF "%ACTION%"=="register" (
    set EXE_FILE=%SCRIPT_PATH%printer-start.bat
    sc "\\%USERDOMAIN%" create nop-printer binPath= "%SCRIPT_PATH%printer-start.bat" start= delayed-auto error= ignore
    exit 0
)

IF "%ACTION%"=="unregister" (
    sc delete nop-printer
    exit 0
)

echo Usage : system-service register
echo/
echo system-service.bat register
echo OR
echo system-service.bat unregister 
echo/
