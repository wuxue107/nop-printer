@echo off

::
:: stop the api service
:: 停止打印机API服务
::

set SCRIPT_PATH=%~dp0
cd %SCRIPT_PATH%
set PHP_PATH=%SCRIPT_PATH%archive\php7.3.4nts

%PHP_PATH%\php -r "$content = shell_exec('netstat -ano');$lines = explode(chr(10),$content);$match = array_filter($lines,function($line){return strpos($line,':8077') > 0;});$info = trim(empty($match)?'':array_values($match)[0]);$fields = preg_split('/\s+/',$info);$pid = intval($fields[4]??'');if($pid !== 0){ system('taskkill /f /pid ' . $pid);}else{echo 'service already stop';}";