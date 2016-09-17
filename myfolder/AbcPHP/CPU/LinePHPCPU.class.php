<?php
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\LineHTTPClient;
/**
 * 这里是消息流核心处理类文件
 */
class LinePHPCPU
{
	/**
	 * 消息
	 * @var WX_Message
	 */
	private static $_message;

	/**
	 * 原始消息
	 * @var string<xml>
	 */
	private static $_msgStr;
	/**
	 * 与威信响应超时时间 单位秒
	 * @var int
	 */
	const REPLY_TIMEOUT = 5;
	/**
	 * 公共平台接收参数名称
	 * @var string
	 */
	const PARAM_TOKEN = 'token';
	const WX_WEB_TOKEN_KEY = 'ZHP,MXG,ZHPENG,GRH,ZHX,ZHTP';
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
	//5秒可以响应的消息类型
	private static $_noThreadMsgType = array (
			'text',
			'news',
			'music'
	);

	/**
	 * 入口
	 */
	public static function run()
	{	
		$msgStr = trim(file_get_contents('php://input'));
		$msgArr = json_decode($msgStr,true);
		Logger::info("receiver line msg:".$msgStr,$msgArr);
		//收到的文本
		$msg = $msgArr['result'][0]['content']['text'];
		
		$botResult = $this->getKeyReply($msg);
		if($reply === false){
			//通过小i机器人获得回答
			include_once LIB_PATH . '/../AbcPHP/Org/iBotCloud/XiaoiBot.php';
			$bot = new XiaoiBot( [ 'app_key' => 'QCrCl92wojmX', 'app_secret' => 'HX8klwdrbOJTPYaQukbj' ] );
			//自支应答
			$askResult = $bot->ask($msg);
			$botResult = "我暂时还无法回答您";
			if($askResult && $askResult[0]==200){
				$botResult = $askResult[1];
			}
		}		
		
		$botApi = new LINEBot(LineConfig::$base, new LineHTTPClient(LineConfig::$base));
		$result = $botApi->sendText([$msgArr['result'][0]['content']['from']], $botResult);
		Logger::info('callback result:',$result);
		echo "------------------test start----------</br>";
		var_dump($_REQUEST);
		echo "</br>------------------test end------------</br>";
		//var_dump($botApi->createReceivesFromJSON($msgStr));
		
		exit;
		$token = C('APP_WEIXIN_API_TOKEN');
		//验证来源是否是微信
		if (! self::_checkSignature($token)) {
			self::_errorLog('AbcPHPCPU::run() error: 验证来源是否是微信失败');
			exit();
		}
		//验证是否为初次接入
		self::_isAccess(); 

		//响应微信发送的消息数据
		self::_responseMsg();
	}

	/**
	 * 是否是初次接入微信公共平台
	 * @param string $token
	 * @return boolean
	 */
	private static function _isAccess()
	{
		if (isset($_GET["echostr"]) && ! empty($_GET["echostr"])) {
			echo $_GET["echostr"];
			exit();
		}
	}
	
	private function getKeyReply($key){
		$arr = array(array("key"=>array("福皓整合科技","福皓","福皓科技"),"reply"=>"http://www.full2house.com/"),
				array("key"=>array("产品","產品"),"reply"=>"電子商務平台：http://www.full2house.com/eBusiness.html  消費通路：http://www.full2house.com/channel.html"),
				array("key"=>array("電話","电话"),"reply"=>"02-87734066"),
					);
		$result = false;
		foreach ($arr as $v){
			if(in_array($key, $v["key"])){
				$result = $v['reply'];
				break;
			}
		}
		return $result;
	}

	/**
	 * 解析url设定的token值
	 * @return false|array
	 */
	private static function _parseToken()
	{
		$token = @$_GET[self::PARAM_TOKEN];
		$tokenParam = self::_decodeWxWebToken($token);
		if (! isset($tokenParam['token']) && empty($tokenParam['token'])) {
			self::_errorLog('AbcPHPCPU::_parseToken() error: token', $token);
			self::_errorLog('AbcPHPCPU::_parseToken() error: decodeWxWebToken', $tokenParam);
			return false;
		}
		return $tokenParam;
	}

