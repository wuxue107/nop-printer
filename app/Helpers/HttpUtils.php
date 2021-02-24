<?php
namespace App\Helpers;


class HttpUtils {

    /**
     * 使用curl扩展请求url
     *
     * @param $url
     * @param callable|null $callback
     * @param bool|false $https
     * @return mixed
     */
    static function httpGet($url,$callback = null,$https=false)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        if(is_callable($callback)){
            $callback($ch);
        }

        $file_contents = curl_exec($ch);
        if (!empty($_GET['dump'])) {
            $error = curl_errno($ch).','.curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            echo <<<HTML
<pre>
    URL: $url
    STATUS : $httpCode
    EORROR : $error
    RESPONSE:$file_contents
</pre>
HTML;
        }

        if(false === $file_contents){
            $file_contents = 'CURL:' .curl_errno($ch) .':'. curl_error($ch);
        }
        curl_close($ch);

        return $file_contents;
    }

    /**
     * @param        $url
     * @param string $post_data
     * @param string $data_type
     * @param null   $callback
     * @param bool   $https
     *
     * @return bool|string
     * @throws \Exception
     */
    static function httpPost($url, $post_data = '',$data_type = 'form',$callback = null, $https = false){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if($data_type == 'json'){
            if(is_array($post_data)) $post_data = json_encode($post_data,JSON_UNESCAPED_UNICODE);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($post_data)
                )
            );
        }else if(!is_array($post_data)){
            $post_data = (string) $post_data;
        }else{
            // 不支持多维数组
            $needToString = false;
            foreach ($post_data as $item){
                if(is_array($item)){
                    $needToString = true;
                    break;
                }
            }
            if($needToString){
                $post_data = http_build_query($post_data);
            }
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        if($https){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }

        if(is_callable($callback)){
            $callback($ch);
        }

        $file_contents = curl_exec($ch);
        if (!empty($_GET['dump'])) {
            $error = curl_errno($ch).','.curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $param = is_string($post_data)?$post_data:var_export($post_data,true);
            $log = <<<HTML
<pre>
    URL: $url
    STATUS : $httpCode
    EORROR : $error
    PARAM  : $param
    RESPONSE:$file_contents
</pre>
HTML;
            Helper::writeLog($log, 'debug.log');
        }

        if(false === $file_contents){
            throw new \Exception('CURL:' .curl_errno($ch) .':'. curl_error($ch));
        }

        curl_close($ch);
        return $file_contents;
    }

    static function httpPostApi($url,$post_data,$data_type = 'form',$callback = null,$https = false){
        $content = self::httpPost($url,$post_data,$data_type,$callback,$https);
        return @json_decode($content,true);
    }

    static function httpGetApi($url,$callback = null, $https = false){
        $content = self::httpGet($url,$callback, $https);
        return @json_decode($content,true);
    }

    static function httpPostJson($url,$post_data,$callback,$https = false){
        $responseText = self::httpPost($url,$post_data,'json',$callback,$https);
        return $responseText;
    }

    static function httpPostJsonApi($url,$post_data,$callback = null,$https = false){
        $responseText = self::httpPostJson($url,$post_data ,$callback,$https);
        return @json_decode($responseText,true);
    }

    /**
     * 向url追加额外query参数
     * @param $url
     * @param $params
     * @return string
     */
    public static function setUrlParams($params = [],$url = null){
        if(is_null($url)){
            $url = @$_SERVER["REQUEST_URI"];
        }

        if(empty($params)){
        	return $url;
        }
        $segments = explode('?',$url,2);
        $queryParams = [];
        if(!empty($segments[1])){
            parse_str($segments[1],$queryParams);
        }
        $queryParams = array_merge($queryParams,$params);

        return $segments[0] .'?'. http_build_query($queryParams);
    }

    public function getUrlParams($url){
        $urlInfo = parse_url($url);
        if(!isset($urlInfo['query'])){
            return [];
        }
        parse_str($urlInfo['query'],$params);
        return $params;
    }

    static function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        switch($method)
        {
            case 'refresh'	: @header("Refresh:0;url=".$uri);
                break;
            default			: @header("Location: ".$uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }

}
