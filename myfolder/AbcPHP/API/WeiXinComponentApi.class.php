<?php
/**
 * PHP SDK for weixin componect
 *
 * @author mxg
 * @version 1.0
 */
include_once ABC_PHP_PATH . '/API/WeiXinError.class.php';
include_once ABC_PHP_PATH . '/API/WeiXinApiRequest.class.php';
include_once ABC_PHP_PATH . '/Common/Json.class.php';
/**
 * 微信组件操作类
 *
 *
 * @author mxg
 * @version 2.0
 */
class WeiXinComponectApi
{

	/**
     * 组件APP ID
     */
	public $componect_app_id;

	/**
     * 组件APP SECRET
     */
	public $componect_app_secret;

	/**
	 * 组件的ticket,微信会定时发送到组件通知地址
	 */
	public $component_verify_ticket;
	/**
     * 组件TOKEN
     */
	public $token;

	/**
     * 微信api平台host地址
     */
	public $host = "https://api.weixin.qq.com/cgi-bin/component/";

	/**
     * 最后错误代码
     */
	protected $_error_code = 0;

	/**
     * 最后错误信息
     */
	protected $_error_message = '';

	/**
     * Set get token API URLS
     */
	public function accessTokenURL()
	{
		return $this->host . 'api_component_token';
	}

	/**
     * 构造函数
     *
     * @access public
     * @param mixed $componect_app_id 微信平台组件APP KEY
     * @param mixed $componect_app_secret SECRET
     * @param mixed $access_token 微信平台放回的token
     * @return void
     */
	public function __construct($componect_app_id, $componect_app_secret,$component_verify_ticket, $token = NULL)
	{
		$this->componect_app_id = $componect_app_id;
		$this->componect_app_secret = $componect_app_secret;
		$this->component_verify_ticket = $component_verify_ticket;
		$this->token = $token;
	}

	/**
     * 获取错误code
     *
     * function_description
     *
     * @author mxg
     * @return int
     */
	public function getErrorCode()
	{
		return $this->_error_code;
	}

	/**
     * 获取错误信息
     *
     * function_description
     *
     * @author mxg
     * @return string
     */
	public function getErrorMessage()
	{
		return $this->_error_message;
	}