	/**
	 * 转义微信web链接token
	 *
	 * @param string $token
	 * @return array
	 */
	private static function _decodeWxWebToken($token)
	{
		$token = base64_decode(trim($token));
		if (! $token)
			return false;

		$tokenArr = explode(';', $token);
		if (! $tokenArr)
			return false;

		$tokenParam = array ();
		$tokenCheck = array ();
		foreach ($tokenArr as $k => $v) {
			$oneArr = explode('=', $v);
			if ($oneArr && @$oneArr[0]) {
				$tokenParam[$oneArr[0]] = @$oneArr[1];
				if ($oneArr[0] != 'sig') {
					array_push($tokenCheck, $v);
				}
			}
		}
		$tokenSig = isset($tokenParam['sig']) ? trim($tokenParam['sig']) : '';
		if (! $tokenSig)
			return false;

		unset($tokenParam['sig']);
		$newSig = self::_genWxWebTokenSig(implode(';', $tokenCheck));
		if ($tokenSig != $newSig) {
			// 签名无效
			return false;
		}
		return $tokenParam;
	}

	/**
	 * 生成签名,
	 *
	 * @param string $str
	 * @return string
	 * @internal
	 *
	 */
	private static function _genWxWebTokenSig($str)
	{
		return md5($str . self::WX_WEB_TOKEN_KEY);
	}

	/**
	 * 验证密钥来源是否是微信
	 * @param string $token
	 * @return boolean
	 */
	private static function _checkSignature($token)
	{
		$signature = @$_GET["signature"];
		$timestamp = @$_GET["timestamp"];
		$nonce = @$_GET["nonce"];

		$tmpArr = array (
				$token,
				$timestamp,
				$nonce
		);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);

