<?php
/**
 * 这里是处理过滤器客服接口消息发送
 */
define("APP_PLATE", 'WXApp');

set_time_limit(0);

include_once dirname(__FILE__) . '/../../Init.php';

//error_reporting(E_ALL);
//ini_set("display_errors", true);

$messageBody = @json_decode($argv[1]);
$pluginKey = @$argv[2];
$pluginInfo = @$argv[3];

$filterSendThread = new FilterSendThread($messageBody, $pluginKey, $pluginInfo);
$filterSendThread->run();

/**
 * 过滤器消息发送类
 */
class FilterSendThread
{
	private $_messageBody;
	private $_pluginKey;
	private $_pluginInfo;

	private $_tmpFiles = array();

	public function __construct($messageBody, $pluginKey, $pluginInfo = '')
	{
		$this->_messageBody = $messageBody;
		$this->_pluginKey = $pluginKey;
		$this->_pluginInfo = $pluginInfo;
	}

	/**
	 * 运行
	 * @return boolean
	 */
	public function run()
	{
		Logger::debug('<'.$this->_pluginKey.' plugin> 发送消息进程开始执行');
		if (! $this->_messageBody) {
			Logger::error('<'.$this->_pluginKey.' plugin> 发送消息进程 数据为空');
			return false;
		}

		$client = $this->_getWXApiClient();

		if ($client === false) {
			Logger::erro('<'.$this->_pluginKey.' plugin> 发送消息进程 获取API客户端失败 执行结束');
			return false;
		}
		Logger::debug('<'.$this->_pluginKey.' plugin> 发送消息进程 获取的发送信息', $this->_messageBody);

		$sendMessageBody = $this->_genSendMessageBody(clone $this->_messageBody);

		if (! $sendMessageBody) {
			Logger::error('<'.$this->_pluginKey.' plugin> 发送消息进程 生成发生数据错误');
			return false;
		}
		//var_dump($sendMessageBody);exit;
		$return = $client->sendMessage($sendMessageBody);

		Logger::debug('<'.$this->_pluginKey.' plugin> ', $return);
		//发送信息
		if ($return) {
			$this->_addDownDialog($this->_messageBody);
			Logger::debug('<'.$this->_pluginKey.' plugin> 发送消息进程 执行成功结束');
		} else {
			Logger::error('<'.$this->_pluginKey.' plugin> error code:'. $client->getErrorCode());
			Logger::error('<'.$this->_pluginKey.' plugin> error msg:'. $client->getErrorMessage());
			Logger::error('<'.$this->_pluginKey.' plugin> 发送消息进程 发送信息失败执行结束');
			return false;
		}
		return true;
	}

	/**
	 * @name 获取微信API
	 */
	private function _getWXApiClient()
	{
		$appId = C('APP_ID');
		$appSecret = C('APP_SECRET');

		$token = getToken($appId, $appSecret);
		if (! $token) {
			Logger::error('<'.$this->_pluginKey.' plugin> 插件获取token 失败 weixin_app_id：' . $appId
			. '  weixin_app_secret: ' . $appSecret);
			return false;
		}
		$client = WeiXinApiCore::getClient($appId, $appSecret, $token);
		if (! $client) {
			Logger::error('<'.$this->_pluginKey.' plugin> 插件获取token 失败 weixin_app_id：'
					. $appId . '  weixin_app_secret: ' . $appSecret . ' token:' . $token);
			return false;
		}
		return $client;
	}

	/**
	 * 生成发送消息内容
	 * @param WX_Message_Body $messageBody
	 * @return WX_Message_Body|null
	 */
	private function _genSendMessageBody($messageBody)
	{
		switch($messageBody->msgType){
			case 'music':
				if ($messageBody->thumbPath) {
					$file = $this->_downloadMedia('thumb', $messageBody->thumbPath);
					if (-1 == $file) {
						Logger::error('<'.$this->_pluginKey.' plugin> download file error: type music, media_url '. $messageBody->thumb_path);
						return null;
					}
					$messageBody->thumbPath = $file;
					$this->_pushTmpFile($file);
				}
				break;
			case 'video':
			case 'image':
			case 'voice':
				if ($messageBody->attachment) {
					$file = $this->_downloadMedia($messageBody->msgType, $messageBody->attachment);
					if(-1 == $file){
						Logger::error('<'.$this->_pluginKey.' plugin> download file error: type '.$messageBody->msgType.', media_url ' . $messageBody->attachment);
						return false;
					}
					$messageBody->attachment = $file;
					$this->_pushTmpFile($file);
				}
				break;
		}
		return $messageBody;
	}

