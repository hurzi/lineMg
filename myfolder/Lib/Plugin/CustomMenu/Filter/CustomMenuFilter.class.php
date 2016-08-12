<?php
/**
 * 自定义菜单过滤器
 */
class CustomMenuFilter
{
	private static $_message;
	private static $_openId;
	private static $_db;
	private static $_eventKey;

	private static $_pluginDialogInfo;

	/**
	 * @name  主运行入口
	 * @param WX_Message $message  微信消息对象
	 */
	public static function main($message)
	{
		Logger::debug("自定义菜单插件 开始");
		$return = array (
				'status' => false,
				'message_body' => '',
				'plugin_key' => PluginKey::CUSTOM_MENU,
				'plugin_info' => ''
		);
		if (! self::checkFilter($message)) {
			Logger::debug("自定义菜单插件 未命中结束");
			return $return;
		}
		$return['status'] = true;

		$initResult = self::_init($message);

		$messageBody = self::_run();

		$return['plugin_info'] = self::$_pluginDialogInfo;
		$return['message_body'] = $messageBody;
		Logger::debug("自定义菜单插件 命中结束");
		return $return;
	}

	/**
	  * 检测过滤器
	  * @param WX_Message $message
	  * @return bool
	  */
	public static function checkFilter($message)
	{
		Logger::info("----------------",$message);
		if ($message && is_object($message)) {
			if (is_object($message->event) && strtolower($message->event->eventType) == MessageEventType::CLICK) {
				return true;
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
		self::$_eventKey = $message->event->eventKey;
		self::$_db = Factory::getDb();

		self::$_pluginDialogInfo = new PluginDialogInfo();
		self::$_pluginDialogInfo->key = PluginKey::CUSTOM_MENU;
		self::$_pluginDialogInfo->id = $message->event->eventKey;

		return true;
	}

	/**
	 * 开始运行
	 * @return NULL|Ambigous <WX_Message_Body, NULL>
	 */
	private static function _run()
	{
		$menu = self::_getMenuById(self::$_eventKey);
		if (! $menu) {
			Logger::error ("自定义菜单插件 获取菜单信息为空");
			return null;
		}
		$messageBody = self::_genMessageBody($menu);
		
		return $messageBody;
	}

	/**
	 * @name 获取事件数据
	 * @param string $evenKey 事件标识
	 * @return array
	 */
	private static function _getMenuById($eventKey)
	{
		$sql = "SELECT * FROM `wx_custom_menu` WHERE `id` = %d";
		try {
			$menu = self::$_db->getRow(sprintf($sql, $eventKey));
		} catch ( Exception $e ) {
			Logger::error('自定义菜单插件 获取数据失败 ', $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
		}
		return $menu;
	}

	/**
	 * 生成MessageBody
	 * @param array $menu
	 * @return WX_Message_Body
	 */
	private static function _genMessageBody($menu)
	{
		$messageBody = null;
		if (! $menu) {
			return null;
		}
		//系统回复
		if ($menu['type'] == 1) {
			$msgType = $menu['msg_type'];
			$content = $menu['content'];

			$messageBody = new WX_Message_Body();
			$messageBody->msgType = $msgType;
			$messageBody->toUser = self::$_openId;
			if ('text' == $msgType) {
				if (! $content) {
					Logger::error("自定义菜单插件 文本类型数据内容为空");
					return null;
				}
				$messageBody->content = trim($content);
			} else {
				$material = self::_getMaterialById($menu['material_id']);
				if (! $material || !is_array($material)) {
					Logger::error("自定义菜单插件 获取素材信息失败");
					return null;
				}

				switch ($msgType) {
					case 'news' :
						$monitorParam = array(
							MonitorParams::MATERIAL_ID => $menu['material_id'],
							MonitorParams::USE_OAUTH => (int) $menu['use_oauth'],
							MonitorParams::MSG_SOURCE => 1,
							MonitorParams::MODUEL_ID => self::$_eventKey,
						);
						$articles = self::_addMonitor($material['articles'], $monitorParam);
						if (! $articles || !is_array($articles)) {
							Logger::error("自定义菜单插件 图文类型数据为空");
							return null;
						}
						foreach ($articles as $key => $value) {
							if (! is_array($value) || ! $value || ! @$value['title']
							|| ! @$value['description'] || ! @$value['url'] || ! @$value['picurl']) {
								Logger::error("自定义菜单插件 图文类型数据格式错误", $articles);
								return null;
							}
						}
						$messageBody->articles = $articles;
						//$messageBody->articles = $material['articles'];
						break;
					case 'music' :
						if (! $material['title'] || ! $material['description'] ||
						! $material['music_url'] || ! $material['thumb_url']) {
							Logger::error("自定义菜单插件 音乐类型数据格式错误");
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
							Logger::error("自定义菜单插件 媒体类型数据不存在");
							return null;
						}
						$messageBody->attachment = $material['media_url'];
						if ('video' == $msgType) {
							$messageBody->title = @$material['title'];
							$messageBody->description = @$material['description'];
						}
						break;
				}
			}
		} else if ($menu['type'] == 2) {
			$url = $menu['url'];
			if (! $url) {
				Logger::error("自定义菜单插件 type为2 url为空");
				return null;
			}

			$messageBody = ThirdPartyTools::pluginCustomMenuPush($url, self::$_message);
			Logger::info("-------------------((((---------",$messageBody);
			if (! $messageBody) {
				Logger::error("ThirdPartyTools::pluginCustomMenuPush() error: http_code:"
						. ThirdPartyTools::getHttpCode() . ' error:' . ThirdPartyTools::getError()
						. ' http_url:' . $url
						. ' response:', ThirdPartyTools::getResponse());
				return null;
			}
			if ('news' == strtolower($messageBody->type)) {
				$userMonitorData = ThirdPartyTools::getMonitorData();
				$monitorData = array(
						MonitorParams::MATERIAL_ID => @$userMonitorData['material_id'],
						MonitorParams::USE_OAUTH => (int) $userMonitorData['use_oauth'],
						MonitorParams::MSG_SOURCE => 2,
						MonitorParams::MODUEL_ID => self::$_eventKey,
				);
				$messageBody->articles = self::_addMonitor($messageBody->articles, $monitorData);
			}
		} else {
			Logger::error("自定义菜单插件 类型错误 type:".$menu['type']."; id:".$menu['id'],$menu);
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
			Logger::error('自定义菜单插件 获取素材信息错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
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
			Logger::error('自定义菜单插件 获取素材明细信息错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
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
		if (! is_array($articles) || ! self::$_openId || ! $monitorParam || ! @$monitorParam[MonitorParams::MATERIAL_ID]) {
			Logger::error("自定义菜单插件 图文数据添加监测，数据格式错误：", array('articles' => $articles, 'monitorParam' => $monitorParam));
			return $articles;
		}

		$articles = MonitorTools::genCustomMenuNewsMonitor($articles, $monitorParam, self::$_openId, self::$_message);
		return $articles;
	}
}