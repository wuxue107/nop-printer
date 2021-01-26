<?php

namespace App\Helpers;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2017/10/28
 * Time: 0:18
 */
class Helper
{

    const CODE_SUCCESS = 0;
    const CODE_FAILED_DEFAULT = 90000;
    const CODE_INVALID_RESPONSE = 90001;

    static function isSuccessApiMsg($apiMsg)
    {
        return is_array($apiMsg) && isset($apiMsg['code']) && $apiMsg['code'] == self::CODE_SUCCESS;
    }

    static function isFailApiMsg($apiMsg)
    {
        return !is_array($apiMsg) || !isset($apiMsg['code']) || $apiMsg['code'] != self::CODE_SUCCESS;
    }

    static function apiMsg($code = self::CODE_SUCCESS, $msg = '', $data = null)
    {
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
    }

    static function successMsg($data = null, $msg = '操作成功', $code = self::CODE_SUCCESS)
    {
        return self::apiMsg($code, $msg, $data);
    }

    static function failMsg($msg = '操作失败', $code = self::CODE_FAILED_DEFAULT, $data = null)
    {
        if($code == 0){
            $code = self::CODE_FAILED_DEFAULT;
        }
        return self::apiMsg($code, $msg, $data);
    }

    static function success($data = null, $msg = '操作成功', $code = self::CODE_SUCCESS)
    {
        return self::responseMsg(self::successMsg($data, $msg, $code));
    }

    static function fail($msg = '操作失败', $code = self::CODE_FAILED_DEFAULT, $data = null)
    {
        return self::responseMsg(self::failMsg($msg, $code, $data));
    }

    static function msg($code = self::CODE_SUCCESS, $msg = '操作成功',$data = null){
        return self::responseMsg(self::apiMsg($code, $msg ,$data));
    }

    /**
     * @param array $apiMsg
     *
     * @return null
     */
    static function responseMsg($apiMsg)
    {
        if(is_scalar($apiMsg['data'])){
            $apiMsg = Helper::failMsg("response: data must be is array or object",self::CODE_INVALID_RESPONSE);
        }

        if(!is_null($apiMsg['data']) && !($apiMsg['data'] instanceof \ArrayObject)){
            $apiMsg['data'] = new \ArrayObject($apiMsg['data']);
        }

        return response()->json($apiMsg);
    }

    public static function throwableMsg(\Throwable $e){
        /** @var \Exception $e * */
        $data = null;

        $isDebug = env('APP_DEBUG');
        if($isDebug){
            $previous = $e->getPrevious();
            $data['throwable'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'previous' => is_null($previous)?null:self::throwableMsg($previous),
            ];
        }

        $message =  $isDebug ? $e->getMessage() : "服务器内部错误";
        return self::failMsg($message, $e->getCode() , $data);
    }

    public static function throwable(\Throwable $e){
        return self::responseMsg(Helper::throwableMsg($e));
    }


    public static function responseToLog($file = 'debug.log'){
        ob_start(function($c) use($file){
            Helper::writeLog(['url' => $_SERVER['REQUEST_URI']??'','post' => $_POST,'get' => $_GET,'response' => $c],$file);
            return $c;
        });
    }


    /**
     * 用于调试程序,记录某些变量的数据
     */
    public static function writeLog($data, $file = 'debug.log')
    {
        if (is_string($data)) {
            $msg = $data;
        }elseif (is_object($data)) {
            if($data instanceof \Throwable){
                $msg = self::formatException($data);
            }else{
                $msg = "PRINT_R:".print_r($data, true);
            }

        }else {
            $msg = "JSON:". json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }
        $msg = date('Y-m-d H:i:s').':'.$msg.PHP_EOL;

        self::writeRuntimeFile($file,$msg,FILE_APPEND);
    }


    public static function getRuntimePath($file = ''){
        return storage_path('runtime/' . $file);
    }

    public static function writeRuntimeFile($file,$data,$flags = 0){
        $file = self::getRuntimePath( $file);
        $dir =  dirname($file);
        if(!is_dir($dir)){
            if(!mkdir($dir,0777,true)){
                throw new \Exception("创建目录失败：$dir");
            }
        }

        return file_put_contents($file,$data,$flags);
    }

    public static function formatException(\Throwable $e, $trace = true)
    {
        $message =  'CODE:'.$e->getCode().',MESSAGE:'.$e->getMessage();

        if(env("APP_DEBUG")){
            $message .=  PHP_EOL .'FILE:'.$e->getFile().PHP_EOL
                .'LINE:'.$e->getLine().PHP_EOL
                .($trace ? 'TRACE:'.$e->getTraceAsString().PHP_EOL : '');
        }

        return $message;
    }
}
