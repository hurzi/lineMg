<?php
include_once dirname(__FILE__) . '/Manager.class.php';
/**
 * weixin Queue Manager
 * 这里是队列管理器，增，删，改，查.
 *
 */
class QueueManager extends Manager
{

	/**
	 * 入口函数
	 * @return bool true:被过滤器命中，否则为false.
	 */
	public function main()
	{
		if (! $this->_getAppInfo()) {
			return false;
		}
		return $this->_createQueue();
	}

	/**
	 * 创建队列
	 *
	 * @return bool
	 */
	protected function _createQueue()
	{
		if (! is_object($this->message) || (! isset($this->message->from_user) || ! $this->message->from_user) || (! isset($this->message->message_id) || ! $this->message->message_id)) {
			Logger::error('QueueManager->_createQueue() error: object message is error', $this->message);
			return false;
		}
		//检测会话是否存在
		$session = $this->_checkSession($this->message->from_user);
		//sql出错
		if ($this->sqlError) {
			return false;
		}
		//会话中存在直接返回
		if ($session) {
			return true;
		}

		//检测队列是否存在
		$queue = $this->_checkQueue($this->message->from_user);
		//sql出错
		if ($this->sqlError) {
			return false;
		}
		//队列中存在直接返回
		if ($queue) {
			return true;
		}

		/**
		 * 企业设置自动进入客服
		 * 1：自动进入开启，直接进入队列
		 * 2：自动关闭，匹配关键词
		 * 2.1：匹配命中进入队列
		 * 2.2：匹配失败，回复设置的内容 (使用5秒自动回复)
		 */
		$checkAutoSet = $this->_checkAutoOperatorSetting();
		if (! $checkAutoSet) {
			Logger::debug("企业后台设定手动进入人工客服 user: " . $this->message->from_user . '; ent_id:' . $this->entId);
			$this->_addDialog(1, $this->message, '-4');//保存上行信息

			$setting = $this->getEntSetting($this->entId);
			$autoContent = @$setting[EntSettingKey::OPERATOR_AUTO_CONTENT];
			$messageBody = new WX_Message_Body();
			$messageBody->to_users = $this->message->from_user;
			$messageBody->type = 'text';
			$messageBody->content = $autoContent;
			$this->_replyMsg($messageBody);

			$this->_addDialog(2, $messageBody, '-4');//保存下行信息
			return true;
		}

		/**
	     * 如果用户还没有开启与客服的会话.
	     * 1，验证当前是否在客服服务时间范围内
	     * 2，如果不在客服服务时间范围内，
	     * 2.1，将消息存储到dialog表中
	     * 2.2，回复客户客服不在服务时间内的消息(使用5秒自动回复)
	     * 3，验证当前企业微信是否使用原始API接口,否：退出
	     */
		$checkSet = $this->checkOperatorSetting(); /*1*/
		if (! $checkSet) { /*2*/
			Logger::info("无客服时间消息user: " . $this->message->from_user . '; ent_id:' . $this->entId);
			$this->_addDialog(1, $this->message, '-2');//保存上行信息

			$setting = $this->getEntSetting($this->entId);
			$replyCon = @$setting[EntSettingKey::OPERATOR_UNLINE_CONTENT];
			$messageBody = new WX_Message_Body();
			$messageBody->to_users = $this->message->from_user;
			$messageBody->type = 'text';
			$messageBody->content = $replyCon;
			$this->_replyMsg($messageBody);

			$this->_addDialog(2, $messageBody, '-2');//保存下行信息
			return true;

		}

		//添加队里
		if (! $this->_addQueue()) {
			return false;
		}

		//进入队列提示消息
		$this->_intoQueueReplyMsg();

		return true;
	}

	/**
	 * 检测用户是否在会话中
	 * @param string $fromUser
	 * @return bool
	 */
	protected function _checkSession($fromUser)
	{
		$sql = "SELECT session_id, message_id FROM `wx_session` WHERE from_user = '%s'";
		try {
			$this->sqlError = false;
			$session = $this->entWxDb->getRow(sprintf($sql, $fromUser));
			if (! $session) {
				return false;
			}
			if ($session['message_id'] && $this->message->message_id && $session['message_id'] > $this->message->message_id) {
				$this->_updateSeesionMessage($session['session_id'], $this->message->message_id);
			}
			return true;
		} catch ( Exception $e ) {
			$this->sqlError = true;
			Logger::error('QueueManager->_checkSession() error: ' . $e->getMessage() . '; sql: ' . $sql);
			return false;
		}
	}

