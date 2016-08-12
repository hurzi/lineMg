<?php
/**
 * 消息处理类
 */
class MessageManager extends Manager
{
	/**
	 * 消息保存
	 */
	public function save()
	{
		if (! $this->message || !is_object($this->message)) {
			Logger::error('MessageManager->save() error: messsage is not object or is null.', $this->message);
			return false;
		}
		//保存用户(另开线程)
		//$this->_saveFromUser($this->message->fromUser, $this->message->createTime);
		//直接保存
		$this->_saveUserCurr($this->message->fromUser, $this->message->createTime);
		
		if ('event' == $this->message->msgType) {
			$result = $this->_saveMessageEvent($this->message);
			//如果是事件消息忽略保存消息失败继续执行
			return true;
		} else {
			$result = $this->_saveMessage($this->message);

			if ($result) {
				//下载附件
				if (in_array($this->message->msgType, array('voice', 'video'))) {
					if (! class_exists('WeiXinApiCore', false)) {
						include_once LIB_PATH . '/../AbcPHP/API/WeiXinApiCore.class.php';
					}

					$appId = C('APP_ID');
					$appSecret = C('APP_SECRET');

					$token = getToken($appId, $appSecret);
					$wxClient = WeiXinApiCore::getClient($appId, $appSecret, $token);
					$mediaUrl = '';
					$thumbMediaUrl = '';
					switch ($this->message->msgType) {
						/* case 'image' :
							$mediaUrl = $this->message->mediaUrl;
							break; */
						case 'voice' :
							$mediaUrl = $wxClient->getMediaUrl($this->message->mediaId);
							break;
						case 'video' :
							$mediaUrl = $wxClient->getMediaUrl($this->message->mediaId);
							$thumbMediaUrl = $wxClient->getMediaUrl($this->message->thumbMediaId);
							break;
					}
					if ($mediaUrl) {
						$data = array (
								'media_url' => @$mediaUrl,
								'thumb_media_url' => @$thumbMediaUrl,
								'media_id' => @$this->message->mediaId,
								'thumb_media_id' => @$this->message->thumbMediaId
						);
						//通知附件下载
						$this->_noticeMediaDownload($this->message->msgId, $this->message->msgType, $data);
					}
				}
			}
		}
		return $result;
	}