		if ($tmpStr == $signature) {
			return true;
		} else {
			self::_errorLog('AbcPHPCPU::_checkSignature() error: request params ', $_GET);
			self::_errorLog('AbcPHPCPU::_checkSignature() error: gen signature ', $tmpStr);
			return false;
		}
	}

	/**
	 * 响应微信发送的上行数据
	 * @return viod
	 */
	private static function _responseMsg()
	{
		self::_callFetchData();

		self::_callApp();

		self::_exit();
	}

	/**
	 * 调用数据解析器
	 */
	private static function _callFetchData()
	{
		$msgStr = trim(file_get_contents('php://input'));
		Logger::info('WXApp::main() : weixin XML ', $msgStr);
		if (! $msgStr) {
			self::_errorLog('AbcPHPCPU::_callFetchData() error: php://input: is null');
			self::_exit();
		}
		self::$_msgStr = $msgStr;
		$message = self::_parseMessage($msgStr);

		if (! $message || ! is_object($message)) {
			self::_errorLog('AbcPHPCPU::_callFetchData() error: weixin push data error', $msgStr);
			self::_exit();
		}

		self::$_message = $message;
		return $message;
	}

	/**
	 * 调用应用
	 * @return
	 */
	private static function _callApp()
	{
		$wxApp = C('WX_APP');

		if (! $wxApp) {
			self::_errorLog('AbcPHPCPU::_callApp() error: WX_APP param is null');
			return false;
		}

		$execType = $wxApp['EXEC_TYPE'];
		$filePath = $wxApp['FILE_PATH'];
		$className = $wxApp['CLASS_NAME'];
		$methodName = $wxApp['METHOD_NAME'];
		$classType = $wxApp['CLASS_TYPE'];

		$return = false;

		switch ($execType) {
			//本地加载插件方式
			case 'local' :
				if (! $filePath || ! $className || ! $methodName || ! $classType) {
					self::_errorLog('AbcPHPCPU::_callApp() error: WX_APP param error:' . $filePath . '==' . $className . '==' . $methodName . '==' . $classType, $wxApp);
					return false;
				}
				if (! file_exists($filePath)) {
					self::_errorLog('AbcPHPCPU::_callApp() error: WX_APP filePath error', $filePath);
					return false;
				}

				include_once $filePath;

				if (! class_exists($className, false) || ! method_exists($className, $methodName)) {
					self::_errorLog('AbcPHPCPU::_callApp() error: class or method not exist');
					return false;
				}

				if ('instance' == $classType) {
					$obj = new $className();
					$return = call_user_func(array($obj, $methodName), self::$_message, self::$_msgStr);
				} else if ('static' == $classType) {
					$return = call_user_func(array ($className, $methodName), self::$_message, self::$_msgStr);
				}

				break;
			case 'http' :
				if (strrpos($filePath, 'http://') === false || strrpos($filePath, 'https://') === false) {
					self::_errorLog('AbcPHPCPU::_callApp() error: remote url error', $filePath);
					return false;
				}
				//TODO:没支持客服接口发送
				$return = self::_thirdApp($filePath, self::$_msgStr);
				break;
		}

		if (! $return) {
			return false;
		}

		if (isset($return['message_body']) && $return['message_body']) {
			$messageBody = $return['message_body'];
			if (! $messageBody || !is_object($messageBody)) {
				self::_errorLog('AbcPHPCPU::_callApp() 响应数据为空或者不是对象', $messageBody);
				return false;
			}
			if (! in_array($messageBody->msgType, self::$_noThreadMsgType)) {
				Logger::debug('AbcPHPCPU::_callApp() 需要API发送消息，非5秒响应');
				return false;
			} else {
				include_once ABC_PHP_PATH . '/CPU/MessageTemplate.class.php';
				$messageXML = MessageTemplate::get(self::$_message->toUserName, $messageBody);
				Logger::info("AbcPHPCPU::_callApp() echo message xml:", $messageXML);
				echo $messageXML;
			}
		}
	}

	/**
	 * 第三方应用
	 * @param string $url
	 * @param string $input  消息文本流
	 * @return bool | WX_Message_Body
	 */
	private static function _thirdApp($url, $input)
	{
		$params = array ();
		$return = array ();
		$messageBody = '';

		$response = self::_http($url, $params, $input);
		$obj = json_decode($response, true);
		if (! $obj || ! isset($obj['data'])) {
			if ($this->getError()) {
				self::_errorLog('AbcPHPCPU::_thirdApp() error: remote Http request fail. http_code :' . $this->getHttpCode() . '; error :' . $this->getError() . "\nhttp_res: " . $this->getResponse(), $url);
			}
			return false;
		}
		if (is_bool($obj['data'])) {
			return $obj['data']; /*bool*/
		} else if (is_array($obj['data'])) {
			$messageBody = self::_parseResponse($response);
			if (! $messageBody) {
				if ($this->getError()) {
					self::_errorLog('AbcPHPCPU::_thirdApp() error: remote Http request fail. http_code :' . $this->getHttpCode() . '; error :' . $this->getError() . "\nhttp_res: " . $this->getResponse(), $url);
				}
				return false;
			}
		}
		$return['message_body'] = $messageBody;
		return $return;
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
		$curl = curl_init();
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

	/**
	 * 解析第三放返回结果
	 * @param string $response json string
	 * @return WX_Message_Body
	 */
	private static function _parseResponse($response)
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
		$messageBody = self::_genMessageBody($result['data']);
		return $messageBody;
	}

	/**
	 * 创建 wx_message_body
	 * @param array $data
	 * @return NULL|WX_Message_Body
	 */
	private static function _genMessageBody($data)
	{
		$messageBody = new WX_Message_Body();
		$msgType = @$data['msg_type'] ? $data['msg_type'] : '';
		$messageBody->msgType = $msgType;
		$messageBody->toUser = self::$_message->fromUser;
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
				if (! @$data['music_url'] || ! @$data['thumb_url'] || ! @$data['hq_music_url']) {
					self::$_ERROR = 'music data error';
					return null;
				}
				$messageBody->title = @$data['title'];
				$messageBody->description = @$data['description'];
				$messageBody->musicUrl = @$data['music_url'];
				$messageBody->thumbPath = @$data['thumb_url'];
				$messageBody->hqMusicUrl = @$data['hq_music_url'];
				break;
			case 'voice' :
			case 'image' :
			case 'video' :
				if (! @$data['media_url']) {
					self::$_ERROR = 'media_url error';
					return null;
				}
				$messageBody->attachment = $data['media_url'];
				$messageBody->mediaPath = $data['media_url'];
				if ('video' == $msgType) {
					$messageBody->title = @$data['title'];
					$messageBody->description = @$data['description'];
				}
				break;
			default :
				self::$_ERROR = 'message type not exsit';
				return null;
		}

		return $messageBody;
	}

	/**
	 * 错误日志记录
	 *
	 * @param
	 */
	private static function _errorLog($message, $data = null)
	{
		Logger::error($message, $data);
	}

	/**
	 * 解析消息
	 * @param string $msgStr
	 * @return bool|object <WX_Message>
	 */
	protected static function _parseMessage($msgStr)
	{
		$msgXmlObj = self::_parseMsgXml($msgStr);

		if (! $msgXmlObj) {
			self::_errorLog('AbcPHPCPU::_parseMessage() error: xml object error :', $msgStr);
			return null;
		}

		$toUserName = (string) $msgXmlObj->ToUserName;//开发者微信号
		$fromUserName = (string) $msgXmlObj->FromUserName;//发送方帐号（一个OpenID）
		$createTime = (string) $msgXmlObj->CreateTime;//消息创建时间 （整型）
		$msgType = strtolower((string) $msgXmlObj->MsgType);//消息类型 text|image|voice|video|location|link
		$msgId = (string) @$msgXmlObj->MsgId;//消息id，64位整型

		$wxMessage = new WX_Message($msgId, $msgType, $fromUserName, $createTime, $toUserName);

		switch ($msgType) {
			case 'text' :
				$content = (string) $msgXmlObj->Content; //文本消息内容
				$wxMessage->content = trim($content);
				break;
			case 'image' :
				$mediaId = (string) $msgXmlObj->MediaId; //图片消息媒体id
				$picUrl = (string) $msgXmlObj->PicUrl; //图片链接
				$wxMessage->mediaId = $mediaId;
				$wxMessage->mediaUrl = $picUrl;
				$wxMessage->picUrl = $picUrl;
				break;
			case 'voice' :
				$mediaId = (string) $msgXmlObj->MediaId; //语音消息媒体id
				$format = (string) $msgXmlObj->Format; //语音格式，如amr，speex等
				$recognition = @ (string) $msgXmlObj->Recognition; //语音识别结果，UTF8编码
				$wxMessage->mediaId = $mediaId;
				$wxMessage->format = $format;
				$wxMessage->recognition = $recognition;
				break;
			case 'video' :
				$mediaId = (string) $msgXmlObj->MediaId; //视频消息媒体id
				$thumbMediaId = (string) $msgXmlObj->ThumbMediaId; //视频消息缩略图的媒体id
				$wxMessage->mediaId = $mediaId;
				$wxMessage->thumbMediaId = $thumbMediaId;
				break;
			case 'location' :
				$locationX = (string) $msgXmlObj->Location_X; //地理位置维度
				$locationY = (string) $msgXmlObj->Location_Y; //地理位置经度
				$scale = (string) $msgXmlObj->Scale; //地图缩放大小
				$label = (string) $msgXmlObj->Label; //地理位置信息
				$wxMessage->location = new WX_Location($msgId, $locationX, $locationY, $scale, $label);
				break;
			case 'link' :
				$title = (string) $msgXmlObj->Title;
				$description = (string) $msgXmlObj->Description;
				$url = (string) $msgXmlObj->Url;
				$wxMessage->title = $title;
				$wxMessage->description = $description;
				$wxMessage->url = $url;
				break;
			case 'event' :
				$eventType = strtolower((string) $msgXmlObj->Event); //事件类型
				$wxMessage->event = new WX_Event($eventType);
				switch ($eventType) {
					case 'subscribe' : //订阅|扫描带参数二维码 用户未关注时，进行关注后的事件
					case 'scan' : //扫描带参数二维码 用户已关注时
						$eventKey = (string) @$msgXmlObj->EventKey; //扫描带参数二维码 qrscene_为前缀，后面为二维码的参数值
						$ticket = (string) @$msgXmlObj->Ticket; //二维码的ticket，可用来换取二维码图片
						$wxMessage->event->eventKey = $eventKey ? $eventKey : null;
						$wxMessage->event->ticket = $ticket ? $ticket : null;
						break;
					case 'unsubscribe' : //取消订阅
						break;
					case 'click' : //点击菜单拉取消息
					case 'view' : //点击菜单跳转链接
						$eventKey = (string) $msgXmlObj->EventKey; //自定义菜单接口中KEY值对应|设置的跳转URL
						$wxMessage->event->eventKey = $eventKey;
						break;
					case 'location' : //上报地理位置
						$latitude = (string) $msgXmlObj->Latitude; //地理位置纬度
						$longitude = (string) $msgXmlObj->Longitude; //地理位置经度
						$precision = (string) $msgXmlObj->Precision; //地理位置精度
						$wxMessage->event->latitude = $latitude;
						$wxMessage->event->longitude = $longitude;
						$wxMessage->event->precision = $precision;
						break;
					case 'templatesendjobfinish' : //模版消息结果
						$msgId = (string) $msgXmlObj->MsgID;
						$wxMessage->msgId = $msgId;
						$wxMessage->event->msgId = $msgId;
						/*
						模版消息发送结果：
						“success” 成功
						“failed:user block” 由于用户拒收（用户设置拒绝接收公众号消息）而失败
						“failed: system failed” 由于其他原因失败
						*/
						$status = (string) $msgXmlObj->Status;
						$wxMessage->event->status = $status;
						break;
					case 'masssendjobfinish' : //群发消息结果
						//群发的消息ID
						$msgId = (string) $msgXmlObj->MsgID;
						//公众号群发助手的微信号，为mphelper
						$fromUserName = (string) $msgXmlObj->FromUserName;
						/*
						群发的结果，为“send success”或“send fail”或“err(num)”。
						但send success时，也有可能因用户拒收公众号的消息、系统错误等原因造成少量用户接收失败。
						err(num)是审核失败的具体原因，可能的情况如下：
						err(10001), //涉嫌广告 err(20001), //涉嫌政治 err(20004), //涉嫌社会
						err(20002), //涉嫌色情 err(20006), //涉嫌违法犯罪 err(20008), //涉嫌欺诈
						err(20013), //涉嫌版权 err(22000), //涉嫌互推(互相宣传) err(21000), //涉嫌其他
						*/
						$status = (string) $msgXmlObj->Status;
						//group_id下粉丝数；或者openid_list中的粉丝数
						$totalCount = (string) $msgXmlObj->TotalCount;
						/*
						过滤（过滤是指，有些用户在微信设置不接收该公众号的消息）后，
						准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount
						*/
						$filterCount = (string) $msgXmlObj->FilterCount;
						//发送成功的粉丝数
						$sentCount = (string) $msgXmlObj->SentCount;
						//发送失败的粉丝数
						$errorCount = (string) $msgXmlObj->ErrorCount;
						$wxMessage->msgId = $msgId;
						$wxMessage->event->msgId = $msgId;
						$wxMessage->event->status = $status;
						$wxMessage->event->totalCount = $totalCount;
						$wxMessage->event->filterCount = $filterCount;
						$wxMessage->event->sentCount = $sentCount;
						$wxMessage->event->errorCount = $errorCount;
						break;
				}
				break;
			default :
				$wxMessage = null;
		}
		return $wxMessage;
	}

	/**
	 * 解析原消息xml
	 * @param string $msgStr
	 * @return NULL|SimpleXMLElement
	 */
	protected static function _parseMsgXml($msgStr)
	{
		$msgXmlObj = simplexml_load_string($msgStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		if (! $msgXmlObj) {
			//TODO: 添加正则处理
			self::_errorLog('AbcPHPCPU::_parseMsgXml() error: xml object error :', $msgStr);
			return null;
		}

		return $msgXmlObj;
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
	 * 程序终止前调用方法
	 */
	private static function _exit()
	{
		if (! self::$_message || ! self::$_msgStr) {
			exit();
		}
		exit();
	}
}