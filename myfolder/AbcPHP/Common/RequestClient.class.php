<?php
/**
 * php curl http 传输文件和数据
 * @author gaoruihua
 * @since 2012-05-23
 *
 */
class RequestClient
{
    const GET = 'GET';
    const POST = 'POST';

    public static $httpCode;
    public static $httpInfo;
    public static $httpErrorCode;
    public static $httpError;
    /**
     * curl request
     * @param string $url
     * @param string $method GET | POST
     * @param array|string $queryData 请求参数
     * @param int $port  端口
     * @param array $params curl 参数
     * @return string 请求结果string
     */
    public static function request($url, $method = 'GET',$queryData = array(), $port = 80, $params = array())
    {
    	$method = strtoupper($method);
    	if ($method == self::GET) {
    		$url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($queryData) ? http_build_query($queryData) : $queryData);
    	}
    	
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, isset($params['timeout']) ? $params['timeout'] : 3);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, isset($params['connect_timeout']) ? $params['connect_timeout'] : 3);
        //获取的信息以文件流的形式返回,不直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if($method == self::POST) {
            curl_setopt($curl, CURLOPT_POST, 1);
            if(!empty($queryData)) {
            	if (is_array($queryData)) {
            		$queryData = http_build_query($queryData);
            	}
            	curl_setopt($curl, CURLOPT_POSTFIELDS, $queryData);
            }
        }
        if(!empty($port)) {
            curl_setopt($curl, CURLOPT_PORT, $port);
        }
        if (isset($params['headers'])) {
        	curl_setopt($curl, CURLOPT_HTTPHEADER, $params['headers']);
        }

        if(isset($params['cookiefile']) && !empty($params['cookiefile'])) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, $params['cookiefile']);
        }
        if(isset($params['cookiejar']) && !empty($params['cookiejar'])) {
            curl_setopt($curl, CURLOPT_COOKIEJAR, $params['cookiejar']);
        }
        if(isset($params['return_header']) && !empty($params['return_header'])) {
            curl_setopt($curl, CURLOPT_HEADER, $params['return_header']);
        }
        if(isset($params['nobody']) && !empty($params['nobody'])) {
            curl_setopt($curl, CURLOPT_NOBODY, true);
        }
        if(isset($params['ssl_verify']) && !empty($params['ssl_verify'])) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        }
        if (isset($params['referer']) && !empty($params['referer'])) {
            curl_setopt( $curl, CURLOPT_REFERER, $params['referer']);
        }
        $response = curl_exec($curl);
        self::$httpErrorCode = curl_errno($curl);
        self::$httpError = curl_error($curl);
        self::$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        self::$httpInfo = curl_getinfo($curl);
        curl_close($curl);
        return $response;
    }
}