	/**
	 * 更新会话消息
	 * @param string $sessionId
	 * @param string $messageId
	 * @return void
	 */
	protected function _updateSeesionMessage($sessionId, $messageId)
	{
		$where = " session_id = '{$sessionId}'";
		$this->entWxDb->update('wx_session', $where, array('message_id' => $messageId));
	}

	/**
	 * 检查队列是否存在
	 * @param string $fromUser
	 * @return bool
	 */
	protected function _checkQueue($fromUser)
	{
		$sql = "SELECT id FROM `wx_queue` WHERE from_user = '%s'";
		try {
			$this->sqlError = false;
			$id = $this->entWxDb->getOne(sprintf($sql, $fromUser));
			return $id ? true : false;
		} catch ( Exception $e ) {
			$this->sqlError = true;
			Logger::error('QueueManager->_checkQueue() error: ' . $e->getMessage() . '; sql: ' . $sql);
			return false;
		}
	}

	/**
	 * 添加队列
	 * @param string $fromUser
	 * @param string $message_id
	 * @return bool
	 */
	protected function _addQueue()
	{
		$data = array (
				'from_user' => $this->message->from_user,
				'message_id' => $this->message->message_id,
				'status' => 1,
				'level' => '1',
				'group_id' => $this->_getTargetGroupId($this->message->from_user),
				'create_time' => date('Y-m-d H:i:s')
		);

		try {
			$this->sqlError = false;
			$result = $this->entWxDb->insert('wx_queue', $data);
			if (! $result) {
				Logger::error('QueueManager->_addQueue() error: add queue fail. sql:' . $this->entWxDb->getLastSql());
				return false;
			}
		} catch ( Exception $e ) {
			$this->sqlError = true;
			Logger::error('QueueManager->_addQueue() error: ' . $e->getMessage() . '; sql:' . $this->entWxDb->getLastSql());
			return false;
		}
		return true;
	}

	/**
	 * 获取目标客服组
	 */
	protected function _getTargetGroupId($fromUser)
	{
		try {
			$sql = "SELECT our.group_id, MIN(our.priority) FROM `wx_og_ug_relation` our"
				." INNER JOIN `wx_user_group_member` ugm ON ugm.ug_id = our.ug_id"
				." WHERE ugm.user = '%s' ORDER BY priority ASC";
			$group = $this->entWxDb->getRow(sprintf($sql, $fromUser));
		} catch ( Exception $e ) {
			Logger::error('QueueManager->_getTargetGroupId() error: ' . $e->getMessage() . '; sql:' . $this->entWxDb->getLastSql());
			return 0;
		}
		return $group ? (int) $group['group_id'] : 0;
	}

