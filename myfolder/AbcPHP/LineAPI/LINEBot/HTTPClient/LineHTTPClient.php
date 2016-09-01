<?php
/**
 * 微信api Request
 *
 * php curl http 传输文件和数据
 * 需要安装curl扩展
 *
 * @author hezq
 */

namespace LINE\LINEBot\HTTPClient;

use LINE\LINEBot;
use LINE\LINEBot\DownloadedContents;
use LINE\LINEBot\Exception\ContentsDownloadingFailedException;
//use LINE\LINEBot\Exception\JSONEncodingException;
use LINE\LINEBot\Exception\LINEBotAPIException;

class LineHTTPClient implements HTTPClient
{
	const GET  = 'GET';
    const POST = 'POST';
    private $channelId;
    private $channelSecret;
    private $channelMid;
    
    /**
     * Contains the last HTTP status code returned.
     *
     * @ignore
     */
    public static $http_code;

    /**
     * Contains the last HTTP headers returned.
     *
     * @ignore
     */
    public static $http_info;

    /**
     * http error code
     * @ignore
     */
    public static $http_error_code = 0;

    /**
     * http error
     * @ignore
     */
    public static $http_error = '';

    /**
     * Contains the last HTTP response.
     *
     * @ignore
     */
    public static $response;

    /**
     * Contains the last API call.
     *
     * @ignore
     */
    public static $url;

    /**
     * Contains the last HTTP params.
     *
     * @ignore
     */
    public static $params;

    /**
     * Set timeout default.
     *
     * @ignore
     */
    public static $timeout = 30;

    /**
     * Set connect timeout.
     *
     * @ignore
     */
    public static $connecttimeout = 30;

    /**
     * Verify SSL Cert.
     *
     * @ignore
     */
    public static $ssl_verifypeer = false;

    /**
     * Respons format.
     *
     * @ignore
     */
    public static $format = 'json';

    /**
     * Decode returned json data.
     *
     * @ignore
     */
    public static $decode_json = TRUE;

    /**
     * Set the useragnet.
     *
     * @ignore
     */
    public static $useragent = 'LineApi SDK v0.1';

    /**
     * print the debug info
     *
     * @ignore
     */
    public static $debug = true;

    /**
     * boundary of multipart
     * @ignore
     */
    public static $boundary = '';

    /**
     * Client constructor.
     *
     * @param array $args Parameters of bot and client.
     * You can also control {@link https://github.com/guzzle/guzzle guzzle} parameter through this argument.
     */
    public function __construct(array $args)
    {
    	$guzzleConfig = isset($args['httpClientConfig']) ? $args['httpClientConfig'] : [];
    
    	if (!isset($guzzleConfig['headers']['User-Agent'])) {
    		$guzzleConfig['headers']['User-Agent'] = 'LINE-BotSDK/' . LINEBot::VERSION;
    	}
    
    	if (!isset($guzzleConfig['timeout'])) {
    		$guzzleConfig['timeout'] = 3;
    	}
    
    	$this->channelId = $args['channelId'];
    	$this->channelSecret = $args['channelSecret'];
    	$this->channelMid = $args['channelMid'];
    }
    /**
     * GET wrappwer for apiRequest.
     *
     * @return mixed
     */
    public function get($url)
    {
        $response = $this->_request($url, self::GET, []);
        $response = json_decode($response, true);
        if ($response === null) {
        	throw new LINEBotAPIException("LINE BOT API error: ".self::$http_code);
        }
        return $response;
    }
    
    /**
     * POST wreapper for apiRequest.
     *
     * @return mixed
     */
    public function post($url, array $data)
    {
    	$timeout = 0;
		
        $response = $this->_request($url, self::POST, $data, $timeout);
        if (!$response || !preg_match('/\A{.+}\z/u', $response)) {
        	throw new LINEBotAPIException("LINE BOT API error: ".self::$http_code);
        }
        
        $response = json_decode($response, true);
        if ($response === null) {
        	throw new LINEBotAPIException("LINE BOT API error: ".self::$http_code);
        }
        return $response;
    }
    
    /**
     * Download contents.
     *
     * @param string $url Contents URL.
     * @param resource $fileHandler File handler to store contents temporally.
     * @return DownloadedContents
     * @throws ContentsDownloadingFailedException When failed to download contents.
     */
    public function downloadContents($url, $fileHandler = null)
    {
    	Logger::info("todo");
    }

