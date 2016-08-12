<?php
/**
 * 微信poi
 *
 */
include_once ABC_PHP_PATH . '/API/WeiXinError.class.php';
include_once ABC_PHP_PATH . '/API/WeiXinApiRequest.class.php';
include_once ABC_PHP_PATH . '/Common/Json.class.php';

class WeiXinPoiApi
{
	/**
	 * appid
	 * @var string
	 */
	protected $appId = '';
	/**
	 * app secret
	 * @var string
	 */
	protected $appSecret = '';
	/**
	 * access token
	 * @var string
	 */
	protected $accessToken = '';
	/**
	 * 微信用户openid
	 * @var string
	 */
	protected $openid = '';
	
	/**
	 * 最后错误代码
	 */
	protected $_error_code = 0;
	
	/**
	 * 最后错误信息
	 */
	protected $_error_message = '';
	/**
	 * api uri
	 * @var string
	 */
	protected $apiUri = 'https://api.weixin.qq.com/cgi-bin/';
	
	public function __construct($appId, $appSecret, $accessToken) {
		$this->appId = $appId;
		$this->appSecret = $appSecret;
		$this->setAccessToken($accessToken);
	}
	
	/**
	 * 获取错误code
	 * @author mxg
	 * @return int
	 */
	public function getErrorCode() {
		return $this->_error_code;
	}
	
	/**
	 * 获取错误信息
	 * @author mxg
	 * @return string
	 */
	public function getErrorMessage() {
		return $this->_error_message;
	}
	
	/**
	 * 设置access token
	 * @param string $token
	 */
	public function setAccessToken($token) {
		$this->accessToken = $token;
	}
	
	public function getPoi ($poiId) {
		$url = $this->_getUrl('poi/getpoi');
		$param = array('poi_id' => $poiId);
		$response = WeiXinApiRequest::post($url, $param);
		return call_user_func_array(array($this, '_parse'),
				array(WeiXinApiRequest::$http_code, $response, '_parsePoi'));
	}
	
	//获取门店列表
	public function getPoiList ($begin = 0, $limit = 10) {
		$url = $this->_getUrl('poi/getpoilist');
		$param = array('begin'=>$begin, 'limit' => $limit);
		$response = WeiXinApiRequest::post($url, $param);
		return call_user_func_array(array($this, '_parse'),
				array(WeiXinApiRequest::$http_code, $response, '_parsePoiList'));
	}
        
