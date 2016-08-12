<?php
/**
 * 随视与第三方企业交互工具类，由这里统一请求参数和返回数据接口
 * 请求时间限制为三秒
 * 1，统一处理参数
 * 2，向第三方发送请求
 * 3，解析第三方返回数据
 *
 */
class ThirdPartyTools
{
	//http请求code
	private static $_HTTP_CODE = 0;
	//http请求详细信息
	private static $_HTTP_INFO = NULL;
	//第三方返回的原始信息
	private static $_HTTP_RESPONSE;
	//自定义错误
	private static $_ERROR = '';
	//操作
	private static $_ACTION = '';
	//超时时间
	private static $_TIME_OUT = 3; //秒
	//http error code
	private static $_HTTP_ERROR_CODE = 0;
	//http error
	private static $_HTTP_ERROR = '';
	private static $_HTTP_PARAMS;

	private static $_MONITOR_DATA;

	private static function _init($action = '')
	{
		self::$_ACTION = $action;
		self::$_HTTP_CODE = 0;
		self::$_HTTP_INFO = null;
		self::$_HTTP_ERROR_CODE = 0;
		self::$_HTTP_ERROR = '';
		self::$_ERROR = '';
		self::$_MONITOR_DATA = null;
	}

	/**
	 * 第三方自定义插件
	 * @param string $url
	 * @param WX_Message $message
	 * @param string $input 消息文本流
	 * @return bool | WX_Message_Body
	 */
	public static function pluginThirdPush($url, $message, $input)
	{
		self::_init('pluginThirdPush');
		$openid = $message->fromUser;
		$authParam = getAuthQueryData();
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid
		));
		$response = self::_http($url, $params, $input);
		$obj = json_decode($response, true);
		if (! $obj || ! isset($obj['data'])) {
			return false;
		}
		if (is_bool($obj['data'])) {
			return $obj['data']; /*bool*/
		} else if (is_array($obj['data'])) {
			return self::_parseResponse($openid, $response);
		}
		return false;
	}

	/**
	 * 欢迎词插件动态消息数据获取请求交互
	 * @param string $url
	 * @param WX_Message $message
	 * @return WX_Message_Body
	 */
	public static function pluginWelcomePush($url, $message)
	{
		self::_init('pluginWelcomePush');
		$openid = $message->fromUser;
		$authParam = getAuthQueryData();
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid,
				'event_key' => $message->event->eventKey
		));
		$response = self::_http($url, $params);
		return self::_parseResponse($openid, $response);
	}

	/**
	 * 自定义菜单插件动态消息获取请求交互
	 * @param string $url
	 * @param WX_Message $message
	 * @return WX_Message_Body
	 */
	public static function pluginCustomMenuPush($url, $message)
	{
		self::_init('pluginCustomMenuPush');
		$openid = $message->fromUser;
		$authParam = getAuthQueryData();
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid
		));

		$response = self::_http($url, $params);
		return self::_parseResponse($openid, $response);
	}

	/**
	 * 关键词插件动态消息数据获取请求交互
	 * @param string $url
	 * @param WX_Message $message
	 * @return WX_Message_Body
	 */
	public static function pluginKeywordPush($url, $message)
	{
		self::_init('pluginKeywordPush');
		$openid = $message->fromUser;
		$authParam = getAuthQueryData();
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid
		));
		$response = self::_http($url, $params);
		return self::_parseResponse($openid, $response);
	}

	/**
	 * 地理位置插件动态消息数据获取请求交互
	 * @param string $url
	 * @param WX_Message $message
	 * @param float $x 经度
	 * @param float $y  纬度
	 * @return WX_Message_Body
	 */
	public static function pluginLocationPush($url, $message, $x, $y)
	{
		self::_init('pluginLocationPush');
		$openid = $message->fromUser;
		$authParam = getAuthQueryData();
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid,
				ThirdPartyReqParams::LOCATION_X => $x,
				ThirdPartyReqParams::LOCATION_Y => $y
		));
		$response = self::_http($url, $params);
		return self::_parseResponse($openid, $response);
	}

	/**
	 * 自定义回复插件动态消息数据获取请求交互
	 * @param string $apiKey
	 * @param string $apiSecret
	 * @param string $url
	 * @param string $openid
	 * @param string $query
	 * @return WX_Message_Body
	 */
	public static function pluginConsultPush($url, $message, $query)
	{
		self::_init('pluginConsultPush');
		$openid = $message->fromUser;
		$authParam = getAuthQueryData();
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid,
				ThirdPartyReqParams::QUERY => $query
		));
		$response = self::_http($url, $params);
		return self::_parseResponse($openid, $response);
	}

	/**
	 * 带参数二维码的第三方数据获取
	 * @param string $url
	 * @param WX_Message $message
	 * @return WX_Message_Body
	 */
	public static function pluginQrcParamPush($url, $message)
	{
		self::_init('pluginQrcParamPush');
		$openid = $message->fromUser;
		$authParam = getAuthQueryData();
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid,
				'event_key' => $message->event->eventKey
		));
		$response = self::_http($url, $params);
		return self::_parseResponse($openid, $response);
	}



	/**
	 * 推送设置
	 * @param string $apiKey
	 * @param string $apiSecret
	 * @param string $url
	 * @param string $openid
	 * @param string $type
	 * @param string $operatorid
	 * @param string $input 消息文本流
	 */
	public static function pushSettingPush($apiKey, $apiSecret, $url, $openid, $type, $operatorid, $input)
	{
		self::_init('pushSettingPush');
		$authParam = getAuthQueryData($apiKey, $apiSecret);
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid,
				ThirdPartyReqParams::PUSH_TYPE => $type,
				ThirdPartyReqParams::OPERATOR_ID => $operatorid
		));
		//（TODO）
		$data = $input;
		self::_http($url, $params, $data);
		return (self::$_HTTP_CODE == 200);
	}

	/**
	 * IVR流程第三方数据获取
	 * @param string $apiKey
	 * @param string $apiSecret
	 * @param string $url
	 * @param string $openid
	 * @return WX_Message_Body
	 */
	public static function ivrThirdPush($apiKey, $apiSecret, $url, $openid, $postData = null)
	{
		self::_init('ivrThirdPush');
		$authParam = getAuthQueryData($apiKey, $apiSecret);
		$params = array_merge($authParam, array (
				ThirdPartyReqParams::OPEN_ID => $openid
		));
		$ret = self::_http($url, $params, $postData);
		return self::_parseResponse($openid, $ret);
	}

	/**
	 * 获取本次请求原始信息
	 * @return string json string
	 */
	public static function getResponse()
	{
		return self::$_HTTP_RESPONSE;
	}

	/**
	 * 获取本次请求错误信息
	 * @return string
	 */
	public static function getError()
	{
		if (self::$_HTTP_ERROR_CODE) {
			return self::$_ERROR . ', http_error_code:' . self::$_HTTP_ERROR_CODE . ", http_error:" . self::$_HTTP_ERROR;
		}
		return self::$_ERROR;
	}

	/**
	 * 获取本次请求httpcode
	 * @return int
	 */
	public static function getHttpCode()
	{
		return self::$_HTTP_CODE;
	}

	/**
	 * 获取本次请求httpInfo
	 * @return array
	 */
	public static function getHttpInfo()
	{
		return self::$_HTTP_INFO;
	}

	/**
	 * 获取本次请求参数
	 * @return array
	 */
	public static function getHttpParams()
	{
		if (is_array(self::$_HTTP_PARAMS)) {
			$params = http_build_query(self::$_HTTP_PARAMS);
		}
		return $params ? $params : self::$_HTTP_PARAMS;
	}

	/**
	 * 获取本次操作
	 * @return string
	 */
	public static function getAction()
	{
		return self::$_ACTION;
	}

	/**
	 * 获取Monitor数据信息
	 * @return array
	 */
	public static function getMonitorData()
	{
		return self::$_MONITOR_DATA;
	}

	/**
	 * 解析第三放返回结果
	 * @param string $response json string
	 * @return WX_Message_Body
	 */
	private static function _parseResponse($openid, $response)
	{
		//check http code
		if (self::$_HTTP_CODE != 200) {
			self::$_ERROR = 'http request error';
			return null;
		}
		if (! $response) {
			self::$_ERROR = "http response empty";
			return null;
		}
		//json_decode
		$result = json_decode($response, true);
		if (! $result || ! @$result['data'] || ! isset($result['error']) || $result['error'] != 0) {
			self::$_ERROR = 'http response fomat error';
			return null;
		}
		
		//create wx_message_body
		$messageBody = self::_genMessageBody($openid, $result['data']);
		return $messageBody;
	}

	/**
	 * 生成 wx_message_body
	 * @param string $openid
	 * @param array $data
	 * @return NULL|WX_Message_Body
	 */
	private static function _genMessageBody($openid, $data)
	{
		$messageBody = new WX_Message_Body();
		$msgType = @$data['msg_type'] ? $data['msg_type'] : '';
		$messageBody->msgType = $msgType;
		$messageBody->toUser = $openid;
		switch ($msgType) {
			case 'text' :
				if (! @$data['text']) {
					self::$_ERROR = 'text data empty';
					return null;
				}
				$messageBody->content = trim($data['text']);
				break;
			case 'news' :
				$articles = @$data['news'];
				if (! $articles || ! is_array($articles) || count($articles) > 10) {
					self::$_ERROR = 'news data error';
					return null;
				}
				foreach ($articles as $key => $value) {
					if (! is_array($value) || ! $value || ! @$value['title'] || ! @$value['description'] || ! @$value['url'] || ! @$value['picurl']) {
						self::$_ERROR = 'news param data error';
						return null;
					}
				}
				$messageBody->articles = $articles;
				break;
			case 'music' :
				if (! @$data['title'] || ! @$data['description'] || ! @$data['music_url'] || ! @$data['thumb_url'] || ! @$data['hq_music_url']) {
					self::$_ERROR = 'music data error';
					return null;
				}
				$messageBody->title = $data['title'];
				$messageBody->description = $data['description'];
				$messageBody->musicUrl = $data['music_url'];
				$messageBody->thumbPath = $data['thumb_url'];
				$messageBody->hqMusicUrl = $data['hq_music_url'];
				break;
			case 'voice' :
			case 'image' :
			case 'video' :
				if (! @$data['media_url']) {
					self::$_ERROR = 'media_url error';
					return null;
				}
				$messageBody->attachment = $data['media_url'];
				if ('video' == $msgType) {
					$messageBody->title = @$data['title'];
					$messageBody->description = @$data['description'];
				}
				break;
			default :
				self::$_ERROR = 'message type not exsit';
				return null;
		}
		self::$_MONITOR_DATA = @$data['monitor_data'];
		return $messageBody;
	}

	/**
	 * http 请求并解析返回结果
	 * @param string $url
	 * @param array $params
	 * @param string $data
	 * @return WX_Message_Body
	 */
	private static function _request($openid, $url, $params = array(), $data = null)
	{
		$ret = self::_http($url, $params, $data = null);
		return self::_parseResponse($openid, $ret);
	}

	/**
	 * http 请求
	 * @param string $url
	 * @param array $params
	 * @param string $data
	 * @return string
	 */
	private static function _http($url, $params = array(), $data = null)
	{
		self::$_HTTP_PARAMS = $params;
		$curl = curl_init();
		//TODO
		if (empty($data)) {
			$body = '';
			if (! empty($params)) {
				if (is_array($params)) {
					$body = http_build_query($params);
				}
			}
		} else {
			$url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
			$body = $data;
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 3);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::$_TIME_OUT);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
		$urlArr = parse_url($url);
		$port = empty($urlArr['port']) ? 80 : $urlArr['port'];
		curl_setopt($curl, CURLOPT_PORT, $port);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array (
				'Expect:'
		));
		//获取的信息以文件流的形式返回,不直接输出
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		self::$_HTTP_CODE = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		self::$_HTTP_INFO = curl_getinfo($curl);
		self::$_HTTP_ERROR_CODE = curl_errno($curl);
		self::$_HTTP_ERROR = curl_error($curl);
		curl_close($curl);
		self::$_HTTP_RESPONSE = $response;
		return $response;
	}
}