    /**
     * Format and sign an API request
     *
     * @return string
     * @ignore
     */
    private function _request($url, $method, $params, $multi = false, $timeout = 0)
    {
        self::$url = $url;
        self::$params = $params;
        $headers = $this->credentials();
        if (self::GET == $method) {
            $url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
            $response = self::_http($url, self::GET,null,$headers);
        } else {
        	$json = json_encode($params);
        	$headers = array_merge($headers, [
        			'Content-Type:application/json; charset=UTF-8',
        			'Content-Length:' . strlen($json),
        			]);
            if (! $multi ) {
                //$body = http_build_query($params);
                $body = $params;
            } else {
            	//TODO:目前没有使用
                $body = self::_buildHttpQueryMulti($params);
                $headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
            }            
            $response = self::_http($url, self::POST, $json, $headers, $timeout);
        }
		return $response;
        //return self::_parseResponse($response);
    }

    /**
     * 解析结果集
     *
     * 把message_id原数字用正则转换为字符串
     *
     * @author mxg
     * @param
     */
    private static function _parseResponse($response)
    {
        return preg_replace('/\"message_id\":( )*([\d]{13,})/', "\"message_id\": \"$2\"", $response);
    }

    /**
     * Make an HTTP request
     *
     * @return string API results
     * @ignore
     */
    private static function _http($url, $method, $postfields = NULL, $headers = array(), $timeout = 0)
    {
        if (! self::test()) {
            echo '您的服务器不支持 PHP 的 Curl 模块，请安装或与服务器管理员联系。';
            exit;
        }
		
		$timeout = $timeout ? $timeout : self::$timeout;
        self::$http_info = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, self::$useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, self::$connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");

        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, self::$ssl_verifypeer);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, (self::$ssl_verifypeer == true) ? 2 : false);

//         curl_setopt($ci, CURLOPT_HEADERFUNCTION, 'LineHTTPClient::_getHeader');
        
        if (self::POST == $method) {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                //self::$postdata = $postfields;
            }
        }
        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
        self::$response = $response = curl_exec($ci);
        self::$http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        self::$http_info = array_merge(self::$http_info, curl_getinfo($ci));
        self::$http_error_code = curl_errno($ci);
        self::$http_error = curl_error($ci);

        if (self::$debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);

            echo '=====info====='."\r\n";
            print_r( curl_getinfo($ci) );

            echo '=====$response====='."\r\n";
            print_r( $response );

            echo '=====error====='."\r\n";
            print_r( curl_error($ci) );
        }
        curl_close($ci);
        return $response;
    }

    /**
     * Get the header info to store.
     *
     * @return int
     * @ignore
     */
    private static function _getHeader($ch, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            //self::$http_header[$key] = $value;
        }
        return strlen($header);
    }

    /**
     * @ignore
     */
    private static function _buildHttpQueryMulti($params)
    {
        if (!$params) return '';

        uksort($params, 'strcmp');

        $pairs = array();

        self::$boundary = $boundary = uniqid('----------');

        $MPboundary = '--'.$boundary;
        $endMPboundary = $MPboundary. '--';
        $multipartbody = '';

        foreach ($params as $param => $value) {

            if( 'media' == $param && $value{0} == '@' ) {
                $url = ltrim( $value, '@' );
                $content = file_get_contents( $url );
                $array = explode( '?', basename( $url ) );
                $filename = $array[0];

                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'Content-Disposition: form-data; name="' . $param . '"; filename="' . $filename . '"'. "\r\n";
                $multipartbody .= "Content-Type: application/octet-stream\r\n\r\n";
                $multipartbody .= $content. "\r\n";
            } else {
                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'Content-Disposition: form-data; name="' . $param . "\"\r\n\r\n";
                $multipartbody .= $value."\r\n";
            }

        }

        $multipartbody .= $endMPboundary;
        return $multipartbody;
    }

    /**
     * Whether this class can be used for retrieving an URL.
     *
     * @static
     * @return boolean False means this class can not be used, true means it can.
     */
    public static function test()
    {
        if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) ) {
            return false;
        }
        $is_ssl = self::$ssl_verifypeer;

        if ( $is_ssl ) {
            $curl_version = curl_version();
            if ( ! (CURL_VERSION_SSL & $curl_version['features']) ) {// Does this cURL version support SSL requests?
                return false;
            }
        }
        return true;
    }
    
    private function credentials()
    {
    	return [
    	'X-Line-ChannelID:'.$this->channelId,
    	'X-Line-ChannelSecret:' . $this->channelSecret,
    	'X-Line-Trusted-User-With-ACL:' . $this->channelMid,
    	];
    }

}