	/**
	 * 添加对话记录
	 * @param int $type 信息类型1上行微信用户消息；2下行插件回复信息
	 * @param string $pluginKey 插件key
	 * @param WX_Message | WX_Message_Body $message 保存数据
	 * @param PluginDialogInfo $pluginInfo
	 * @return bool
	 */
	public function addDialog($type, $pluginKey, $message, $pluginInfo = null, $dialogType = 4)
	{
		if (! $type || ! $pluginKey || (! $message || ! $message->msgType)) {
			Logger::error('添加dialog数据失败! 参数有误：type:' . $type . '; plugin_key:' . $pluginKey . '; message:', $message);
			return false;
		}

		$msgType = $message->msgType;
		$data = array (
				'session_id' => - 1,
				'operator_id' => - 1,
				'operator_name' => '系统',
				'create_time' => date('Y-m-d H:i:s'),
				'msg_type' => $msgType,
				'plugin_key' => $pluginKey
		);
		if ($pluginInfo) {
			$data['plugin_info'] = stripcslashes(json_encode($pluginInfo));
		}

		// 判断上下行
		if ($type == 1) {
			//检测数据是否存在
			$sql = "SELECT dialog_id FROM `wx_dialog` WHERE `msg_id` = '%s'";
			try {
				$dialogId = $this->db->getOne(sprintf($sql, $message->msgId));
			} catch ( Exception $e ) {
				Logger::error('MessageManager->addDialog() error: ' . $e->getMessage() . '; sql:' . $this->db->getLastSql());
				return false;
			}

			if ($dialogId) {
				return $dialogId;
			}

			$data['type'] = 1;
			$data['msg_id'] = $message->msgId;
			$data['openid'] = $message->fromUser;
			$data['message_created_at'] = date('Y-m-d H:i:s', $message->createTime);
		} else {
			$data['type'] = $dialogType;
			$data['msg_id'] = -1;
			$data['openid'] = $message->toUser;
		}

		switch ($msgType) {
			case 'text' :
				$data['content'] = faddslashes($message->content);
				break;
			case 'location' :
				$data['location_x'] = $message->location->locationX;
				$data['location_y'] = $message->location->locationY;
				$data['scale'] = faddslashes($message->location->scale);
				$data['label'] = $message->location->label;
				break;
			case 'news' :
				$data['articles'] = faddslashes(serialize($message->articles));
				break;
			case 'image' :
				$data['media_id'] = $message->mediaId;
				if (1 == $type) {
					$data['media_url'] = faddslashes($message->mediaUrl);
				} else {
					$data['media_url'] = faddslashes($message->attachment);
				}
				break;
			case 'voice' :
				$data['media_id'] = $message->mediaId;
				if (1 == $type) {
					$data['format'] = $message->format;
					$data['recognition'] = faddslashes($message->recognition);
				} else {
					$data['media_url'] = faddslashes($message->attachment);
				}
				break;
			case 'video' :
				$data['media_id'] = $message->mediaId;
				$data['thumb_media_id'] = $message->thumbMediaId;
				if (2 == $type) {
					$data['media_url'] = faddslashes($message->attachment);
					$data['thumb_url'] = faddslashes($message->thumbPath);
				}
				break;
			case 'music' :
				$data['title'] = faddslashes($message->title);
				$data['description'] = faddslashes($message->description);
				$data['thumb_url'] = faddslashes($message->thumbPath);
				$data['thumb_media_id'] = $message->thumbMediaId;
				$data['music_url'] = faddslashes($message->musicUrl);
				$data['hq_music_url'] = faddslashes($message->hqMusicUrl);
				break;
			case 'link' :
				$data['title'] = faddslashes($message->title);
				$data['description'] = faddslashes($message->description);
				$data['url'] = faddslashes($message->url);
			default :
				//return;
		}

		try {
			return $this->db->insert('wx_dialog', $data);
		} catch ( Exception $e ) {
			Logger::error('MessageManager->addDialog(): 添加dialog数据失败! ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 更新消息状态
	 * @param string $msgId
	 * @param int $status
	 */
	public function updateStatus($msgId, $status = 1)
	{

		if (! $msgId) {
			Logger::error('MessageManager->updateStatus() error: msg_id is null.');
			return false;
		}

		try {
			$this->sqlError = false;
			$where = "msg_id = '{$msgId}'";
			$set = array (
					'status' => $status
			);
			$result = $this->db->update('wx_message', $where, $set);
			if (false === $result) {
				Logger::error('MessageManager->updateStatus() error: update message fail. sql:' . $this->db->getLastSql());
				return false;
			}
		} catch ( Exception $e ) {
			$this->sqlError = true;
			Logger::error('MessageManager->updateStatus() error: ' . $e->getMessage() . '; sql:' . $this->db->getLastSql());
			return false;
		}
		return true;
	}

	/**
	 * 保存事件消息操作
	 * @param WX_Message $message
	 * @return bool
	 */
	protected function _saveMessageEvent($message)
	{
		if (! is_object($message->event) || ! $message->event) {
			Logger::error('MessageManager->_saveMessageEvent() error: message event is not object or is null.', $message);
			return false;
		}

		$data = array (
				'openid' => (string) $message->fromUser,
				'event_type' => (string) $message->event->eventType,
				'event_key' => (string) $message->event->eventKey,
				'latitude' => (string) $message->event->latitude,
				'longitude' => (string) $message->event->longitude,
				'msg_id' => $message->event->message_id,
				'status' => faddslashes($message->event->status),
				'total_count' => (int) $message->event->total_count,
				'filter_count' => (int) $message->event->filter_count,
				'sent_count' => (int) $message->event->sent_count,
				'error_count' => (int) $message->event->error_count,
				'created_at' => (int) $message->createTime,
				'create_time' => date('Y-m-d H:i:s'),
		);

		try {
			$this->sqlError = false;
			$result = $this->db->insert('wx_message_event', $data);
			if (false === $result) {
				Logger::error('MessageManager->_saveMessageEvent() error: save event message fail. sql:'
						. $this->db->getLastSql());
				return false;
			}
		} catch ( Exception $e ) {
			$this->sqlError = true;
			Logger::error('MessageManager->_saveMessageEvent() error: ' . $e->getMessage()
				. '; sql:' . $this->db->getLastSql());
			return false;
		}
		return true;
	}

	/**
	 * 保存消息操作
	 * @param WX_Message $message
	 * @return bool
	 */
	protected function _saveMessage($message)
	{
		$data = array (
				'msg_id' => $message->msgId,
				'openid' => $message->fromUser,
				'msg_type' => $message->msgType,
				'content' => faddslashes($message->content),
				'media_id' => $message->mediaId,
				'media_url' => faddslashes($message->mediaUrl),
				'thumb_media_id' => $message->thumbMediaId,
				'title' => faddslashes($message->title),
				'description' => faddslashes($message->description),
				'url' => faddslashes($message->url),
				'status' => - 1,
				'format' => $message->format,
				'recognition' => faddslashes($message->recognition),
				'created_at' => $message->createTime,
				'create_time' => date('Y-m-d H:i:s'),
		);

		if (is_object($message->location) && $message->location) {
			$data['location_x'] = $message->location->locationX;
			$data['location_y'] = $message->location->locationY;
			$data['scale'] = $message->location->scale;
			$data['label'] = faddslashes($message->location->label);
		}

		try {
			$this->sqlError = false;
			$result = $this->db->insert('wx_message', $data, true);
			if (false === $result) {
				Logger::error('MessageManager->_saveMessage() error: save message fail. sql:'
						. $this->db->getLastSql());
				return false;
			}
		} catch ( Exception $e ) {
			$this->sqlError = true;
			Logger::error('MessageManager->_saveMessage() error: '
					. $e->getMessage() . '; sql:' . $this->db->getLastSql());
			return false;
		}
		return true;
	}
	
	/**
	 * 立即获取用户
	 */
	protected function _saveUserCurr($openid,$msgTime){
		$eventType = $this->message->event ? $this->message->event->eventType : '';
		include_once LIB_PATH.'/Common/WxUserFetcher.class.php';
		$userFetcher = new WxUserFetcher($openid, $eventType, $msgTime);
		$userFetcher->run();
	}

	/**
	 * 保存发送消息用户
	 * 另开线程处理
	 * @param string $openId
	 * @param string $msgTime
	 */
	protected function _saveFromUser($openId, $msgTime)
	{
		$phpCliPath = C('PHP_CLI_PATH');
		$dirPath = LIB_PATH . "/Shell/WXAppThread/WxUserFetcher.shell.php";
		$eventType = $this->message->event ? $this->message->event->eventType : '';
		$cmd = "{$phpCliPath} {$dirPath} '{$openId}' '{$eventType}' '{$msgTime}' &";

		Logger::info("MessageManager->_saveFromUser() 保存微信用户 脚本执行命令:\n'" . $cmd);
		//开启进程
		$out = popen($cmd, "r");
		pclose($out);
	}

	/**
	 * 通知附件下载
	 *
	 * @param string $msgId
	 * @param string $msgType
	 * @param array $data
	 * array(
	 *	'media_url' => 媒体URL
	 *	'thumb_media_url' => 缩略图URL
	 *  'media_id' => 媒体ID
	 *	'thumb_media_id' => 缩略图ID
	 *	);
	 */
	protected function _noticeMediaDownload($msgId, $msgType, $data)
	{
		$phpCliPath = C('PHP_CLI_PATH');
		$dirPath = LIB_PATH . "/Shell/WXAppThread/MediaDownload.shell.php";

		$postData = array (
				$msgId,
				'"' . $msgType . '"',
				'"' . str_replace('"', '\"', $data['media_url']) . '"',
				'"' . $data['media_id'] . '"',
				'"' . str_replace('"', '\"', $data['thumb_media_url']) . '"',
				'"' . $data['thumb_media_id'] . '"'
		);
		$paramStr = implode(" ", $postData);
		$cmd = "{$phpCliPath} {$dirPath} {$paramStr} > /tmp/media_download.log &";
		Logger::info("MessageManager->_noticeMediaDownload() media download 脚本执行命令\n" . $cmd);
		//开启进程
		$out = popen($cmd, "r");
		pclose($out);
	}
}