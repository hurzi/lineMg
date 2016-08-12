<?php
/**
 * 发送消息文件
 * @author grh
 * @since 2014-3-7
 */
if (!class_exists("WeiXinApiCore")) {
	include LIB_PATH . '/../../../AbcPHP/API/WeiXinApiCore.class.php';
}
class SendMessage
{
	protected $error_code = 0;
	protected $error = '';
	protected $app_id;
	protected $app_secret;
	protected $max_count = 0;

	public function __construct($app_id, $app_secret){
		$this->app_id = $app_id;
		$this->app_secret = $app_secret;
	}

	public function getErrorCode (){
		return $this->error_code;
	}

	public function getError(){
		return $this->error;
	}

	/**
	 * 单个发送消息
	 * @param array $message
	 */
	public function send($message){
		$messageBody = $this->createMessageBody($message);
		if(!$messageBody){
			return false;
		}
		
		return $this->_sendMessage($messageBody);
	}
	/**
	 * 群发送消息
	 * @param array $message
	 */
	public function massSend($message, $max_count){
		$message= $this->check($message, $max_count);
		return false;
	}

	/**
	 * 创建messageBody
	 * @param array $message
	 * @return boolean|WX_Message_Body
	 */
	private function createMessageBody($message){
		$message = $this->check($message);
		if(!$message){
			return false;
		}
		$messageBody = new WX_Message_Body();
		$messageBody->msgType = $message['type'];
		$messageBody->toUser = $message['to_users'];
		switch ($message['type']){
			case 'text':
				$messageBody->content = $message['content'];
				break;
			case 'news':
				$messageBody->articles = $message['articles'];
				break;
			case 'template':
				$messageBody->template_id = $message['template_id'];
				$messageBody->data = $message['data'];
				break;
			case 'music':
				break;
			case 'video':
				break;
			case 'voice':
				break;
			case 'image':
				break;
			default:
				break;
		}
		return $messageBody;
	}

	/**
	 * 验证消息格式
	 * @param array $message
	 * @param int $max_count
	 * @return boolean|unknown
	 */
	private  function check($message ,$max_count=null){
		if (!is_array($message) ) {
			$this->error('message format error','check');
			return false;
		}
		if(!@$message['to_users'] || ! $toUsers = explode(',', @$message['to_users'])){
			$this->error('message to_users error','check');
			return false;
		}
		if($max_count){
			//验证目标用户数量
			$toUsers = array_unique(array_filter($toUsers));
			$count = count($toUsers);
			$this->max_count = $count;
			if ($count <= 0 || $count > $max_count) {
				$this->error('send mass overflow max_count','check');
				return false;
			}
		}
		if(!@$message['type']){
			$this->error('message type empty','check');
			return false;
		}
		switch (@$message['type']){
			case 'text':
				if (!@$message['content'] || !is_string(@$message['content'])) {
					$this->error('text content param empty','check');
					return false;
				}
				break;
			case 'news':
				$articles = @$message['articles'];
				if (! $articles || !is_array($articles) ) {
					$this->error('news articles param empty','check');
					return false;
				}
				if (count($articles) > 10) {
					$this->error('news articles limit 10','check');
					return false;
				}
				foreach ($articles as $key => $value) {
					if (!is_array($value) || !$value || !@$value['title'] || !@$value['description']
							|| !@$value['url'] || !@$value['picurl']) {
						$this->error('news articles param error','check');
						return false;
					}
				}
				break;
			case 'template':
				if (! @$message['template_id']) {
					$this->error('template_id empty','check');
					return false;
				}
				if (! is_array(@$message['data']) || ! @$message['data']) {
					$this->error('template data error','check');
					return false;
				}
				break;
			case 'music':
			case 'video':
			case 'voice':
			case 'image':
			default:
				$this->error('message type error','check');
				return false;
				break;
		}
		return $message;
	}

	/**
	 * 调用API发送消息
	 * @param object $messageBody
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	private function _sendMessage ($messageBody){
		//API发送
		//Factory::getCacher()->clear('ABC_WX_API_TOKEN' . $this->app_id);
		$token = getToken($this->app_id, $this->app_secret);
		$weixinClient = WeiXinApiCore::getClient($this->app_id, $this->app_secret, $token);
		$message_id = $weixinClient->sendMessage($messageBody);
		Logger::debug("send message result: ", $message_id);
		//处理发送失败情况
		if (! $message_id) {
			$wxApiErrorCode = $weixinClient->getErrorCode();
			$clientError = array (
					'errorcode' => $wxApiErrorCode,
					'errormsg' => $weixinClient->getErrorMessage()
			);
			if (WX_Error::API_FREQ_OUT_ERROR == $wxApiErrorCode) {
				$this->error("下行消息达到上限", '_sendMessage', true, $clientError);
				return false;
			} else if (WX_Error::INVALID_USER_ERROR == $wxApiErrorCode) {
				$this->error('用户无效', "_sendMessage", true, $clientError);
				return false;
			} else if (WX_Error::API_UNAUTHORIZED == $wxApiErrorCode) {
				$this->error("api未授权", '_sendMessage', true, $clientError);
				return false;
			} else if (WX_Error::RESPONSE_OUT_TIME == $wxApiErrorCode) {
				$this->error("会话超时", '_sendMessage', true, $clientError);
				return false;
			}
			$this->error("wx api client error", '_sendMessage', true, $clientError);
			return false;
		}
		//保存发送信息
		$msgManager = new MessageManager($messageBody,"");
		$msgManager->addDialog(2, 'third', $messageBody);
// 		/MessageManager::saveToMessage($messageBody);
		return $message_id;
	}
	/**
	 * 设置错误和记录日志
	 * @param string $error 错误提示
	 * @param string $logTitle log错误标题
	 * @param bool $isLog 是否写入log
	 * @param mixed $logVar
	 */
	private function error($error, $logTitle='', $isLog=true, $logVar=null)
	{
		$this->error = $error;
		if ($isLog) {
			Logger::error("$logTitle -> error msg:" . $this->getError(), $logVar);
		}
	}

}