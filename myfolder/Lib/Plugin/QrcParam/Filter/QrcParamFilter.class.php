<?php
/**
 * 参数二维码过滤器
 */
class QrcParamFilter
{
	private static $_message;
	private static $_openId;
	private static $_db;
	private static $_eventType;
	private static $_eventKey;
	private static $_sceneId;
	private static $_qimgId;
	private static $_qrcId;
	private static $_qrcAppImageInfo;
	private static $_appType;

	private static $_materialSource;

	private static $_pluginDialogInfo;

	/**
	 * @name  主运行入口
	 * @param int $entId  企业ID
	 * @param WX_Message $message  微信消息对象
	 */
	public static function main($message)
	{
		Logger::debug("带参数二维码插件 开始");
		$return = array (
				'status' => false,
				'message_body' => '',
				'plugin_key' => PluginKey::QRC_PARAM,
				'plugin_info' => ''
		);
		if (! self::checkFilter($message)) {
			Logger::debug("带参数二维码插件 未命中结束");
			return $return;
		}
		$return['status'] = true;

		$initResult = self::_init($message);

		$messageBody = self::_run();
		$return['plugin_info'] = self::$pluginDialogInfo;

		$return['message_body'] = $messageBody;
		Logger::debug("带参数二维码插件 命中结束");
		return $return;
	}