	/**
     * 获取token
     *
     * 对应API：{@link https://api.weixin.qq.com /cgi-bin/component/api_component_token}
     *
     * @access public
     * @return object WX_Token
     */
	public function getToken()
	{
		$params = array ();
		$params['component_appid'] = $this->componect_app_id;
		$params['component_appsecret'] = $this->componect_app_secret;
		$params['component_verify_ticket'] = str_replace('ticket@@@', '', $this->component_verify_ticket);
		$response = WeiXinApiRequest::post($this->accessTokenURL(), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseToken'
		));
	}
    /**
     * 获取公众号授权token
     *
     * 对应API：{@link https:// api.weixin.qq.com /cgi-bin/component/api_authorizer_token?component_access_token=xxxxx}
     *
     * @access public
     * @return WX_Token
     */
	public function getAuthorizerAccessToken($authorizerAppid,$authorizerRefreshToken)
	{
		$params = array ();
		$params['component_appid'] = $this->componect_app_id;
		$params['authorizer_appid'] = $authorizerAppid;
		$params['authorizer_refresh_token'] = str_replace('refreshtoken@@@', '', $authorizerRefreshToken);
		//$params['authorizer_refresh_token'] = $authorizerRefreshToken;
        $url = $this->host.'api_authorizer_token?component_access_token='.$this->token;
		$response = WeiXinApiRequest::post($url, $params);
		Logger::debug(__METHOD__." api_authorizer_token params:".json_encode($params)." response info:".WeiXinApiRequest::$response);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseAuthorizerAccessToken'
		));
	}

	/**
     * 设置token
     *
     * function_description
     *
     * @author mxg
     * @param  string $token
     * @return void
     */
	public function setToken($token)
	{
		$this->token = $token;
	}

	/**
	 * 获取预授权码
	 *
	 * 对应API：{@link https://api.weixin.qq.com /cgi-bin/component/api_create_preauthcode?component_access_token=xxx}
	 *
	 * @access public
	 * @return object WX_Token
	 */
	public function getPreAuthCode($token='')
	{
		$params = array ();
		$params['component_appid'] = $this->componect_app_id;
                  if($token){
                        $this->token = $token;
                  }
		$path = 'api_create_preauthcode';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parsPreAuthCode'
		));
	}
	
	
	
	/**
     * 使用授权码换取公众号的授权信息
     *
     * 对应API：{@link https://api.weixin.qq.com/cgi-bin/component/api_query_auth}
     *
     * @author mxg
     * @param  string $user_id 微信用户ID
     * @return object WX_User
     */
	public function getQueryAuth($authorization_code)
	{
		$params = array ();
		$params['component_appid'] = $this->componect_app_id;
		$params['authorization_code'] = str_replace('queryauthcode@@@', '', $authorization_code);
		$path = 'api_query_auth';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
        Logger::debug(__METHOD__." api_query_auth  params:".json_encode($params)."  response info:".WeiXinApiRequest::$response);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseQueryAuth'
		));
	}

	


	/**
	 * 获取（刷新）授权公众号的令牌
	 *
	 * 对应API：{@link https:// api.weixin.qq.com /cgi-bin/component/api_authorizertoken}
	 *
	 * @author mxg
	 * @param  string $user_id 微信用户ID
	 * @return object WX_User
	 */
	public function flushAuthorizationToken($authorization_api,$authorizer_refresh_token)
	{
		$params = array ();
		$params['component_appid'] = $this->componect_app_id;
		$params['authorization_api'] = $authorization_api;
		$params['authorizer_refresh_token'] = str_replace('refreshtoken@@@', '',$authorizer_refresh_token);
		$path = 'api_authorizertoken';
		$response = WeiXinApiRequest::GET($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseFlushAuthorizationToken'
		));
	}
	
	
	/**
	 * 获取授权公众号些信息
	 *
	 * 对应API：{@link https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info}
	 *
	 * @author mxg
	 * @param  string $user_id 微信用户ID
	 * @return object WX_User
	 */
	public function getAuthorizationInfo($authorizer_appid)
	{
		$params = array ();
		$params['component_appid'] = $this->componect_app_id;
		$params['authorizer_appid'] = $authorizer_appid;
		$path = 'api_get_authorizer_info';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
        Logger::debug(__METHOD__." params:" .json_encode($params)."  response info:".WeiXinApiRequest::$response);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseGetAuthorizationInfo'
		));
	}
	
	/**
	 * 获取授权方的选项设置信息
	 *
	 * 对应API：{@link https://api.weixin.qq.com/cgi-bin/component/ api_get_authorizer_option}
	 *
	 * @author mxg
	 * @param  string $authorization_api 授权方appid
	 * @param  string $option_name 操作key 目前支持以下几种
	 * 		option_name		option_value	选项值说明
			location_report	 	 	0	无上报
			(地理位置上报选项)		1	进入会话时上报
									2	每5s上报
	
			voice_recognize			0	关闭语音识别
			（语音识别开关选项）	1	开启语音识别
			customer_service		0	关闭多客服
			（客服开关选项）		1	开启多客服
	 * @return object WX_User
	 */
	public function getAuthorizationOption($authorization_api,$option_name)
	{
		$params = array ();
		$params['component_appid'] = $this->componect_app_id;
		$params['authorizer_appid'] = $authorization_api;
		$params['option_name'] = $option_name;
		$path = 'api_get_authorizer_option';
		$response = WeiXinApiRequest::GET($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseGetAuthorizationOption'
		));
	}
	
	/**
	 * 获取授权方的选项设置信息
	 *
	 * 对应API：{@link https://api.weixin.qq.com/cgi-bin/component/ api_set_authorizer_option}
	 *
	 * @author mxg
	 * @param  string $authorization_api 授权方appid
	 * @param  string $option_name 操作key 目前支持以下几种
	 * 		option_name		option_value	选项值说明
	 location_report	 	 	0	无上报
	 (地理位置上报选项)		1	进入会话时上报
	 2	每5s上报
	
	 voice_recognize			0	关闭语音识别
	 （语音识别开关选项）	1	开启语音识别
	 customer_service		0	关闭多客服
	 （客服开关选项）		1	开启多客服
	 * @return object WX_User
	 */
	public function setAuthorizationOption($authorization_api,$option_name,$option_value)
	{
		$params = array ();
		$params['component_appid'] = $this->componect_app_id;
		$params['authorization_api'] = $authorization_api;
		$params['option_name'] = $option_name;
		$params['option_value'] = $option_value;
		$path = 'api_set_authorizer_option';
		$response = WeiXinApiRequest::GET($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseSetAuthorizationOption'
		));
	}
	
	/**
	 * 母商户资质申请
	 * @param unknown $register_capital  注册资本，数字，单位：分
	 * @param unknown $business_license_media_id 营业执照扫描件的media_id
	 * @param unknown $tax_registration_certificate_media_id	税务登记证扫描件的media_id
	 * @param unknown $last_quarter_tax_listing_media_id	上个季度纳税清单扫描件media_id
	 * @return mixed
	 */
	public function uploadCardAgentQualification($register_capital,$business_license_media_id,
			$tax_registration_certificate_media_id,$last_quarter_tax_listing_media_id)
	{
		$params = array ();
		$params['register_capital'] = $register_capital;
		$params['business_license_media_id'] = $business_license_media_id;
		$params['tax_registration_certificate_media_id'] = $tax_registration_certificate_media_id;
		$params['last_quarter_tax_listing_media_id'] = $last_quarter_tax_listing_media_id;
		$path = 'upload_card_agent_qualification';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseBase'
		));
	}
	
	/**
	 * 母商户资质查询
	 * @return mixed
	 */
	public function checkCardAgentQualification()
	{
		$params = array ();
		$path = 'check_card_agent_qualification';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseCheckStatus'
		));
	}
	
	/**
	 * 子商户户资质申请
	 * @param unknown $appid  appid
	 * @param unknown $name  子商户的商户名，显示在卡券券面的商户名称
	 * @param unknown $logo_media_id  子商户的logo，显示在卡券券面的商户logo
	 * @param unknown $business_license_media_id 营业执照或个体工商户执照扫描件的media_id
	 * @param unknown $agreement_file_media_id	子商户与第三方签署的代理授权函的media_id
	 * @param unknown $primary_category_id	一级类目id
	 * @param unknown $secondary_category_id	二级类目id
	 * @return mixed
	 */
	public function uploadCardMerchantQualification($appid,$name,$logo_media_id,
			$business_license_media_id,$agreement_file_media_id,
			$primary_category_id,$secondary_category_id,$operator_id_card_media_id="")
	{
		$params = array ();
		$params['appid'] = $appid;
		$params['name'] = $name;
		$params['logo_media_id'] = $logo_media_id;
		$params['business_license_media_id'] = $business_license_media_id;
		$params['agreement_file_media_id'] = $agreement_file_media_id;
		if($operator_id_card_media_id){
			$params['operator_id_card_media_id'] = $operator_id_card_media_id;
		}
		$params['primary_category_id'] = $primary_category_id;
		$params['secondary_category_id'] = $secondary_category_id;
		$path = 'upload_card_merchant_qualification';
		$json = new Json();
		$params = $json->encode($params, false);
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
		Logger::debug(__METHOD__." log: [url:[".$this->_genUrl($path)."]][params: [".WeiXinApiRequest::$params."]][response: [{$response}]]");
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseBase'
		));
	}
	
	/**
	 * 子商户资质查询
	 * @param appid  appid
	 * @return mixed
	 */
	public function checkCardmerchantQualification($appid)
	{
		$params = array ();
		$params['appid'] = $appid;
		$path = 'check_card_merchant_qualification';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseCheckStatus'
		));
	}
	
	/**
	 * 资料上传
	 * @return mixed
	 */
	public function uploadMedia($type,$attachment)
	{
		$params = array();
		$params['type'] = $type;
		$params['media'] = '@' . $attachment;
		$url="https://api.weixin.qq.com/cgi-bin/media/upload?type=$type&access_token=" . $this->token;
		$response = WeiXinApiRequest::post($url, $params, false, false);
		return call_user_func_array(array(
				$this,
				'_parse'
		), array(
				WeiXinApiRequest::$http_code,
				$response,
				'_parseUpload'
		));
		
	}
	
	/**
	 * 子商户信息接口
	 * @param appid  appid
	 * @return mixed
	 */
	public function getCardmerchant($appid)
	{
		$params = array ();
		$params['appid'] = $appid;
		$path = 'get_card_merchant';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseBase'
		));
	}
	
	/**
	 * 拉取子商户信息列表
	 * @param $next_get  下一页的标识，由上一页返回
	 * @return mixed
	 */
	public function getBatchCardmerchant($next_get)
	{
		$params = array ();
		$params['next_get'] = $next_get?$next_get:'';
		$path = 'batchget_card_merchant';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseBase'
		));
	}
	
	/**
	 * 获取卡券类目
	 * @return mixed
	 */
	public function getCardCategory () {
		$param = array();
		$url="https://api.weixin.qq.com/card/getapplyprotocol?access_token=" . $this->token;
		$response = WeiXinApiRequest::post($url, $param);
		return call_user_func_array(array($this, '_parse'),
				array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
	}
	
	/**
	 * 确认授权
	 * @param unknown $component_appid  第三方平台appid
	 * @param unknown $authorizer_appid	授权方appid
	 * @param unknown $funcscope_category_id	权限集ID
	 * @param unknown $confirm_value	是否确认。 （1.确认授权， 2，取消确认）
	 * @return mixed
	 */
	public function confirmApiAuthorization($component_appid,$authorizer_appid,
			$funcscope_category_id,$confirm_value)
	{
		$params = array ();
		$params['component_appid'] = $component_appid;
		$params['authorizer_appid'] = $authorizer_appid;
		$params['funcscope_category_id'] = $funcscope_category_id;
		$params['confirm_value'] = $confirm_value;
		$path = 'api_confirm_authorization';
		$response = WeiXinApiRequest::post($this->_genUrl($path), $params);
		return call_user_func_array(array (
				$this,
				'_parse'
		), array (
				WeiXinApiRequest::$http_code,
				$response,
				'_parseConfirmApiAuthorization'
		));
	}
	
	
	/**
     * 文本加密
     *
     * @param  string $text
     * @return string
     */
	protected function _textEncode($text)
	{
		if (! $text) return null;
		return urlencode($text);
	}

	
	/**
     * 生成api请求的URL
     *
     * @param  string $path
     * @param  array  $params 参数
     * @return string $url
     */
	protected function _genUrl($path, $params = array())
	{
		$path = trim($path);
		$length = strlen($path);
		if (0 === strpos($path, '/')) {
			$length -= 1;
			$path = substr($path, 1, $length);
		}
		if (($length - 1) === strrpos($path, '/')) {
			$path = substr($path, 0, $length - 1);
		}
		$host = $this->host;
		$url = $host . $path . '?component_access_token=' . $this->token;

		if ($params) {
			$url .= '&' . (is_array($params) ? http_build_query($params) : $params);
		}
		return $url;
	}

	/**
     * 分析数据结果
     *
     * @param int    $code   curl发送请求的状态码
     * @param array  $response 得到的结果
     * @param string $fun_name
     * @return mixed
     */
	protected function _parse($code, $response, $func_name = '')
	{
		$error = $this->_parseError($code, $response);
		if ($error == false) {
			return false;
		}
		if ($func_name) {
			return call_user_func_array(array (
					$this,
					$func_name
			), array (
					$response
			));
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
	protected function _parseError($code, $response)
	{
		/* var_dump($code);
        echo '<br>';
        var_dump(WeiXinApiRequest::$http_info);
        echo '<br>';
        var_dump(WeiXinApiRequest::$response);
        echo '<br>';
        var_dump($response);
        exit; */
		$this->_error_code = WX_Error::NO_ERROR;
		if (200 == $code && (isset($response['errcode']) && $response['errcode'])) {
			$code = $response['errcode'];
		}
		//与微信链接失败
		if (0 == $code) {
			$code = 5100;
		}

		switch ($code) {
			case 200 :
				return true;
			//http code
			case 404 :
				$error_code = WX_Error::HTTP_FORBIDDEN_ERROR;
				break;
			case 503 :
				$error_code = WX_Error::HTTP_SERVICE_UNAVAILABLE_ERROR;
				break;
			//response code
			case 40002 :
				$error_code = WX_Error::INVALID_GRANT_TYPE_ERROR;
				break;
			case 41002 :
				$error_code = WX_Error::APP_ID_MISSING_ERROR;
				break;
			case 41004 :
				$error_code = WX_Error::APP_SECRET_MISSING_ERROR;
				break;
			case 40013 :
				$error_code = WX_Error::INVALID_APP_ID_ERROR;
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
			case 40004 :
				$error_code = WX_Error::INVALID_MEDIA_TYPE_ERROR;
				break;
			case 40005 :
				$error_code = WX_Error::INVALID_FILE_TYPE_ERROR;
				break;
			case 41005 :
				$error_code = WX_Error::MEDIA_DATA_MISSING_ERROR;
				break;
			case 43002 :
				$error_code = WX_Error::REQUIRE_POST_METHOD_ERROR;
				break;
			case 40008 :
				$error_code = WX_Error::INVALID_MESSAGE_TYPE_ERROR;
				break;
			case 40007 :
				$error_code = WX_Error::MEDIA_ID_MISSING_ERROR;
				break;
			case 47001 :
				$error_code = WX_Error::DATA_FORMAT_ERROR;
				break;
			case 40012 :
				$error_code = WX_Error::INVALID_THUMB_SIZE_ERROR;
				break;
			case 44003 :
				$error_code = WX_Error::EMPTY_NEWS_DATA_ERROR;
				break;
			case 45008 :
				$error_code = WX_Error::ARTICLE_SIZE_OUT_ERROR;
				break;
			case 40006 :
				$error_code = WX_Error::INVALID_MEIDA_SIZE_ERROR;
				break;
			case 45007 :
				$error_code = WX_Error::PLAYTIME_OUT_ERROR;
				break;
			case 45009 :
				$error_code = WX_Error::API_FREQ_OUT_ERROR;
				break;
			case - 1 :
				$error_code = WX_Error::SYSTEM_ERROR;
				break;
			case 41001 :
				$error_code = WX_Error::KOTEN_MISSING_ERROR;
				break;
			case 40016 :
				$error_code = WX_Error::INVALID_BUTTON_SIZE;
				break;
			case 40017 :
				$error_code = WX_Error::INVALID_BUTTON_TYPE;
				break;
			case 40018 :
				$error_code = WX_Error::INVALID_BUTTON_NAME;
				break;
			case 40019 :
				$error_code = WX_Error::INVALID_BUTTON_KEY;
				break;
			case 40023 :
				$error_code = WX_Error::INVALID_SUB_BUTTON_SIZE;
				break;
			case 40024 :
				$error_code = WX_Error::INVALID_SUB_BUTTON_TYPE;
				break;
			case 40025 :
				$error_code = WX_Error::INVALID_SUB_BUTTON_NAME;
				break;
			case 40026 :
				$error_code = WX_Error::INVALID_SUB_BUTTON_KEY;
				break;
			case 46003 :
				$error_code = WX_Error::MENU_NO_EXIST;
				break;
			case 43004 :
				$error_code = WX_Error::REQUIRE_SUBSCRIBE;
				break;
			case 45015 :
				$error_code = WX_Error::RESPONSE_OUT_TIME;
				break;
			case 48001 :
				$error_code = WX_Error::API_UNAUTHORIZED;
                break;
            case 44002 :
                $error_code = WX_Error::EMPTY_POST_DATA;
                break;
            case 40032 :
                $error_code = WX_Error::INVALID_OPENID_LIST_SIZE;
                break;
            case 40059 :
                $error_code = WX_Error::INVALID_MSG_ID;
				break;
            case 61022:
                $error_code = WX_Error::COMPONECT_MERCHANT_AUDITING;
				break;
            case 61018:
            	$error_code = WX_Error::COMPONECT_ALREADY_CONFIRM;
            	break;
            case 61021:
            	$error_code = WX_Error::COMPONECT_NOT_SUBMIT_AUDIT;
            	break;
            case 61019:
            	$error_code = WX_Error::COMPONECT_NOT_NEED_CONFIRM;
            	break;
			default :
				$error_code = $code;
		}
		$this->_setError($error_code);
		return false;
    }

    /**
     * 清楚token缓存存储
     */
    protected function clearTokenCache()
    {
        if (function_exists("clearComponectTicket") && function_exists("getToken")) {
            $oldToken = getComponectToken($this->componect_app_id, $this->app_secret,$this->component_verify_ticket, false);
            if ($oldToken && $oldToken != $this->token) {
                return;
            }
            $c = clearComponectTicket($this->componect_app_id);
            if (! $c) {
                Logger::error('wxapiclient clear token cache fail');
            }
        }
	}

	/**
     * 获取api请求错误
     *
     * @param  int $code
     * @return void
     */
	protected function _setError($code, $log_enabled = true)
	{
		//记录错误日志
		if ($log_enabled)
			$this->_log();

		$this->_error_code = $code;
		$this->_error_message = WX_Error::getMessage($code);
	}

	/**
     * 解析token
     *
     * @param  array  $response
     * @return object WX_Token
     */
	protected function _parseToken($response)
	{
		if (! isset($response['component_access_token']) || ! $response['component_access_token']) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return null;
		}
		return new WX_Token($response['component_access_token'], $response['expires_in']);
	}
	
	/**
	 * 基础解析,公用，正确返回response,错误返回错误
	 * @date 2015-5-21
	 * @param $response
	 * @return array
	 */
	protected function _parseBase($response) {
		if (!isset($response['errcode']) || !isset($response['errmsg'])) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return false;
		}
		if (0 == $response['errcode']) {
			return $response;
		}
		return false;
	}
	
	/**
	 * 确认授权解析
	 * @date 2015-5-21
	 * @param $response
	 * @return array
	 */
	protected function _parseConfirmApiAuthorization($response) {
		if (!isset($response['errcode']) || !isset($response['errmsg'])) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return false;
		}
		if (0 == $response['errcode']) {
			return $response;
		}
		return false;
	}
	
	
    /**
     * 解析公众号授权token
     * @date 2015-2-6
     * @param  array  $response
     * @return object WX_Token
     */
	protected function _parseAuthorizerAccessToken($response)
	{
		if (! isset($response['authorizer_access_token']) || ! $response['authorizer_access_token']) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return null;
		}
		return new WX_Token($response['authorizer_access_token'], $response['expires_in'], @$response['authorizer_refresh_token']);
	}

	/**
     * 解析用户
     *
     * function_description
     *
     * @author mxg
     * @param  array  $response
     * @return
     */
	protected function _parsPreAuthCode($response)
	{
		if (! isset($response['pre_auth_code']) || ! $response['pre_auth_code'] ) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return null;
		}
		return $response;
	}

	/**
     * 解析授权信息
     *
     * @author mxg
     * @param  array  $response
     * @return array
     * array(
     *    'authorizer_appid' => string,//授权方appid
     *    'authorizer_access_token'=> string ,//授权方令牌
     *    'expires_in' => int,  //有效期
     *    'authorizer_refresh_token' => string //刷新令牌
     *    'func_info' => array() //授权权限集
     * )
     */
	protected function _parseQueryAuth($response)
	{
		if (! isset($response['authorization_info']) || ! isset($response['authorization_info']['authorizer_access_token'])) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return null;
		}
		
		return $response['authorization_info'];
	}

	/**
	 * 解析授权信息
	 *
	 * @author mxg
	 * @param  array  $response
	 * @return array
	 * array(
	 *    'authorizer_access_token'=> string ,//授权方令牌
	 *    'expires_in' => int,  //有效期
	 *    'authorizer_refresh_token' => string //刷新令牌
	 * )
	 */
	protected function _parseFlushAuthorizationToken($response)
	{
		if (! isset($response['authorizer_access_token']) || ! isset($response['authorizer_access_token'])) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return null;
		}
	
		return $response;
	}
	
	
	
	/**
     * 记录错误日志
     *
     * @author mxg
     * @return void
     */
	protected function _log()
	{
		$params = array ();
		$params['data'] = WeiXinApiRequest::$params;
		$params['response'] = WeiXinApiRequest::$response;
        $params['info'] = array('appid'=>$this->componect_app_id,
            'http_error'=> WeiXinApiRequest::$http_error,
            'http_error_code'=>WeiXinApiRequest::$http_error_code);
		Logger::error('WeiXinApi->_log：Contains the last API call url: ' . WeiXinApiRequest::$url, $params);
	}

   
    /**
     * 解析获取授权操作
     * @param array $response
     * @return array
     * {
		"authorizer_appid":"wx7bc5ba58cabd00f4",
		"option_name":"voice_recognize",
		"option_value":"1"
		}

     */
    protected function _parseGetAuthorizationOption($response)
    {
        if (! isset($response['authorizer_appid']) || ! isset($response['option_value'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        return $response;
    }
    
    /**
     * 解析设置授权操作
     * @param array $response
     * @return array
     * {
     "authorizer_appid":"wx7bc5ba58cabd00f4",
     "option_name":"voice_recognize",
     "option_value":"1"
     }
    
     */
    protected function _parseSetAuthorizationOption($response)
    {
    	if (! isset($response['errcode']) || ! isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	return $response;
    }

    /**
     * 解析获取授权方信息
     * @param array $response
     * @return mixed
     * {
		    "authorizer_info": {
		        "nick_name": "微信SDK Demo Special",
		        "head_img": "http://wx.qlogo.cn/mmopen/GPyw0pGicibl5Eda4GmSSbTguhjg9LZjumHmVjybjiaQXnE9XrXEts6ny9Uv4Fk6hOScWRDibq1fI0WOkSaAjaecNTict3n6EjJaC/0",
		        "service_type_info": {  "id": 2  },
		        "verify_type_info": {   "id": 0  },
				"user_name":"gh_eb5e3a772040",
				"alias":"paytest01"
		    },
		    "authorization_info": {
		        "appid": "wxf8b4f85f3a794e77",
		        "func_info": [
		            {   "funcscope_category": {  "id": 1  }    },
		            {   "funcscope_category": {  "id": 2  }    },
		            {   "funcscope_category": {  "id": 3  }    }
		        ]
		    }
		}
     */
    private function _parseGetAuthorizationInfo($response)
    {
        if (! isset($response['authorizer_info']) || ! isset($response['authorization_info'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }

        return $response;
    }

    /**
     * 分析发送消息结果
     *
     * @param  array $response
     * @return string $media_id
     */
    protected function _parseUpload($response) {
    	if (!isset($response['type'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return null;
    	}
    	$media_id = '';
    	switch ($response['type']) {
    		case 'thumb' :
    			if (!isset($response['thumb_media_id']) || !$response['thumb_media_id']) {
    				$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    				return null;
    			}
    			$media_id = $response['thumb_media_id'];
    			break;
    		default :
    			if (!isset($response['media_id']) || !$response['media_id']) {
    				$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    				return null;
    			}
    			$media_id = $response['media_id'];
    	}
    	return $media_id;
    }
    
    /**
     * 解析检测审核状态
     * @date 2015-5-21
     * @param $response
     * @return array
     */
    protected function _parseCheckStatus($response) {
//     	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
//     		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
//     		return false;
//     	}
    	if (isset($response['errcode']) && isset($response['errmsg'])
    		&& $response['errcode']!=0) {
    		return false;
    	}
    	if (isset($response['result'])) {
    		return $response;
    	}
    	return false;
    }
   
}