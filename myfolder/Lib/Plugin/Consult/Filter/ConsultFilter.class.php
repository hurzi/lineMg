<?php
/**
 * 自定义回复过滤器
 */
class ConsultFilter
{
	private static $_message;
	private static $_openId;
	private static $_db;

	private static $_keyword;
	private static $_consultId;

	private static $_queryUrl;
	private static $_queryContent;

	private static $_pluginDialogInfo;

	/**
	 * 自定义回复过插件过滤器入口
	 * @param int $entId 企业id
	 * @param WX_Message $message
	 * @return bool
	 */
	public static function main($message)
	{
		Logger::debug('自定义回复插件 开始');
		$return = array (
				'status' => false,
				'message_body' => '',
				'plugin_key' => PluginKey::CONSULT,
				'plugin_info' => ''
		);

		self::$_db = Factory::getDb();

		if (! self::checkFilter($message)) {
			Logger::debug("自定义回复插件 未命中结束");
			return $return;
		}

		$return['status'] = true;

		$initResult = self::_init($message);

		$messageBody = self::_run();

		$return['plugin_info'] = self::$pluginDialogInfo;
		$return['message_body'] = $messageBody;
		Logger::debug("自定义回复插件 命中结束");

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
		if ($message->type != 'text') {
			return false;
		}

		// 获取关键词列表
		$ruleList = self::_getRuleList();

		$content = trim($message->content);

		foreach ($ruleList as $value) {
			if (! $value['query_url']) {
				continue;
			}
			//匹配rule规则
			if (0 !== strpos(strtolower($content), strtolower($value['keyword_rule']))) {
				continue;
			}
			$count = 1;
			$queryContent = str_replace(strtolower($value['keyword_rule']), '', strtolower($content), $count);
			$queryContent = trim($queryContent);

			// query 为空 并且 配置为非空
			if (! $queryContent && $value['is_query_empty'] != 1) {
				continue;
			}

			self::$_queryContent = $queryContent;
			self::$_queryUrl = $value['query_url'];

			self::$_keyword = $value['keyword_rule'];
			self::$_consultId = $value['id'];

			return true;
		}
		return false;
	}

	/**
	 * 根据entId获取规则列表
	 * @return array
	 */
	private static function _getRuleList()
	{
		$cacher = Factory::getGlobalCacher();
		$cacheId = GlobalCatchId::CONSULT_RULES. C('WECHAT');
		$list = $cacher->get($cacheId);
		if (is_array($list)) {
			return $list;
		}

		$sql = "SELECT * FROM `wx_plug_consult_set`";
		try {
			$list = self::$_db->getAll($sql);
			if (! $list) {
				$list = array();
			}
		} catch ( Exception $e ) {
			Logger::error("自定义回复插件 获取规则列表失败： ", $e->getMessage() . "\n" . $e->getTraceAsString());
			return array ();
		}
		$cacher->set($cacheId, $list, GlobalCatchExpired::CONSULT_RULES);
		return $list;
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
		self::$_pluginDialogInfo->key = PluginKey::CONSULT;
		self::$_pluginDialogInfo->id = self::$_consultId;
		self::$_pluginDialogInfo->keyword = self::$_keyword;
		self::$_pluginDialogInfo->ruleId = self::$_consultId;

		return true;
	}

	/**
	 * 运行
	 * @return NULL|Ambigous <WX_Message_Body, NULL, array>
	 */
	private static function _run()
	{
		$message = self::_getReplyMessage();
		if (! $message) {
			Logger::error ("自定义回复插件 获取回复信息为空");
			return null;
		}
		$messageBody = self::_genMessageBody($message);
		return $messageBody;
	}

	/**
	 * 获取自定义回复消息
	 * @return boolean|Ambigous <NULL, multitype:multitype: >
	 */
	private static function _getReplyMessage()
	{
		$messageBody = ThirdPartyTools::pluginConsultPush(self::$_queryUrl, self::$_message, self::$_queryContent);
		if (! $messageBody) {
			Logger::error("自定义回复插件 ThirdPartyTools::pluginConsultPush() error: http_code:"
					. ThirdPartyTools::getHttpCode() . '; error:' . ThirdPartyTools::getError()
					. '; http_url:' . self::$_queryUrl
					. '; http_params:' . ThirdPartyTools::getHttpParams()
					. '; response:', ThirdPartyTools::getResponse());
			return null;
		}

		return $messageBody;
	}

	/**
	 * 生成MessageBody
	 * @param WX_Message_Body $message
	 * @return WX_Message_Body
	 */
	private static function _genMessageBody($message)
	{
		if (! $message) {
			return null;
		}

		if ('news' == strtolower($message->msgType)) {
			//动态回复图文监测
			$userMonitorData = ThirdPartyTools::getMonitorData();
			$monitorParam = array (
					MonitorParams::MATERIAL_ID => @$userMonitorData['material_id'],
					MonitorParams::USE_OAUTH => (int) @$userMonitorData['use_oauth'],
					MonitorParams::MSG_SOURCE => 2,
					MonitorParams::MODUEL_ID => self::$_consultId,
			);
			$message->articles = self::_addMonitor($message->articles, $monitorParam);
		}

		return $message;
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
			Logger::error("自定义回复插件 图文数据添加监测，数据格式错误：", array('articles' => $articles, 'monitorParam' => $monitorParam));
			return $articles;
		}

		$articles = MonitorTools::genConsultNewsMonitor($articles, $monitorParam, self::$_openId, self::$_message);

		return $articles;
	}
}