	/**
	  * 检测过滤器
	  * @param WX_Message $message
	  * @return bool
	  */
	public static function checkFilter($message)
	{
		if ($message && is_object($message)) {
			if (is_object($message->event) && isset($message->event->eventKey) && $message->event->eventKey) {
				if (strtolower($message->event->eventType) == MessageEventType::SUBSCRIBE
						|| strtolower($message->event->eventType) == MessageEventType::SCAN) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 初始化数据
	 * @param int $entId
	 * @param WX_Message $message
	 */
	private static function _init($entId, $message)
	{
		self::$_message = $message;
		self::$_openId = $message->fromUser;
		self::$_eventType = strtolower($message->event->eventType);
		self::$_eventKey = $message->event->eventKey;
		self::$_db = Factory::getDb();

		if (self::$_eventType == MessageEventType::SUBSCRIBE) {
			self::$_sceneId = substr($message->event->eventKey, strlen('qrscene_'));
		} else if (self::$_eventType == MessageEventType::SCAN) {
			self::$_sceneId = $message->event->eventKey;
		}
		self::$_pluginDialogInfo = new PluginDialogInfo();
		self::$_pluginDialogInfo->key = PluginKey::QRC_PARAM;
		self::$_pluginDialogInfo->id = self::$_sceneId;

		return true;
	}

	/**
	 * 运行
	 * @return string|Ambigous <string, WX_Message_Body, NULL>
	 */
	private static function _run()
	{
		$messageBody = '';
		$scene = self::_getQrcSceneById();

		if (! $scene) {
			Logger::debug("带参数二维码插件 获取临时二维码场景信息为空");
			return $messageBody;
		}
		$qrcId = $scene['qrc_id'];
		$qimgId = $scene['qimg_id'];
		$memberId = $scene['member_id'];

		self::$_qrcId = $qrcId;
		self::$_qimgId = $qimgId;

		$qrcApp = self::_getQrcAppById($qrcId);
		if (! $qrcApp) {
			Logger::debug("带参数二维码插件 获取二维码应用信息为空");
			return $messageBody;
		}

		$qrcAppImage = self::_getQrcAppImageById($qimgId);
		if (! $qrcAppImage) {
			Logger::debug("带参数二维码插件 获取二维码图片信息为空");
			return $messageBody;
		}
		self::$_appType = $appType = $qrcApp['app_type'];
		$noticeUrl = $qrcApp['app_notice_url'];
		$groupId = $qrcAppImage['group_id'];

		self::$_qrcAppImageInfo = $qrcAppImage;

		switch ($appType) {
			case 7 : //带参数二维码
				$messageBody = self::_bind($qrcId, $qimgId, $memberId, $noticeUrl, $groupId);
				break;
			case 8 : //用户来源
				$messageBody = self::_userSource($qimgId, $noticeUrl, $groupId);
				break;
		}
		return $messageBody;
	}

	/**
	 * 绑定操作
	 */
	private static function _bind($qrcId, $qimgId, $memberId, $noticeUrl, $groupId)
	{
		//绑定类型 0：没有任何响应；1：未关注用户绑定；2：已关注用户绑定；
				//3：用户已经绑定过其他二维码；4：二维码已经被其他用户绑定过；5：重复绑定
		$type = 0;
		//微信用户是否绑定过
		$userBindByUser = self::_getUserBindByUser();
		//已经绑定过 检查是否是扫描的二维码
		if ($userBindByUser) {
			if ($userBindByUser['member_id'] == $memberId) {
				$type = 5;
			} else {
				$type = 3;
			}
		}
		//扫描的二维码是否被绑定过
		$userBindByMember = self::_getUserBindByMemberId($memberId);
		//已经被绑定过 检查是否是当前微信用户
		if ($userBindByMember) {
			if ($userBindByUser['openid'] == self::$_openId) {
				$type = 5;
			} else {
				$type = 4;
			}
		}
		//微信用户和扫描的二维码没有绑定 绑定关系
		if (! $userBindByUser && ! $userBindByMember) {
			$bindResult = self::_bindUser($memberId, $qrcId);
			if ($bindResult) {
				if (self::$_eventType == MessageEventType::SUBSCRIBE) {
					$type = 1;
				} else {
					$type = 2;
				}
				//绑定成功通知企业
				self::_notice($noticeUrl, $memberId);
				//是否有自动分组
				if ($groupId) {
					if (! self::_bindUserGroup($groupId)) {
						Logger::error("带参数二维码插件 绑定用户组失败");
					}
				}
			} else {
				Logger::error("带参数二维码插件 绑定用户失败");
			}
		}
		Logger::debug("带参数二维码插件 状态：". $type);
		$messageBody = self::_getMessageBodyByType($type, $qimgId);
		return $messageBody;
	}

	/**
	 * 用户来源操作
	 */
	private static function _userSource($qimgId, $noticeUrl, $groupId)
	{
		//1:关注；2:扫描；
		$type = 0;
		if (self::$_eventType == MessageEventType::SUBSCRIBE) {
			$type = 1;
		} else {
			$type = 2;
		}

		//是否有自动分组
		if ($groupId) {
			if (! self::_bindUserGroup($groupId)) {
				Logger::error("带参数二维码插件 绑定用户组失败");
			}
		}
		//绑定成功通知企业
		self::_notice($noticeUrl);

		$messageBody = self::_getMessageBodyByType($type, $qimgId);
		return $messageBody;
	}

	/**
	 * 获取临时二维场景信息
	 * @return mixed|boolean|unknown
	 */
	private static function _getQrcSceneById()
	{
		$sql = "SELECT * FROM `wx_qrc_scene` WHERE scene_id = %d";
		try {
			return self::$_db->getRow(sprintf($sql, self::$_sceneId));
		} catch (Exception $e) {
			Logger::error ("带参数二维码插件 获取临时二维场景信息失败： ", $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 获取二维码图片信息
	 */
	private static function _getQrcAppImageById($qimgId)
	{
		$sql = "SELECT * FROM `wx_qrc_app_image` WHERE qimg_id = %d";
		try {
			return self::$_db->getRow(sprintf($sql, $qimgId));
		} catch (Exception $e) {
			Logger::error ("带参数二维码插件 获取二维码图片信息失败： ", $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 获取二维码应用
	 */
	private static function _getQrcAppById($qrcId)
	{
		$sql = "SELECT * FROM `wx_qrc_app` WHERE qrc_id = %d";
		try {
			return self::$_db->getRow(sprintf($sql, $qrcId));
		} catch (Exception $e) {
			Logger::error ("带参数二维码插件 获取二维码应用失败： ", $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 通过openId获取用户绑定信息
	 * @return mixed|boolean
	 */
	private static function _getUserBindByUser()
	{
		$sql = "SELECT * FROM `wx_member_bind` WHERE openid = '%s'";
		try {
			return self::$_db->getRow(sprintf($sql, self::$_openId));
		} catch (Exception $e) {
			Logger::error ("带参数二维码插件 通过openId获取用户绑定信息失败： ", $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 通过memberId获取用户绑定信息
	 * @return mixed|boolean
	 */
	private static function _getUserBindByMemberId($memberId)
	{
		$sql = "SELECT * FROM `wx_member_bind` WHERE member_id = '%s'";
		try {
			return self::$_db->getRow(sprintf($sql, $memberId));
		} catch (Exception $e) {
			Logger::error ("带参数二维码插件 通过memberId获取用户绑定信息失败： ", $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
		}
	}

	/**
	 * 绑定用户
	 * @param string $memberId
	 * @param int $qrcId 二维码应用id
	 * @param string $cid
	 * @param Object $member
	 * @return bool
	 */
	private function _bindUser($memberId, $qrcId)
	{
		$data = array(
				'openid' => self::$_openId,
				'member_id' => $memberId,
				'qrc_id' => $qrcId,
				'bind_type' => 2,
				'create_time' => date("Y-m-d H:i:s"),
		);
		try {
			$is = self::$_db->insert("wx_member_bind", $data);
			return $is === false ? false : true;
		} catch (Exception $e) {
			Logger::error('带参数二维码插件 绑定用户error :'.$e->getMessage(), self::$_db->getLastSql());
			return false;
		}
	}

	/**
	 * 绑定用户分组
	 * @return mixed|boolean
	 */
	private static function _bindUserGroup($groupId)
	{
		$count = 0;
		$sql = "SELECT COUNT(*) FROM `wx_user_group_member` WHERE openid = '%s' AND ug_id = %d";
		try {
			$count = self::$_db->getOne(sprintf($sql, self::$_openId, $groupId));
		} catch (Exception $e) {
			Logger::error ("带参数二维码插件 绑定用户分组失败： ", $e->getMessage(). "\n" . $e->getTraceAsString());
		}

		if ($count > 0) {
			return true;
		}

		$data = array(
				'openid' => self::$_openId,
				'ug_id' => $groupId,
				'create_time' => date("Y-m-d H:i:s"),
		);
		try {
			return self::$_db->insert("wx_user_group_member", $data);
		} catch (Exception $e) {
			Logger::error('带参数二维码插件 绑定用户分组失败:'.$e->getMessage(), self::$_db->getLastSql());
			return false;
		}
	}

	/**
	 * 通知企业
	 */
	private static function _notice($noticeUrl, $memberId = 0)
	{
		if (! $noticeUrl) {
			Logger::debug("通知企业 url 为空");
			return null;
		}
		$postData = array(
				'openid' => self::$_openId,
				'uid' => $memberId,
				'subscribe' => 1,
				'event_type' => self::$_eventType,
				'event_kye' => self::$_eventKey,
				'qrc_app_id' => isset(self::$_qrcAppImageInfo['qrc_id']) ? self::$_qrcAppImageInfo['qrc_id'] : '',
				'media_name' => isset(self::$_qrcAppImageInfo['media_name']) ? self::$_qrcAppImageInfo['media_name'] : '',
				'media_id' => isset(self::$_qrcAppImageInfo['media_id']) ? self::$_qrcAppImageInfo['media_id'] : '',
		);
		$authParam = getAuthQueryData();
		$postData = array_merge($authParam, $postData);

		$postData = json_encode($postData, JSON_HEX_APOS);

		$phpCliPath = C('PHP_CLI_PATH');
		$dirPath = LIB_PATH . "/Shell/WXAppThread/QrcParamFilterNotice.shell.php";

		$cmd = "{$phpCliPath} {$dirPath} '{$noticeUrl}' '{$postData}' &";
		//开启进程
		$out = popen($cmd, "r");
		Logger::info("通知企业 脚本执行命令:status:'".!!$out, $cmd);
		pclose($out);
	}

	/**
	 * 通过类型获取响应信息
	 * @param int $type
	 */
	private static function _getMessageBodyByType($type, $qimgId)
	{
		self::$_materialSource = MonitorParams::MOUDEL_QRCODE_PARAM;
		$message = '';
		switch ($type) {
			case 0:
				break;
			case 1: //未关注用户绑定，绑定成功获取返回消息，如果为空，返回欢迎词消息
				$message = self::_getMessageByQimgIdAndMsgKey($qimgId, 1);
				break;
			case 2: //已关注用户绑定
				$message = self::_getMessageByQimgIdAndMsgKey($qimgId, 2);
				break;
			case 3: //已经绑定其他二维码
			case 4: //二维码已经被其微信用过绑定
				$message = self::_getMessageByQimgIdAndMsgKey($qimgId, 3);
				break;
			case 5: //重复绑定
				$message = self::_getMessageByQimgIdAndMsgKey($qimgId, 4);
				break;
		}
		$messageBody = self::_genMessageBody($message);
		return $messageBody;
	}

	/**
	 * 获取二维码绑定响应消息
	 * @param int $qimgId
	 * @param int $msgKey
	 * @return mixed|boolean
	 */
	private static function _getMessageByQimgIdAndMsgKey($qimgId, $msgKey)
	{
		$message = null;
		$sql = "SELECT * FROM `wx_qrc_app_msg` WHERE qimg_id = %d AND qrc_msg_key = %d";
		try {
			$message =  self::$_db->getRow(sprintf($sql, $qimgId, $msgKey));
		} catch (Exception $e) {
			Logger::error ("带参数二维码插件 获取二维码绑定响应消息失败：", $e->getMessage(). "\n" . $e->getTraceAsString());
		}

		if (! $message && 1 == $msgKey) {
			self::$_materialSource = MonitorParams::MOUDEL_WELCOME;
			try {
				$sql = "SELECT * FROM `wx_welcome`";
				$welcome =  self::$_db->getRow($sql);
			} catch ( Exception $e ) {
				Logger::error("带参数二维码插件 获取欢迎词推送数据sql失败：", $e->getMessage(). "\n" . $e->getTraceAsString());
			}

			if ($welcome) {
				$message['type'] = $welcome['type'];
				$message['msg_type'] = $welcome['msg_type'];
				$message['content'] = $welcome['content'];
				$message['material_id'] = $welcome['material_id'];
				$message['use_oauth'] = $welcome['use_oauth'];
				$message['url'] = $welcome['url'];
				$message['is_welcome'] = 1;
			}
		}
		return $message;
	}

	/**
	 * 生成MessageBody
	 * @param array $message
	 * @return WX_Message_Body
	 */
	private static function _genMessageBody($message)
	{
		//Logger::debug("带参数二维码插件 _genMessageBody", $message);
		if (! $message || ! is_array($message)) {
			return null;
		}

		//系统回复
		if ($message['type'] == 1) {
			$messageBody = new WX_Message_Body();
			$msgType = $message['msg_type'];
			$content = $message['content'];
			$messageBody->msgType = $msgType;
			$messageBody->toUser = self::$_openId;
			if ('text' == $msgType) {
				if (! $content) {
					Logger::error("带参数二维码插件 文本类型数据内容为空");
					return null;
				}
				$messageBody->content = trim($content);
			} else {
				$material = self::_getMaterialById($message['material_id']);
				if (! $material || ! is_array($material)) {
					Logger::error("带参数二维码插件 获取素材信息失败");
					return null;
				}

				switch ($msgType) {
					case 'news' :
						$monitorParam = array (
							MonitorParams::MATERIAL_ID => $message['material_id'],
							MonitorParams::USE_OAUTH => (int) @$message['use_oauth'],
							MonitorParams::MSG_SOURCE => 1,
							MonitorParams::MODUEL_ID => self::$_qimgId,
							MonitorParams::QRC_APP_ID => self::$_qrcId,
							MonitorParams::MATERIAL_SOURCE => self::$_materialSource,
						);
						$articles = self::_addMonitor($material['articles'], $monitorParam);
						if (! $articles || ! is_array($articles)) {
							Logger::error("带参数二维码插件 图文类型数据为空");
							return null;
						}
						foreach ($articles as $key => $value) {
							if (! is_array($value) || ! $value || ! @$value['title'] || ! @$value['description'] || ! @$value['url'] || ! @$value['picurl']) {
								Logger::error("带参数二维码插件 图文类型数据格式错误", $articles);
								return null;
							}
						}
						$messageBody->articles = $articles;
						break;
					case 'music' :
						if (! $material['title'] || ! $material['description'] || ! $material['music_url'] || ! $material['thumb_url']) {
							Logger::error("带参数二维码插件 音乐类型数据格式错误");
							return null;
						}
						$messageBody->title = $material['title'];
						$messageBody->description = $material['description'];
						$messageBody->musicUrl = $material['music_url'];
						$messageBody->thumbPath = $material['thumb_url'];
						$messageBody->hqMusicUrl = @$material['hq_music_url'];
						break;
					case 'voice' :
					case 'image' :
					case 'video' :
						if (! $material['media_url']) {
							Logger::error("带参数二维码插件 媒体类型数据不存在");
							return null;
						}
						$messageBody->attachment = $material['media_url'];
						if ('video' == $msgType) {
							$messageBody->title = $material['title'];
							$messageBody->description = $material['description'];
						}
						break;
				}
			}
		} else if ($message['type'] == 2) {

			if (! $message['url']) {
				Logger::error("带参数二维码插件 type为2 url为空");
				return null;
			}

			if (isset($message['is_welcome']) && ! empty($message['is_welcome'])) {
				$methodName = 'pluginWelcomePush';
			} else {
				$methodName = 'pluginQrcParamPush';
			}

			$messageBody = call_user_func(array(ThirdPartyTools, $methodName), $message['url'], self::$_message);
			if (! $messageBody) {
				Logger::error("带参数二维码插件 ThirdPartyTools::{$methodName}() error: http_code:"
						. ThirdPartyTools::getHttpCode() . ' error:' . ThirdPartyTools::getError()
						. ' http_url:' . $message['url'] . ' response:', ThirdPartyTools::getResponse());
			}

			if ('news' == strtolower($messageBody->msgType)) {
				//动态回复图文监测
				$userMonitorData = ThirdPartyTools::getMonitorData();
				$monitorParam = array (
						MonitorParams::MATERIAL_ID => (string) @$userMonitorData['material_id'],
						MonitorParams::USE_OAUTH => (int) @$userMonitorData['use_oauth'],
						MonitorParams::MSG_SOURCE => 2,
						MonitorParams::MODUEL_ID => self::$_qimgId,
						MonitorParams::QRC_APP_ID => self::$_qrcId,
						MonitorParams::MATERIAL_SOURCE => self::$_materialSource,
				);
				$messageBody->articles = self::_addMonitor($messageBody->articles, $monitorParam);
			}

		} else {
			Logger::error("带参数二维码插件 type 错误：" . $message['type']);
			return null;
		}
		return $messageBody;
	}

	/**
	 * 获取素材信息根据ID
	 * @param int $id
	 * @return
	 */
	private static function _getMaterialById($id)
	{
		if (! $id) {
			return array();
		}
		$material = array();
		$sql = "SELECT * FROM wx_material WHERE id = %d";
		try {
			$material = self::$_db->getRow(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error('带参数二维码插件 获取素材信息根据ID错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return self::_parseMaterial($material);
	}

	/**
	 * 解析素材
	 * @param array $material
	 * @return array
	 */
	private static function _parseMaterial($material)
	{
		if (! $material) {
			return array();
		}
		$material['material_id'] = $material['id'];
		switch ($material['type']) {
			case 'news' :
				$material['articles'] = self::_getNewsDetailById($material['id']);
				break;
			case 'music' :
				$tmp = unserialize($material['articles']);
				$material['title'] = @$tmp['title'];
				$material['description'] = @$tmp['description'];
				$material['thumb_url'] = @$tmp['thumb_url'];
				$material['music_url'] = @$tmp['music_url'];
				$material['hq_music_url'] = @$tmp['hq_music_url'];
				break;
			case 'image' :
			case 'voice' :
			case 'video' :
				$material['media_url'] = $material['media_url'];
				break;
		}
		return $material;
	}

	/**
	 * 获取图文明细
	 * @param int $id
	 * @return array
	 */
	private static function _getNewsDetailById($id)
	{
		$list = array();
		$sql = "SELECT * FROM `wx_material_news` WHERE `is_deleted` = 0 AND material_id = %d ";
		try {
			$list = self::$_db->getAll(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error('带参数二维码插件 获取图文明细错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $list;
	}

	/**
	 * 添加图文监测数据
	 * @param array $articles
	 * @param array $monitorParam
	 * @return array
	 */
	private static function _addMonitor($articles, $monitorParam)
	{
		if (! is_array($articles) || !self::$_openId || ! $monitorParam || ! @$monitorParam[MonitorParams::MATERIAL_ID]) {
			Logger::error("欢迎词插件 图文数据添加监测，数据格式错误：", array('articles' => $articles, 'monitorParam' => $monitorParam));
			return $articles;
		}

		$articles = MonitorTools::genQrcParamNewsMonitor($articles, $monitorParam, self::$_openId, self::$_message);

		return $articles;
	}
}
