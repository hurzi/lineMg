<?php
/**
 * 位置插件信息过滤器
 */
class LocationFilter
{
	const EARTH_RADIUS = 6371;

	private static $_message;
	private static $_openId;
	private static $_db;
	private static $_locationX;
	private static $_locationY;

	private static $_locationId;
	private static $_locationConf = array();
	private static $_pluginDialogInfo;

	/**
	 * @name  主运行入口
	 * @param int $entId  企业ID
	 * @param WX_Message $message  微信消息对象
	 */
	public static function main($message)
	{
		Logger::debug("地理位置插件 开始");
		$return = array (
				'status' => false,
				'message_body' => '',
				'plugin_key' => PluginKey::LOCATION,
				'plugin_info' => ''
		);
		if (! self::checkFilter($message)) {
			Logger::debug("地理位置插件 未命中结束");
			return $return;
		}
		$return['status'] = true;

		$initResult = self::_init($message);

		$messageBody = self::_run();

		self::$_pluginDialogInfo->id = self::$_locationId;
		self::$_pluginDialogInfo->locationId = self::$_locationId;

		$return['plugin_info'] = self::$pluginDialogInfo;
		$return['message_body'] = $messageBody;
		Logger::debug("地理位置插件 命中结束");
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
			if (strtolower(MessageEventType::LOCATION) == strtolower($message->msgType)) {
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

		self::$_locationX = (float) $message->location->locationX;
		self::$_locationY = (float) $message->location->locationY;

		self::$_db = Factory::getDb();

		self::$_pluginDialogInfo = new PluginDialogInfo();
		self::$_pluginDialogInfo->key = PluginKey::LOCATION;

		return true;
	}

	/**
	 * 运行
	 * @return NULL|Ambigous <WX_Message_Body, NULL>
	 */
	private static function _run()
	{
		$locationConfig = self::_getLocationConfig();
		if (! $locationConfig) {
			Logger::error("地理位置插件 获取设置信息为空");
			return null;
		}
		self::$_locationConf = $locationConfig;
		$messageBody = self::_genMessageBody($locationConfig);
		return $messageBody;
	}

	/**
	 * 获取地理位置设置信息
	 */
	private static function _getLocationConfig()
	{
		$config = array();
		$sql = "SELECT * FROM wx_plug_location_conf";
		try {
			$config = self::$_db->getRow($sql);
		} catch ( Exception $e ) {
			Logger::error('地理位置插件 获取地理位置设置信息错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
		}
		return $config;
	}

	/**
	 * 生成MessageBody
	 * @param array $locationConfig
	 * @return WX_Message_Body
	 */
	private static function _genMessageBody($locationConfig)
	{
		if (! $locationConfig || ! is_array($locationConfig)) {
			return null;
		}
		//系统回复
		if ($locationConfig['type'] == 1) {

			$replyMessage = self::_getReplyMessage(self::$_locationX, self::$_locationY);
			if (! $replyMessage) {
				// 错误loggor
				Logger::error('地理位置插件 获取推送数据失败');
				return null;
			}
			$msgType = $replyMessage['msg_type'];
			$content = $replyMessage['content'];

			$messageBody = new WX_Message_Body();
			$messageBody->msgType = $msgType;
			$messageBody->toUser = self::$_openId;
			if ('text' == $msgType) {
				if (! $content) {
					Logger::error("地理位置插件 文本类型数据内容为空");
					return null;
				}
				$messageBody->content = trim($content);
			} else {
				$material = self::_getMaterialById($replyMessage['material_id']);
				if (! $material || ! is_array($material)) {
					Logger::error("地理位置插件 获取素材信息失败");
					return null;
				}

				switch ($msgType) {
					case 'news' :

						$monitorParam = array(
								MonitorParams::MATERIAL_ID => (int) $replyMessage['material_id'],
								MonitorParams::USE_OAUTH => (int) $replyMessage['use_oauth'],
								MonitorParams::MSG_SOURCE => 1,
								MonitorParams::MODUEL_ID => self::$_locationId,
						);
						$articles = self::_addMonitor($material['articles'], $monitorParam);

						if (! $articles || ! is_array($articles)) {
							Logger::error("地理位置插件 图文类型数据为空");
							return null;
						}
						foreach ($articles as $key => $value) {
							if (! is_array($value) || ! $value || ! @$value['title'] || ! @$value['description'] || ! @$value['url'] || ! @$value['picurl']) {
								Logger::error("地理位置插件 图文类型数据格式错误", $articles);
								return null;
							}
						}
						$messageBody->articles = $articles;
						break;
					case 'music' :
						if (! $material['title'] || ! $material['description'] || ! $material['music_url'] || ! $material['thumb_url']) {
							Logger::error("地理位置插件 音乐类型数据格式错误");
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
							Logger::error("地理位置插件 媒体类型数据不存在");
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
		} else if ($locationConfig['type'] == 2) {
			$url = $locationConfig['url'];
			if (! $url) {
				Logger::error("地理位置插件 type为2 url为空");
				return null;
			}

			$messageBody = ThirdPartyTools::pluginLocationPush($url, self::$_message, self::$_locationX, self::$_locationY);
			if (! $messageBody) {
				Logger::error("keyword插件 ThirdPartyTools::pluginLocationPush() error: http_code:"
						. ThirdPartyTools::getHttpCode() . ' error:' . ThirdPartyTools::getError()
						. ' http_url:' . $url . ' response:', ThirdPartyTools::getResponse());
			}

			if ('news' == strtolower($messageBody->msgType)) {
				//动态回复图文监测
				$userMonitorData = ThirdPartyTools::getMonitorData();
				$monitorParam = array (
						MonitorParams::MATERIAL_ID => @$userMonitorData['material_id'],
						MonitorParams::USE_OAUTH => (int) @$userMonitorData['use_oauth'],
						MonitorParams::MSG_SOURCE => 2,
						MonitorParams::MODUEL_ID => (int) @self::$_locationId,
				);
				$messageBody->articles = self::_addMonitor($messageBody->articles, $monitorParam);
			}

		} else {
			Logger::error("地理位置插件 type 错误：" . $locationConfig['type']);
			return null;
		}
		return $messageBody;
	}

	/**
	 * 获取坐标范围中的推送数据
	 */
	public function _getReplyMessage($entId, $locationX, $locationY)
	{
		$replyMessage = array();
		$replyData = array();
		//获取经度纬度范围
		$range = self::_getSquarePoint($locationX, $locationY, self::$_locationConf['range']);

		$sql = "SELECT * FROM `wx_plug_location_set`"
				." WHERE location_x BETWEEN '{$range['minLng']}' AND '{$range['maxLng']}'"
				." AND location_y BETWEEN '{$range['minLat']}' AND '{$range['maxLat']}'"
				." ORDER BY create_time DESC";
		try {
			$replyData = self::$_db->getAll($sql);
		} catch ( Exception $e ) {
			Logger::error('地理位置插件 获取坐标范围中的推送数据错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
		}

		if (! $replyData) {
			Logger::debug('地理位置插件 获取回复数据为空');
			return $replyMessage;
		}

		//验证后的数组
		$replyMessages = array ();
		//循环验证范围
		$num = 0; //计数变量
		foreach ($replyData as $v) {
			if ($num >= self::$_locationConf['send_count']) {
				break;
			}
			$distance = self::_getDistance($v['location_x'], $v['location_y'], $locationX, $locationY);
			if ($distance > self::$_locationConf['range']) {
				continue;
			}
			$replyMessages[] = $v;
			$num ++;
		}

		if (! $replyMessages) {
			Logger::debug('地理位置插件 匹配范围获取回复数据为空，返回默认信息');
			$replyMessages = self::_getDefaulReplyMessage();
		}
		foreach ($replyMessages as $v) {
			self::$_locationId = isset($v['id']) ? $v['id'] : 0;
			$replyMessage['msg_type'] = $v['msg_type'];
			$replyMessage['content'] = @$v['content'];
			$replyMessage['material_id'] = @$v['material_id'];
			$replyMessage['use_oauth'] = @$v['use_oauth'];
			break;
		}
		return $replyMessage;
	}

	/**
	 *计算某个经纬度的周围某段距离的正方形的四个点
	 *@param lng float 经度
	 *@param lat float 纬度
	 *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
	 *@return array 经纬度的最大最小值
	 */
	private static function _getSquarePoint($lng, $lat, $distance = 0.5)
	{
		$dlng = 2 * asin(sin($distance / (2 * self::EARTH_RADIUS)) / cos(deg2rad($lat)));
		$dlng = rad2deg($dlng);

		$dlat = $distance / self::EARTH_RADIUS;
		$dlat = rad2deg($dlat);
		$return = array (
				'minLat' => $lat - $dlat,
				'maxLat' => $lat + $dlat,
				'minLng' => $lng - $dlng,
				'maxLng' => $lng + $dlng
		);

		if ($return['minLng'] > $return['maxLng']) {
			$a = $return['maxLng'];
			$return['maxLng'] = $return['minLng'];
			$return['minLng'] = $a;
		}
		return $return;
	}

	/**
	 *@name 获取两个经纬度之间的距离
	 *@param float $lng1  第一点坐标的经度
	 *@param float $lat1  第一点坐标的纬度
	 *@param float $lng2  第二点坐标的经度
	 *@param float $lat2  第二点坐标的纬度
	 *@return float $s (km) 保留小数点4位
	 */
	private static function _getDistance($lng1, $lat1, $lng2, $lat2)
	{
		$radLat1 = deg2rad($lat1);
		//echo $radLat1;
		$radLat2 = deg2rad($lat2);
		$a = $radLat1 - $radLat2;
		$b = deg2rad($lng1) - deg2rad($lng2);
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
		$s = $s * self::EARTH_RADIUS;
		$s = round($s * 10000) / 10000;
		return $s;
	}

	/**
	 * 获取默认回复数据
	 * @return array
	 */
	private static function _getDefaulReplyMessage()
	{
		$defaultMessage = array (
				'msg_type' => self::$_locationConf['msg_type'],
				'material_id' => self::$_locationConf['material_id'],
				'content' => self::$_locationConf['content'],
				'use_oauth' => self::$_locationConf['use_oauth'],
		);
		return array (
				$defaultMessage
		);
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
			Logger::error('地理位置插件 获取素材信息根据ID错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
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
			Logger::error('地理位置插件 获取图文明细错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
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
			Logger::error("地理位置插件 图文数据添加监测，数据格式错误：", array('articles' => $articles, 'monitorParam' => $monitorParam));
			return $articles;
		}

		$articles = MonitorTools::genLocationNewsMonitor($articles, $monitorParam, self::$_openId, self::$_message);

		return $articles;
	}
}