	/**
	 * 创建媒体
	 * @param $type
	 * @param $mediaUrl
	 * @return
	 */
	private function _downloadMedia($type, $mediaUrl)
	{
		if (! $mediaUrl) {
			return - 1;
		}
		$mediaCon = file_get_contents($mediaUrl);
		if (! $mediaCon) {
			return - 1;
		}

		switch ($type) {
			case 'image' :
			case 'thumb' :
				$ext = '.jpg';
				break;
			case 'voice' :
				$parts = pathinfo($mediaUrl);
				$extension = @$parts['extension'];
				if (! in_array(strtolower($extension), array('mp3','amr'))) {
					return -1;
				}
				$ext = '.' . strtolower($extension);
				break;
			case 'video' :
				$ext = '.mp4';
				break;
			default :
				return - 1;
				break;
		}

		$file = "/tmp/" . uniqid() . $ext;
		$save = file_put_contents($file, $mediaCon);
		if (! $save) {
			return - 1;
		}
		unset($mediaCon);
		return $file;
	}

	/**
	 * 添加下行对话记录
	 * @param WX_Message_Body $message 保存数据
	 * @return bool
	 */
	private function _addDownDialog($message)
	{
		if (! $message || ! $message->msgType) {
			Logger::error('添加dialog数据失败! ; plugin_key:'. $this->_pluginKey .'; message:',$message);
			return false;
		}

		$db = Factory::getDb();

		$msgType = $message->msgType;
		$params = array(
				'session_id' => -1,
				'operator_id' => -1,
				'operator_name' => '系统',
				'create_time' => date('Y-m-d H:i:s'),
				'msg_type' => $msgType,
				'plugin_key' => $this->_pluginKey,
				'type' => 4,
				'msg_id' => -1,
				'openid' => $message->toUser
		);
		if ($this->_pluginInfo) {
			$params['plugin_info'] = stripcslashes($this->_pluginInfo);
		}

		switch ($msgType) {
    		case 'text':
    			$params['content'] = faddslashes($message->content);
    			break;
			case 'news' :
				$params['articles'] = faddslashes(serialize($message->articles));
				break;
			case 'image' :
				$params['media_id'] = $message->mediaId;
				$params['media_url'] = faddslashes($message->attachment);
				break;
			case 'voice' :
				$params['media_id'] = $message->mediaId;
				$params['media_url'] = faddslashes($message->attachment);
				break;
			case 'video' :
				$params['media_id'] = $message->mediaId;
				$params['thumb_media_id'] = $message->thumbMediaId;
				$params['media_url'] = faddslashes($message->attachment);
				$params['thumb_url'] = faddslashes($message->thumbpath);
				break;
			case 'music' :
				$params['title'] = faddslashes($message->title);
				$params['description'] = faddslashes($message->description);
				$params['thumb_url'] = faddslashes($message->thumbpath);
				$params['thumb_media_id'] = $message->thumbMediaId;
				$params['music_url'] = faddslashes($message->musicUrl);
				$params['hq_music_url'] = faddslashes($message->hqMusicUrl);
				break;
			default:
				//return;
		}
		try {
			return $db->insert('wx_dialog', $params);
		} catch (Exception $e) {
			Logger::error('<'.$this->_pluginKey.' plugin>添加dialog数据失败! params:',$params);
			return false;
		}
	}

	private function _pushTmpFile($file)
	{
		if (!$file || !is_string($file)) return;
		array_push($this->__tmpFiles, $file);
	}

	public function __destruct()
	{
		foreach ($this->_tmpFiles as $file) {
			if (is_file($file)) {
				@unlink($file);
			}
		}
	}
}