        /**
         * 添加门店信息
         * @param $location_arr
         * @return mixed|null
         */
        public function createStore($location_arr) {
            $param = array('business' => array('base_info' => $location_arr));
            $url = $this->_getUrl('poi/addpoi');
            //WeiXinApiRequest::$debug = 1;
            $json = new Json();
            $param = $json->encode($param, false);
            $response = WeiXinApiRequest::post($url, $param);
            Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
            return call_user_func_array(array($this, '_parse'),
                array(WeiXinApiRequest::$http_code, $response, '_parseCreateStore'));
        }
        
            
        /**
         * 获取门店分类
         * @param $location_arr
         * @return mixed|null
         */
        public function getStoreCategory() {
            $url = $this->_getUrl('cgi-bin/api_getwxcategory');
            $response = WeiXinApiRequest::get($url);
            Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);return false;
            return call_user_func_array(array($this, '_parse'),
                array(WeiXinApiRequest::$http_code, $response, '_parseGetStoreCategory'));
        }
	
	protected function _parsePoi ($response) {
		if (!isset($response['errcode']) || !isset($response['errmsg'])) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return false;
		}
		return @$response['business'];
	}
	
	protected function _parsePoiList ($response) {
		if (!isset($response['errcode']) || !isset($response['errmsg'])) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return false;
		}
		return array(
			'business_list' => @$response['business_list'],
			'total_count' => (int)$response['total_count']
		);
	}
	
	/**
	 * 记录错误日志
	 *
	 * @author mxg
	 * @return void
	 */
	protected function _log() {
		$params = array();
		$params['data'] = WeiXinApiRequest::$params;
		$params['response'] = WeiXinApiRequest::$response;
		$params['app_id'] = $this->appId;
		$params['http_code'] = WeiXinApiRequest::$http_code;
		Logger::error('WeiXinPoiApi last erorr, url: ' . WeiXinApiRequest::$url, $params);
	}
	
	/**
	 * 获取api请求错误
	 *
	 * @param  int $code
	 * @return void
	 */
	protected function _setError($code, $log_enabled = true) {
		//记录错误日志
		if ($log_enabled) $this->_log();
	
		$this->_error_code = $code;
		$this->_error_message = WX_Error::getMessage($code);
	}
	
	/**
	 * 获取api请求url
	 * @param 请求模块路径 $path
	 * @param 参数 $params
	 * @return string
	 */
	protected function _getUrl($path, $params = array()) {
		$path = trim(trim($path), '/');
		$url = $this->apiUri . $path . '?access_token=' . $this->accessToken;;
		//echo $url;
		if ($params) {
			$url .= '?' . (is_array($params) ? http_build_query($params) : ltrim($params, '?'));
		}
		return $url;
	}
	
	/**
	 * 清楚token缓存陈胖
	 */
	protected function clearTokenCache() {
		if (function_exists("clearToken") && function_exists("getToken")) {
			$oldToken = getToken($this->appId, $this->appSecret, false);
			if ($oldToken && $oldToken != $this->accessToken) {
				Logger::debug(__METHOD__.' clear token 时使用的token与cache中不同无需清除 cacheToken:'
						.$oldToken.'  usedToken:'.$this->accessToken);
						return;
			}
			$c1 = clearToken($this->appId);
			$c2 = true;
			if (function_exists("clearAuthorizerAccessToken")) {
				$c2 = clearAuthorizerAccessToken("COUPON", $this->appId);
			}
			if (!$c1 || !$c2) {
				Logger::error('WeiXinPoiApi clear token cache fail : clearToken:' . (int)$c1
				. "  clearAuthorizerAccessToken:" . (int)$c2);
			}
		}
	}
	/**
	 * 分析数据结果
	 *
	 * @param int $code curl发送请求的状态码
	 * @param array $response 得到的结果
	 * @param string $fun_name
	 * @return mixed
	 */
	protected function _parse($code, $response, $func_name = '') {
		$error = $this->_parseError($code, $response);
		if ($error == false) {
			return false;
		}
		if ($func_name) {
			return call_user_func_array(array($this, $func_name), array($response));
		}
		return true;
	}
	/**
	 * 分析错误
	 *
	 * @param  int $code
	 * @param  array $response
	 * @return bool
	 */
	protected function _parseError($code, $response) {
		$this->_error_code = WX_Error::NO_ERROR;
		if (200 == $code && (isset($response['errcode']) && $response['errcode'])) {
			$code = $response['errcode'];
		}
		//与微信链接失败
		if (0 == $code) {
			$code = 5100;
		}
	
		switch ($code) {
			case 200:
				return true;
				//http code
			case 404:
				$error_code = WX_Error::HTTP_FORBIDDEN_ERROR;
				break;
			case 503:
				$error_code = WX_Error::HTTP_SERVICE_UNAVAILABLE_ERROR;
				break;
			case 40013:
				$error_code = WX_Error::INVALID_APP_ID_ERROR;
				break;
			case 41001:
				$error_code = WX_Error::KOTEN_MISSING_ERROR;
				break;
			case 40073: //invalid card id
				$error_code = WX_Error::INVALID_CARD_ID;
				break;
			case 40056: //invalid serial code
				$error_code = WX_Error::INVALID_CARD_CODE;
				break;
			case 40001 :
				$error_code = WX_Error::INVALID_CREDENTIAL_ERROR;
				$this->clearTokenCache();
				break;
			case 40003 :
				$error_code = WX_Error::INVALID_USER_ERROR;
				break;
			case 42001 :
				$error_code = WX_Error::TOKEN_EXPIRED_ERROR;
				$this->clearTokenCache();
				break;
			case 40079:
				$error_code = WX_Error::INVALID_CARD_TIME;
			default:
				$error_code = $code;
		}
		$this->_setError($error_code);
		return false;
	}
        
        /**
         * 分析导入门店信息结果
         * @author huqian
         * @param  array $response
         * @return string|null $card_id
         */
        protected function _parseCreateStore($response) {
            if (!isset($response['errcode']) || !isset($response['errmsg'])) {
                $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
                return false;
            }
            if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
                return true;
            }else{
                return -1;
            }
            return false;
        }        
            
        /**
         * 获取颜色列表结果
         * @param $response
         * @return bool
         */
        protected function _parseGetStoreCategory($response) {
            if (!isset($response['errcode']) || !isset($response['errmsg'])) {
                $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
                return false;
            }
            if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
                return $response;
            }
            return false;
        }
}