	/**
	 * 验证是否在客服服务时间内
	 */
	protected function checkOperatorSetting()
	{
		$setting = $this->getEntSetting($this->entId);
		$operOnlineSet = unserialize(@$setting[EntSettingKey::OPERATOR_ONLINE_TIME]);
		if (! $operOnlineSet) {
			return false;
		}
		//每天24小时服务
		if (isset($operOnlineSet['isAll']) && $operOnlineSet['isAll']) {
			return true;
		}
		$setWeeks = (array) @$operOnlineSet['weeks'];
		$week = date("w");
		//不再选定星期中
		if (! in_array($week, $setWeeks)) {
			return false;
		}
		$currTime = intval(date("H")) * 60 + (int) date("i");
		$setTimes = (array) $operOnlineSet['times'];
		//特定星期24小时服务
		if (@$setTimes['isAll']) {
			return true;
		}
		//没有设定服务时间,视为24小时
		if (empty($setTimes['time'])) {
			return true;
		}
		//验证是否在设定服务时间内
		foreach ($setTimes['timeInt'] as $t) {
			if ($currTime >= $t[0] && $currTime <= $t[1]) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 将消息保存到dialog中
	 */
	protected function saveToDialog()
	{
		if (! $this->message) {
			return;
		}
		$message = $this->message;
		$data = array (
				'session_id' => - 2, //不再客服服务期，直接导入的
				'message_id' => $message->message_id,
				'type' => 1, //客户上行数据
				'operator_id' => - 1, //系统
				'operator_name' => '系统',
				'wx_user' => $message->fromUser,
				'msgtype' => $message->type,
				'content' => $message->content,
				'media_id' => $message->media_id,
				'play_time' => $message->play_time,
				'media_url' => $message->media_url,
				'thumb_media_id' => $message->thumb_media_id,
				'title' => $message->title,
				'description' => $message->description,
				'message_created_at' => $message->created_at,
				'message_create_time' => date('Y-m-d H:i:s'),
				'create_time' => date('Y-m-d H:i:s')
		);

		if (is_object($message->location) && $message->location) {
			$data['location_x'] = $message->location->location_x;
			$data['location_y'] = $message->location->location_y;
			$data['scale'] = $message->location->scale;
			$data['label'] = $message->location->label;
		}

		try {
			$this->sqlError = false;
			$result = $this->entWxDb->insert('wx_dialog2', $data);
			if (false === $result) {
				Logger::error('QueueManager->saveToDialog() error; sql:' . $this->entWxDb->getLastSql());
				return false;
			}
		} catch ( Exception $e ) {
			$this->sqlError = true;
			Logger::error('QueueManager->saveToDialog() error: ' . $e->getMessage() . '; sql:' . $this->entWxDb->getLastSql());
			return false;
		}
		return true;
	}

	/**
	 * 回复客户客服不在服务时间内的消息(使用5秒自动回复)
	 */
	protected function replyOperUnlineMsg()
	{
		$setting = $this->getEntSetting($this->entId);
		$replyCon = @$setting[EntSettingKey::OPERATOR_UNLINE_CONTENT];
		if (! $replyCon) {
			Logger::warning("企业未设置当非客服服务时间内时的回复消息,ent_id:" . $this->entId);
			return false;
		}
		$textTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[%s]]></MsgType>
		<Content><![CDATA[%s]]></Content>
		<FuncFlag>0</FuncFlag>
		</xml>";
		$msgType = "text";
		$resultStr = sprintf($textTpl, $this->message->from_user, $this->message->ent_weixin, time(), $msgType, $replyCon);
		echo $resultStr;
		return true;
	}

	/**
	 * 检查企业进入人工客服设置
	 */
	protected function _checkAutoOperatorSetting()
	{
		$setting = $this->getEntSetting($this->entId);
		$isAuto = @$setting[EntSettingKey::OPERATOR_IS_AUTO];
		$autoKeyword = @$setting[EntSettingKey::OPERATOR_AUTO_KEYWORD];

		if ($isAuto) {
			return true;
		} else {
			if ('text' == $this->message->type) {
				/* if (false !== stripos(strtolower($this->message->content), strtolower($autoKeyword))) {
					return true;
				} */
				if (strtolower(tripslashes(trim($this->message->content))) == strtolower($autoKeyword)) {
					return true;
				}
			}
			return false;
		}
	}

	/**
	 * 添加对话记录
	 * @param int $type 信息类型1上行微信用户消息；2下行插件回复信息
	 * @param WX_Message|WX_Message_Body $message 保存数据
	 * @return bool
	 */
	private function _addDialog($type, $message, $sessionId)
	{
		if (! $type || (! $message || ! $message->type)) {
			Logger::error('添加dialog数据失败! 参数有误：type:'.$type.'; message:',$message);
			return false;
		}

		$msgType = $message->type;
		$params = array(
				'session_id' => $sessionId,//-2不再客服服务期，直接导入的;-4禁止自动进入队列
				'operator_id' => -1,
				'operator_name' => '系统',
				'create_time' => date('Y-m-d H:i:s'),
				'msgtype' => $msgType,
		);

		// 判断上下行
		if ($type == 1) {

			//检测数据是否存在
			$sql = "SELECT dialog_id FROM wx_dialog2 WHERE `message_id` = '%s'";
			try {
				$dialogId = $this->entWxDb->getOne(sprintf($sql, $message->message_id));
			} catch ( Exception $e ) {
				Logger::error('QueueManager->_addDialog() error: ' .$e->getMessage(). '; sql:'. $this->entWxDb->getLastSql());
				return false;
			}

			if ($dialogId) {
				return $dialogId;
			}

			$params['type'] = 1;
			$params['message_id'] = $message->message_id;
			$params['wx_user'] = $message->fromUser;
			$params['message_created_at'] = date('Y-m-d H:i:s', $message->created_at);
		} else {
			$params['type'] = 3;
			$params['message_id'] = -1;
			$params['wx_user'] = $message->to_users;
		}

		switch ($msgType) {
    		case 'text':
    			$params['content'] = $message->content;
    			break;
    		case 'location':
    			$params['location_x'] = $message->location->location_x;
    			$params['location_y'] = $message->location->location_y;
    			$params['scale'] = $message->location->scale;
    			$params['label'] = $message->location->label;
    			break;
			case 'news' :
				$params['articles'] = faddslashes(serialize($message->articles));
				break;
			case 'image' :
				$params['media_id'] = $message->media_id;
				if (1 == $type) {
					$params['media_url'] = $message->media_url;
				} else {
					$params['media_url'] = $message->attachment;
				}
				break;
			case 'voice' :
				$params['media_id'] = $message->media_id;
				if (1 == $type) {
					$params['format'] = $message->format;
				} else {
					$params['media_url'] = $message->attachment;
				}
				break;
			case 'video' :
				$params['media_id'] = $message->media_id;
				$params['thumb_media_id'] = $message->thumb_media_id;
				if (2 == $type) {
					$params['media_url'] = $message->attachment;
					$params['thumb_url'] = $message->thumb_path;
				}
				break;
			case 'music' :
				$params['title'] = $message->title;
				$params['description'] = $message->description;
				$params['thumb_url'] = $message->thumb_path;
				$params['thumb_media_id'] = $message->thumb_media_id;
				$params['music_url'] = $message->music_url;
				$params['hq_music_url'] = $message->hq_music_url;
				break;
			case 'link' :
				$params['title'] = $message->title;
				$params['description'] = $message->description;
				$params['url'] = $message->url;
    		default:
    			//return;
    	}

		try {
			return $this->entWxDb->insert('wx_dialog2', $params);
		} catch (Exception $e) {
			Logger::error('添加dialog数据失败! params:',$params);
			return false;
		}
	}

	/**
	 * 回复消息(使用5秒自动回复)
	 * @param WX_Message_Body $message
	 */
	protected function _replyMsg($messageBody)
	{
		if (! $messageBody) {
			Logger::warning("企业未设置回复消息,ent_id:" . $this->entId);
			return false;
		}
		$textTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[%s]]></MsgType>
		<Content><![CDATA[%s]]></Content>
		<FuncFlag>0</FuncFlag>
		</xml>";
		$msgType = $messageBody->type;
		$resultStr = sprintf($textTpl, $messageBody->to_users, $this->message->ent_weixin, time(), $msgType, $messageBody->content);
		echo $resultStr;
		return true;
	}

