<?php
/**
 * 关键词插件信息过滤
 */
class KeywordFilter
{
	private static $_message;
	private static $_openId;
	private static $_db;

	private static $_keyword;
	private static $_keywordId;
	private static $_ruleId;

	private static $_useOauth = 0;

	private static $_pluginDialogInfo;

	/**
	 * @name  主运行入口
	 * @param int $entId  ID
	 * @param WX_Message $message  微信消息对象
	 */
	public static function main($message)
	{
		Logger::debug("Keyword插件 开始");
		$return = array (
				'status' => false,
				'message_body' => '',
				'plugin_key' => PluginKey::KEYWORD,
				'plugin_info' => ''
		);

		self::$_db = Factory::getDb();

		if (! self::checkFilter($message)) {
			Logger::debug("Keyword插件 未命中结束");
			return $return;
		}
		$return['status'] = true;

		$initResult = self::_init($message);

		$messageBody = self::_run();
		
		$return['plugin_info'] = array();//self::$pluginDialogInfo;
		$return['message_body'] = $messageBody;
		
		Logger::debug("Keyword插件 命中结束:",$return);
		return $return;
	}

	/**
	  * 检测过滤器
	  * @param WX_Message $message
	  * @return bool
	  */
	public static function checkFilter($message)
	{
		if (! $message || ! is_object($message)) {
			return false;
		}

		if ($message->msgType != 'text') {
			return false;
		}

		// 获取关键词列表
		$ruleList = self::_getRuleList();

		if (! $ruleList) {
			Logger::debug('Keyword插件 规则数据为空');
			return false;
		}
		$content = trim($message->content);
		foreach ($ruleList as $rule) {
			$keywordList = self::_getKeywordList($rule['rule_id']);
			if (false === $keywordList) {
				Logger::debug('Keyword插件 关键词数据获取失败 rule_id:', $rule['rule_id']);
				continue;
			}
			if (! $keywordList) {
				Logger::debug('Keyword插件 关键词数据为空 rule_id:', $rule['rule_id']);
				continue;
			}
			$parseList = self::_parseKeywordList($keywordList);
			// 完全匹配
			foreach ($parseList['all'] as $v) {
				if (strtolower($v['keyword']) == strtolower($content)) {
					Logger::debug(' Keyword插件 关键词完全匹配成功,keyword:' . $content);
					self::$_keyword = $v['keyword'];
					self::$_keywordId = $v['kwd_id'];
					self::$_ruleId = $v['rule_id'];
					self::$_useOauth = $rule['use_oauth'];
					return true;
				}
			}
			// 模糊匹配
			foreach ($parseList['some'] as $v) {
				$keywords = explode(' ', strtolower($v['keyword']));

				$pregArr = array();
				foreach ($keywords as $keyword) {
					array_push($pregArr, '(.*'. preg_quote($keyword) . '.*)');
				}
				if (! $pregArr || ! array_filter($pregArr)) {
					continue;
				}

				$preg_str = '/' . implode('|', $pregArr) . '/is';

				Logger::debug('Keyword插件 关键词 preg:' . $preg_str);
				if (preg_match($preg_str, strtolower($content))) {
					Logger::debug(' Keyword插件 关键词模糊匹配成功, keyword:' . $v['keyword']);
					self::$_keyword = $v['keyword'];
					self::$_keywordId = $v['kwd_id'];
					self::$_ruleId = $v['rule_id'];
					self::$_useOauth = $rule['use_oauth'];
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

		self::$_pluginDialogInfo = new PluginDialogInfo();
		self::$_pluginDialogInfo->key = PluginKey::KEYWORD;
		self::$_pluginDialogInfo->id = self::$_keywordId;
		self::$_pluginDialogInfo->keyword = self::$_keyword;
		self::$_pluginDialogInfo->ruleId = self::$_ruleId;

		return true;
	}

	/**
	 *
	 * @name 获取规则列表
	 */
	private static function _getRuleList()
	{
		$cacheId = GlobalCatchId::KEYWORD_RULES . C('WECHAT');
		$cacher = Factory::getGlobalCacher();
		$list = $cacher->get($cacheId);
		if (is_array($list)) {
			return $list;
		}
		$now = date('Y-m-d');
		$sql = "SELECT * FROM `wx_plug_keyword_rule` WHERE `start_date` <= '{$now}' AND `end_date` >= '{$now}'"
			." ORDER BY `rule_sort` ASC ";
		try {
			$list = self::$_db->getAll($sql);
			if (!$list) {
				$list = array();
			}
		} catch ( Exception $e ) {
			Logger::error("Keyword插件 关键词过滤器获取规则列表失败 ", $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
		}
		$cacher->set($cacheId, $list, GlobalCatchExpired::KEYWORD_RULES);
		return $list;
	}

	/**
	 * @name 获取列表
	 */
	private static function _getKeywordList($ruleId)
	{
		$cacheId = GlobalCatchId::KEYWORD_RULE_KEYWORDS . C('WECHAT') . '_' . $ruleId;
		$cacher = Factory::getGlobalCacher();
		$list = $cacher->get($cacheId);
		if (is_array($list)) {
			return $list;
		}
		$sql = "SELECT * FROM `wx_plug_keyword_set` WHERE `rule_id` = %d";
		try {
			$list = self::$_db->getAll(sprintf($sql, $ruleId));
			if (!$list) {
				$list = array();
			}
		} catch ( Exception $e ) {
			Logger::error("Keyword插件 关键词过滤器获取关键词列表失败 ", $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
		}
		$cacher->set($cacheId, $list, GlobalCatchExpired::KEYWORD_RULE_KEYWORDS);
		return $list;
	}

	/**
	 *
	 * @name 查询数据处理
	 */
	private static function _parseKeywordList($list)
	{
		$all = array ();
		$some = array ();
		foreach ($list as $k => $v) {
			if ($v['match_rule'] == 1) {
				$all[] = $v;
			} else {
				$some[] = $v;
			}
		}
		$return['all'] = $all;
		$return['some'] = $some;
		return $return;
	}

	/**
	 * 运行
	 * @return NULL|Ambigous <WX_Message_Body, NULL>
	 */
	private static function _run()
	{
		$message = self::_getReplyMessage(self::$_ruleId);
		if (! $message) {
			Logger::error ("Keyword插件 获取回复信息为空");
			return null;
		}
		$messageBody = self::_genMessageBody($message);
		return $messageBody;
	}

	/**
	 * 获取关键词回复消息
	 * @param int $ruleId
	 * @return boolean|Ambigous <NULL, multitype:multitype: >
	 */
	private static function _getReplyMessage($ruleId)
	{
		$sql = "SELECT * FROM `wx_plug_keyword_msg` WHERE `rule_id` = %d";
		try {
			$message = self::$_db->getRow(sprintf($sql, $ruleId));
		} catch ( Exception $e ) {
			Logger::error("Keyword插件 关键词过滤器获取推送信息列表失败： ", $e->getMessage(). "\n" . $e->getTraceAsString());
			return false;
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
		if (! $message || ! is_array($message)) {
			Logger::error ("Keyword插件 回复规则消息为空");
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
					Logger::error("keyword插件 文本类型数据内容为空");
					return null;
				}
				$messageBody->content = trim($content);		
			} else {
				$material = self::_getMaterialById($message['material_id']);
				if (! $material || !is_array($material)) {
					Logger::error("keyword插件 获取素材信息失败");
					return null;
				}

				switch ($msgType) {
					case 'news' :
						$monitorParam = array (
								MonitorParams::MATERIAL_ID => $message['material_id'],
								MonitorParams::USE_OAUTH => (int) @$message['use_oauth'],
								MonitorParams::MSG_SOURCE => 1
						);
						$articles = self::_addMonitor($material['articles'], $monitorParam);
						if (! $articles || !is_array($articles)) {
							Logger::error("keyword插件 图文类型数据为空");
							return null;
						}
						foreach ($articles as $key => $value) {
							if (! is_array($value) || ! $value || ! @$value['title']
							|| ! @$value['description']  || ! @$value['picurl']) {
								Logger::error("keyword插件 图文类型数据格式错误", $articles);
								return null;
							}
						}
						$messageBody->articles = $articles;
						break;
					case 'music' :
						if (! $material['title'] || ! $material['description'] ||
						! $material['music_url'] || ! $material['thumb_url']) {
							Logger::error("keyword插件 音乐类型数据格式错误");
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
							Logger::error("keyword插件 媒体类型数据不存在");
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
		} else if ($message['type'] == 2) {

			if (! $message['url']) {
				Logger::error("keyword插件 type为2 url为空");
				return null;
			}

			$messageBody = ThirdPartyTools::pluginKeywordPush($message['url'], self::$_message);
			if (! $messageBody) {
				Logger::error("keyword插件 ThirdPartyTools::pluginKeywordPush() error: http_code:"
						. ThirdPartyTools::getHttpCode() . ' error:' . ThirdPartyTools::getError()
						. ' http_url:' . $message['url'] . ' response:', ThirdPartyTools::getResponse());
			}

			if ('news' == strtolower($messageBody->msgType)) {
				//动态回复图文监测
				$userMonitorData = ThirdPartyTools::getMonitorData();
				$monitorParam = array (
						MonitorParams::MATERIAL_ID => @$userMonitorData['material_id'],
						MonitorParams::USE_OAUTH => (int) @$userMonitorData['use_oauth'],
						MonitorParams::MSG_SOURCE => 2,
						MonitorParams::MODUEL_ID => self::$_keywordId,
						MonitorParams::RULE_ID => self::$_ruleId,
				);
				$messageBody->articles = self::_addMonitor($messageBody->articles, $monitorParam);
			}
		} else {
			Logger::error("keyword插件 type 错误：" . $message['type']);
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
			Logger::error('Keyword插件 获取素材信息错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
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
			Logger::error('Keyword插件 获取图文明细错误：', $e->getMessage() . "\n" . $e->getTraceAsString());
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
			Logger::error("Keyword插件 图文数据添加监测，数据格式错误：", array('articles' => $articles, 'monitorParam' => $monitorParam));
			return $articles;
		}

		$articles = MonitorTools::genKeywordNewsMonitor($articles, $monitorParam, self::$_openId, self::$_message);

		return $articles;
	}
}
