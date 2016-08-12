<?php
/**
 * 欢迎词过滤器
 */
class WelcomeFilter
{
	private static $_message;
	private static $_openId;
	private static $_db;
	private static $_pluginDialogInfo;

	/**
	 * @name  主运行入口
	 * @param int $entId  企业ID
	 * @param WX_Message $message  微信消息对象
	 */
	public static function main($message)
	{
		Logger::debug("欢迎词插件 开始");
		$return = array (
				'status' => false,
				'message_body' => '',
				'plugin_key' => PluginKey::WELCOME,
				'plugin_info' => ''
		);

		if (! self::checkFilter($message)) {
			Logger::debug("欢迎词插件 未命中结束");
			return $return;
		}
		$return['status'] = true;

		$initResult = self::_init($message);

		$messageBody = self::_run();

		$return['plugin_info'] = self::$_pluginDialogInfo;
		$return['message_body'] = $messageBody;
		Logger::debug("欢迎词插件 命中结束");
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
			if ('event' == $message->msgType) {
				$eventType = strtolower($message->event->eventType);
				if (MessageEventType::SUBSCRIBE == $eventType && ! $message->event->eventKey) {
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
	private static function _init($message)
	{
		self::$_message = $message;
		self::$_openId = $message->fromUser;
		self::$_db = Factory::getDb();

		self::$_pluginDialogInfo = new PluginDialogInfo();
		self::$_pluginDialogInfo->key = PluginKey::WELCOME;

		return true;
	}

	/**
	 * 运行
	 * @return NULL|Ambigous <WX_Message_Body, NULL>
	 */
	private static function _run()
	{
		$welcome = self::_getWelcome();
		if (! $welcome) {
			Logger::error("欢迎词插件 获取信息为空");
			return null;
		}
		$messageBody = self::_genMessageBody($welcome);
		return $messageBody;
	}

	/**
	 * @name 获取事件数据
	 * @param string $evenKey 事件标识
	 * @return array
	 */
	private static function _getWelcome()
	{
		$sql = "SELECT * FROM `wx_welcome`";
		try {
			$welcome = self::$_db->getRow($sql);
		} catch ( Exception $e ) {
			Logger::error('欢迎词插件 获取数据失败 ', $e->getMessage() . "\n" . $e->getTraceAsString());
			return false;
		}
		return $welcome;
	}

	/**
	 * 生成MessageBody
	 * @param array $menu
	 * @return WX_Message_Body
	 */
	private static function _genMessageBody($welcome)
	{
		if (! $welcome ||!  is_array($welcome)) {
			return null;
		}
		//系统回复
		if ($welcome['type'] == 1) {
			$messageBody = new WX_Message_Body();
			$msgType = $welcome['msg_type'];
			$content = $welcome['content'];
			$messageBody->msgType = $msgType;
			$messageBody->toUser = self::$_openId;
			if ('text' == $msgType) {
				if (! $content) {
					Logger::error("欢迎词插件 文本类型数据内容为空");
					return null;
				}
				$messageBody->content = trim($content);
			} else {
				$material = self::_getMaterialById($welcome['material_id']);
				if (! $material || ! is_array($material)) {
					Logger::error("欢迎词插件 获取素材信息失败");
					return null;
				}

				switch ($msgType) {
					case 'news' :
						$monitorParam = array (
								MonitorParams::MATERIAL_ID => $welcome['material_id'],
								MonitorParams::USE_OAUTH => (int) @$welcome['use_oauth'],
								MonitorParams::MSG_SOURCE => 1
						);
						$articles = self::_addMonitor($material['articles'], $monitorParam);
						if (! $articles || ! is_array($articles)) {
							Logger::error("欢迎词插件 图文类型数据为空");
							return null;
						}
						foreach ($articles as $key => $value) {
							if (! is_array($value) || ! $value || ! @$value['title'] || ! @$value['description'] || ! @$value['url'] || ! @$value['picurl']) {
								Logger::error("欢迎词插件 图文类型数据格式错误", $articles);
								return null;
							}
						}
						$messageBody->articles = $articles;
						break;
					case 'music' :
						if (! $material['title'] || ! $material['description'] || ! $material['music_url'] || ! $material['thumb_url']) {
							Logger::error("欢迎词插件 音乐类型数据格式错误");
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
							Logger::error("欢迎词插件 媒体类型数据不存在");
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
		} else if ($welcome['type'] == 2) {

			if (! $welcome['url']) {
				Logger::error("欢迎词插件 type为2 url为空");
				return null;
			}

			$messageBody = ThirdPartyTools::pluginWelcomePush($welcome['url'], self::$_message);
			if (! $messageBody) {
				Logger::error("欢迎词插件 ThirdPartyTools::pluginWelcomePush() error: http_code:"
						. ThirdPartyTools::getHttpCode() . ' error:' . ThirdPartyTools::getError()
						. ' http_url:' . $welcome['url'] . ' response:', ThirdPartyTools::getResponse());
			}

			if ('news' == strtolower($messageBody->msgType)) {
				//动态回复图文监测
				$userMonitorData = ThirdPartyTools::getMonitorData();
				$monitorParam = array (
						MonitorParams::MATERIAL_ID => (string) @$userMonitorData['material_id'],
						MonitorParams::USE_OAUTH => (int) @$userMonitorData['use_oauth'],
						MonitorParams::MSG_SOURCE => 2
				);
				$messageBody->articles = self::_addMonitor($messageBody->articles, $monitorParam);
			}
		} else {
			Logger::error("欢迎词插件 type 错误：" . $welcome['type']);
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
			return array ();
		}
		$material = array ();
		$sql = "SELECT * FROM wx_material WHERE id = %d";
		try {
			$material = self::$_db->getRow(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error('欢迎词插件 获取素材信息错误', $e->getMessage() . "\n" . $e->getTraceAsString());
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
			return array ();
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
		$list = array ();
		$sql = "SELECT * FROM `wx_material_news` WHERE `is_deleted` = 0 AND material_id = %d ";
		try {
			$list = self::$_db->getAll(sprintf($sql, $id));
		} catch ( Exception $e ) {
			Logger::error('欢迎词插件 获取图文素材明细信息错误', $e->getMessage() . "\n" . $e->getTraceAsString());
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

		$articles = MonitorTools::genWelcomeNewsMonitor($articles, $monitorParam, self::$_openId, self::$_message);

		return $articles;
	}
}