	/**
	 * 进入队列回复消息
	 */
	protected function _intoQueueReplyMsg()
	{
		Logger::info("进入队列回复消息user: " . $this->message->from_user . '; ent_id:' . $this->entId);
		//$this->_addDialog(1, $this->message, '-1');//保存上行信息

		$setting = $this->getEntSetting($this->entId);
		$replyCon = @$setting[EntSettingKey::OPERATOR_USER_INTO_QUEUE_CONTENT];
		$messageBody = new WX_Message_Body();
		$messageBody->to_users = $this->message->from_user;
		$messageBody->type = 'text';
		$messageBody->content = $replyCon;

		//linux下的PHP路径
		$php_cli_path = ConfigBase::PHP_CLI_PATH;
		//验证操作系统  方便测试
		$message_body = json_encode($messageBody, JSON_HEX_APOS);
		$dir_path = dirname(dirname(__FILE__)). "/Shells/CPUThread/MessageSendThread.shell.php";

		$cmd = "{$php_cli_path} {$dir_path} {$this->entId} '{$message_body}' >/tmp/timeline.log  &";
		Logger::info('QueueManager->_intoQueueReplyMsg cmd :' . $cmd);
		//执行linux命令
		system($cmd);
		//$out = popen($cmd, "r");
		//pclose($out);
	}
}
