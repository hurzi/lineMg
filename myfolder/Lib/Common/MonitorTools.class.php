<?php
/**
 * 生成图文监测类
 *
 */
class MonitorTools
{
	/**
	 * 生成欢迎词图文监测
	 * @param array $articles
	 * @param array $monitorParam
	 * @param string $openid
	 * @param WX_Message $message
	 * @return array|NULL
	 */
	public static function genWelcomeNewsMonitor($articles, $monitorParam, $openid, $message = null)
	{
		$articlesMonitor = array();
		$materialId = $monitorParam[MonitorParams::MATERIAL_ID];
		$useOauth = $monitorParam[MonitorParams::USE_OAUTH];
		$msgSource = $monitorParam[MonitorParams::MSG_SOURCE];

		//如果monitor参数存在解析monitor data
		$monitorData = array(
				//MonitorParams::WECHAT => C('WECHAT'),
				MonitorParams::MATERIAL_ID => $materialId,
				MonitorParams::SOURCE_ID => $openid,
				MonitorParams::MOUDEL => MonitorParams::MOUDEL_WELCOME,
				MonitorParams::MODUEL_ID => 0,
				MonitorParams::RULE_ID => 0,
				MonitorParams::OPERATOR_ID => 0,
				MonitorParams::USE_OAUTH => (int )$useOauth,
				//MonitorParams::MSG_SOURCE => 1,
				MonitorParams::QRC_APP_ID => '',
				MonitorParams::EVENT_KEY => '',
				MonitorParams::MATERIAL_SOURCE => MonitorParams::MOUDEL_WELCOME
		);
		$params = array(
				MonitorHttpParams::OPEN_ID => $openid,
				//AuthQueryData::REQUEST_AUTH_API_KEY => C('API_KEY'),
				//AuthQueryData::REQUEST_AUTH_API_SECRET => C('API_SECRET'),
				MonitorHttpParams::M_FROM => 'msg',
				MonitorHttpParams::OAUTHED => 0
		);
		if (1 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 1;
			$articlesMonitor = self::_genNewsMonitor($articles, $monitorData, $params, $message);

		} else if (2 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 2;
			//动态回复图文监测
			if (strlen($materialId) > 64) {
				Logger::error("MonitorTools::genWelcomeNewsMonitor() material_id too long", $monitorData);
				return $articles;
			}

			foreach ($articles as $index => $value) {
				$articlesMonitor[$index]['url'] = self::_genNewsOriginalUrlWithMonitorData($index+1, $value['url'], $monitorData, $params, $message);
			}
		}
		return $articlesMonitor;
	}

	/**
	 * 生成自定义菜单图文监测
	 * @param array $articles
	 * @param array $monitorParam
	 * @param string $openid
	 * @param WX_Message $message
	 * @return array|NULL
	 */
	public static function genCustomMenuNewsMonitor($articles, $monitorParam, $openid, $message = null)
	{
		$articlesMonitor = array();
		$materialId = $monitorParam[MonitorParams::MATERIAL_ID];
		$useOauth = $monitorParam[MonitorParams::USE_OAUTH];
		$msgSource = $monitorParam[MonitorParams::MSG_SOURCE];
		$moduelId = $monitorParam[MonitorParams::MODUEL_ID];

		//如果monitor参数存在解析monitor data
		$monitorData = array(
				//MonitorParams::WECHAT => C('WECHAT'),
				MonitorParams::MATERIAL_ID => $materialId,
				MonitorParams::SOURCE_ID => $openid,
				MonitorParams::MOUDEL => MonitorParams::MOUDEL_CUNSTOM_MENU,
				MonitorParams::MODUEL_ID => $moduelId,
				MonitorParams::RULE_ID => 0,
				MonitorParams::OPERATOR_ID => 0,
				MonitorParams::USE_OAUTH => (int )$useOauth,
				//MonitorParams::MSG_SOURCE => 1,
				MonitorParams::QRC_APP_ID => '',
				MonitorParams::EVENT_KEY => '',
				MonitorParams::MATERIAL_SOURCE => MonitorParams::MOUDEL_CUNSTOM_MENU
		);
		$params = array(
				MonitorHttpParams::OPEN_ID => $openid,
				//AuthQueryData::REQUEST_AUTH_API_KEY => C('API_KEY'),
				//AuthQueryData::REQUEST_AUTH_API_SECRET => C('API_SECRET'),
				MonitorHttpParams::M_FROM => 'msg',
				MonitorHttpParams::OAUTHED => 0
		);
		if (1 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 1;
			$articlesMonitor = self::_genNewsMonitor($articles, $monitorData, $params, $message);

		} else if (2 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 2;
			//动态回复图文监测
			if (strlen($materialId) > 64) {
				Logger::error("MonitorTools::genCustomMenuNewsMonitor() material_id too long", $monitorData);
				return $articles;
			}

			foreach ($articles as $index => $value) {
				$articlesMonitor[$index]['url'] = self::_genNewsOriginalUrlWithMonitorData($index+1, $value['url'], $monitorData, $params, $message);
			}
		}
		return $articlesMonitor;
	}

	/**
	 * 生成关键词图文监测
	 * @param array $articles
	 * @param array $monitorParam
	 * @param string $openid
	 * @param WX_Message $message
	 * @return array|NULL
	 */
	public static function genKeywordNewsMonitor($articles, $monitorParam, $openid, $message = null)
	{
		$articlesMonitor = array();
		$materialId = $monitorParam[MonitorParams::MATERIAL_ID];
		$useOauth = $monitorParam[MonitorParams::USE_OAUTH];
		$msgSource = $monitorParam[MonitorParams::MSG_SOURCE];
		$moduelId = $monitorParam[MonitorParams::MODUEL_ID];
		$ruleId = $monitorParam[MonitorParams::RULE_ID];

		//如果monitor参数存在解析monitor data
		$monitorData = array(
				//MonitorParams::WECHAT => C('WECHAT'),
				MonitorParams::MATERIAL_ID => $materialId,
				MonitorParams::SOURCE_ID => $openid,
				MonitorParams::MOUDEL => MonitorParams::MOUDEL_KEYWORD,
				MonitorParams::MODUEL_ID => $moduelId,
				MonitorParams::RULE_ID => $ruleId,
				MonitorParams::OPERATOR_ID => 0,
				MonitorParams::USE_OAUTH => (int )$useOauth,
				//MonitorParams::MSG_SOURCE => 1,
				MonitorParams::QRC_APP_ID => '',
				MonitorParams::EVENT_KEY => '',
				MonitorParams::MATERIAL_SOURCE => MonitorParams::MOUDEL_KEYWORD
		);
		$params = array(
				MonitorHttpParams::OPEN_ID => $openid,
				//AuthQueryData::REQUEST_AUTH_API_KEY => C('API_KEY'),
				//AuthQueryData::REQUEST_AUTH_API_SECRET => C('API_SECRET'),
				MonitorHttpParams::M_FROM => 'msg',
				MonitorHttpParams::OAUTHED => 0
		);
		if (1 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 1;
			$articlesMonitor = self::_genNewsMonitor($articles, $monitorData, $params, $message);

		} else if (2 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 2;
			//动态回复图文监测
			if (strlen($materialId) > 64) {
				Logger::error("MonitorTools::genKeywordNewsMonitor() material_id too long", $monitorData);
				return $articles;
			}

			foreach ($articles as $index => $value) {
				$articlesMonitor[$index]['url'] = self::_genNewsOriginalUrlWithMonitorData($index+1, $value['url'], $monitorData, $params, $message);
			}
		}
		return $articlesMonitor;
	}

	/**
	 * 生成地理位置图文监测
	 * @param array $articles
	 * @param array $monitorParam
	 * @param string $openid
	 * @param WX_Message $message
	 * @return array|NULL
	 */
	public static function genLocationNewsMonitor($articles, $monitorParam, $openid, $message = null)
	{
		$articlesMonitor = array();
		$materialId = $monitorParam[MonitorParams::MATERIAL_ID];
		$useOauth = $monitorParam[MonitorParams::USE_OAUTH];
		$msgSource = $monitorParam[MonitorParams::MSG_SOURCE];
		$moduelId = $monitorParam[MonitorParams::MODUEL_ID];

		//如果monitor参数存在解析monitor data
		$monitorData = array(
				//MonitorParams::WECHAT => C('WECHAT'),
				MonitorParams::MATERIAL_ID => $materialId,
				MonitorParams::SOURCE_ID => $openid,
				MonitorParams::MOUDEL => MonitorParams::MOUDEL_LOCATION,
				MonitorParams::MODUEL_ID => $moduelId,
				MonitorParams::RULE_ID => 0,
				MonitorParams::OPERATOR_ID => 0,
				MonitorParams::USE_OAUTH => (int )$useOauth,
				//MonitorParams::MSG_SOURCE => 1,
				MonitorParams::QRC_APP_ID => '',
				MonitorParams::EVENT_KEY => '',
				MonitorParams::MATERIAL_SOURCE => MonitorParams::MOUDEL_LOCATION
		);
		$params = array(
				MonitorHttpParams::OPEN_ID => $openid,
				//AuthQueryData::REQUEST_AUTH_API_KEY => C('API_KEY'),
				//AuthQueryData::REQUEST_AUTH_API_SECRET => C('API_SECRET'),
				MonitorHttpParams::M_FROM => 'msg',
				MonitorHttpParams::OAUTHED => 0
		);
		if (1 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 1;
			$articlesMonitor = self::_genNewsMonitor($articles, $monitorData, $params, $message);

		} else if (2 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 2;
			//动态回复图文监测
			if (strlen($materialId) > 64) {
				Logger::error("MonitorTools::genLocationNewsMonitor() material_id too long", $monitorData);
				return $articles;
			}

			foreach ($articles as $index => $value) {
				$articlesMonitor[$index]['url'] = self::_genNewsOriginalUrlWithMonitorData($index+1, $value['url'], $monitorData, $params, $message);
			}
		}
		return $articlesMonitor;
	}

	/**
	 * 生成自定义回复图文监测
	 * @param array $articles
	 * @param array $monitorParam
	 * @param string $openid
	 * @param WX_Message $message
	 * @return array|NULL
	 */
	public static function genConsultNewsMonitor($articles, $monitorParam, $openid, $message = null)
	{
		$articlesMonitor = array();
		$materialId = $monitorParam[MonitorParams::MATERIAL_ID];
		$useOauth = $monitorParam[MonitorParams::USE_OAUTH];
		$msgSource = $monitorParam[MonitorParams::MSG_SOURCE];
		$moduelId = $monitorParam[MonitorParams::MODUEL_ID];

		//如果monitor参数存在解析monitor data
		$monitorData = array(
				//MonitorParams::WECHAT => C('WECHAT'),
				MonitorParams::MATERIAL_ID => $materialId,
				MonitorParams::SOURCE_ID => $openid,
				MonitorParams::MOUDEL => MonitorParams::MOUDEL_CONSULT,
				MonitorParams::MODUEL_ID => $moduelId,
				MonitorParams::RULE_ID => 0,
				MonitorParams::OPERATOR_ID => 0,
				MonitorParams::USE_OAUTH => (int )$useOauth,
				//MonitorParams::MSG_SOURCE => 1,
				MonitorParams::QRC_APP_ID => '',
				MonitorParams::EVENT_KEY => '',
				MonitorParams::MATERIAL_SOURCE => MonitorParams::MOUDEL_CONSULT
		);
		$params = array(
				MonitorHttpParams::OPEN_ID => $openid,
				//AuthQueryData::REQUEST_AUTH_API_KEY => C('API_KEY'),
				//AuthQueryData::REQUEST_AUTH_API_SECRET => C('API_SECRET'),
				MonitorHttpParams::M_FROM => 'msg',
				MonitorHttpParams::OAUTHED => 0
		);
		if (1 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 1;
			$articlesMonitor = self::_genNewsMonitor($articles, $monitorData, $params, $message);

		} else if (2 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 2;
			//动态回复图文监测
			if (strlen($materialId) > 64) {
				Logger::error("MonitorTools::genConsultNewsMonitor() material_id too long", $monitorData);
				return $articles;
			}

			foreach ($articles as $index => $value) {
				$articlesMonitor[$index]['url'] = self::_genNewsOriginalUrlWithMonitorData($index+1, $value['url'], $monitorData, $params, $message);
			}
		}
		return $articlesMonitor;
	}

	/**
	 * 生成带参数二维码图文监测
	 * @param array $articles
	 * @param array $monitorParam
	 * @param string $openid
	 * @param WX_Message $message
	 * @return array|NULL
	 */
	public static function genQrcParamNewsMonitor($articles, $monitorParam, $openid, $message = null)
	{
		$articlesMonitor = array();
		$materialId = $monitorParam[MonitorParams::MATERIAL_ID];
		$useOauth = $monitorParam[MonitorParams::USE_OAUTH];
		$msgSource = $monitorParam[MonitorParams::MSG_SOURCE];
		$moduelId = $monitorParam[MonitorParams::MODUEL_ID];

		$qrcAppId = $monitorParam[MonitorParams::QRC_APP_ID];
		$materialSource = $monitorParam[MonitorParams::MATERIAL_SOURCE];

		//如果monitor参数存在解析monitor data
		$monitorData = array(
				//MonitorParams::WECHAT => C('WECHAT'),
				MonitorParams::MATERIAL_ID => $materialId,
				MonitorParams::SOURCE_ID => $openid,
				MonitorParams::MOUDEL => MonitorParams::MOUDEL_QRCODE_PARAM,
				MonitorParams::MODUEL_ID => $moduelId,
				MonitorParams::RULE_ID => 0,
				MonitorParams::OPERATOR_ID => 0,
				MonitorParams::USE_OAUTH => (int )$useOauth,
				//MonitorParams::MSG_SOURCE => 1,
				MonitorParams::QRC_APP_ID => $qrcAppId,
				MonitorParams::EVENT_KEY => '',
				MonitorParams::MATERIAL_SOURCE => $materialSource
		);
		$params = array(
				MonitorHttpParams::OPEN_ID => $openid,
				//AuthQueryData::REQUEST_AUTH_API_KEY => C('API_KEY'),
				//AuthQueryData::REQUEST_AUTH_API_SECRET => C('API_SECRET'),
				MonitorHttpParams::M_FROM => 'msg',
				MonitorHttpParams::OAUTHED => 0
		);
		if (1 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 1;
			$articlesMonitor = self::_genNewsMonitor($articles, $monitorData, $params, $message);

		} else if (2 == $msgSource) {
			$monitorData[MonitorParams::MSG_SOURCE] = 2;
			//动态回复图文监测
			if (strlen($materialId) > 64) {
				Logger::error("MonitorTools::genQrcParamNewsMonitor() material_id too long", $monitorData);
				return $articles;
			}

			foreach ($articles as $index => $value) {
				$articlesMonitor[$index]['url'] = self::_genNewsOriginalUrlWithMonitorData($index+1, $value['url'], $monitorData, $params, $message);
			}
		}
		return $articlesMonitor;
	}


	/**
	 * 生成图文监测
	 * @param array $news 图文数据
	 * @param array $monitorData 监测数据
	 * @param array $params 附加参数 appKey, appSecret, openId
	 * @param WX_Message 上行消息对象
	 * @return array
	 */
	private static function _genNewsMonitor($news, $monitorData, $params, $message = null) {
		$newsMonitor = array();
		if (! $news  ||! is_array($news)) {
			Logger::error("自定义菜单插件 图文数据添加监测，数据格式错误：news不存在或不是数组");
			return $newsMonitor;
		}

		foreach ($news as $k => $v) {
			if ($v['news_text']) {
				$url = self::_genNewsTextUrlWithMonitorData($v['news_index'], $monitorData, $params, $message);
			} else {
				$url = self::_genNewsOriginalUrlWithMonitorData($v['news_index'], $v['url'], $monitorData, $params, $message);
			}
			$newsMonitor[$k]['title'] = $v['title'];
			$newsMonitor[$k]['description'] = $v['description'];
			$newsMonitor[$k]['picurl'] = $v['picurl'];
			$newsMonitor[$k]['url'] = $url;
		}
		return $newsMonitor;
	}

	/**
	 * 生存图文正文URL带监测数据
	 * @param int $newsIndex
	 * @param array $monitorData
	 * @param array $params
	 * @param WX_Message $message
	 * @return NULL|string
	 */
	private static function _genNewsTextUrlWithMonitorData($newsIndex, $monitorData, $params, $message)
	{
		if (! $newsIndex || (! isset($monitorData[MonitorParams::MATERIAL_ID]) && ! $monitorData[MonitorParams::MATERIAL_ID])) {
			return null;
		}

		$queryData = array(
				MonitorHttpParams::MATERIAL_ID => (int) $monitorData[MonitorParams::MATERIAL_ID],
				MonitorHttpParams::INDEX => $newsIndex,
				MonitorHttpParams::MONITOR_DATA => self::_formatMonitorData($newsIndex, $monitorData, $message),
				MonitorHttpParams::OPEN_ID => $params[MonitorHttpParams::OPEN_ID]
		);

		$queryData = getAuthQueryData($queryData);
		$queryData[MonitorHttpParams::M_FROM] = $params[MonitorHttpParams::M_FROM];
		$queryData[MonitorHttpParams::OAUTHED] = $params[MonitorHttpParams::OAUTHED];
		$url = C('NEWS_TEXT_URL') . (false == strrpos(C('NEWS_TEXT_URL'), '?')?'?':'&').http_build_query($queryData);
		return $url;
	}

	/**
	 * 生成图文原文URL带监测数据
	 * @param int $newsIndex
	 * @param string $newsUrl
	 * @param array $monitorData
	 * @param array $params
	 * @param WX_Message $message
	 * @return NULL|unknown|Ambigous <unknown, string>
	 */
	private static function _genNewsOriginalUrlWithMonitorData($newsIndex, $newsUrl, $monitorData, $params, $message)
	{
		if (! $newsUrl) {
			return null;
		}
		if (! $monitorData || ! is_array($monitorData)) {
			return $newsUrl;
		}

		$target = $newsUrl;
		$queryData = array(
				MonitorHttpParams::MONITOR_DATA => self::_formatMonitorData($newsIndex, $monitorData, $message),
				MonitorHttpParams::OPEN_ID => $params[MonitorHttpParams::OPEN_ID]
		);
		$queryData = getAuthQueryData($queryData);
		$queryData[MonitorHttpParams::M_FROM] = $params[MonitorHttpParams::M_FROM];
		$queryData[MonitorHttpParams::OAUTHED] = $params[MonitorHttpParams::OAUTHED];
		$queryData[MonitorHttpParams::TARGET] = $target;
		return resetUrl(C('NEWS_ORIGINAL_URL'), $queryData);
	}

	/**
	 * 格式化监测数据
	 * @param int $newsIndex
	 * @param array $monitorData
	 * @param WX_Message $message
	 * @return string
	 */
	private static function _formatMonitorData($newsIndex, $monitorData, $message)
	{
		$formatArray = array(
				MonitorParams::WECHAT => (string) @C('WECHAT'),
				MonitorParams::MATERIAL_ID => trim(@$monitorData[MonitorParams::MATERIAL_ID]),
				MonitorParams::MATERIAL_INDEX => (string)@$newsIndex,
				MonitorParams::SOURCE_ID => (string) @$monitorData[MonitorParams::SOURCE_ID],
				MonitorParams::MOUDEL => (string) @$monitorData[MonitorParams::MOUDEL],
				MonitorParams::MODUEL_ID => (string)@$monitorData[MonitorParams::MODUEL_ID],
				MonitorParams::RULE_ID => (string)@$monitorData[MonitorParams::RULE_ID],
				MonitorParams::OPERATOR_ID => (string)@$monitorData[MonitorParams::OPERATOR_ID],
				MonitorParams::USE_OAUTH => (string)@$monitorData[MonitorParams::USE_OAUTH],
				MonitorParams::MSG_SOURCE => (string)@$monitorData[MonitorParams::MSG_SOURCE],
				MonitorParams::QRC_APP_ID => (string)@$monitorData[MonitorParams::QRC_APP_ID],
				MonitorParams::EVENT_KEY => (string)@$monitorData[MonitorParams::EVENT_KEY],
				MonitorParams::MATERIAL_SOURCE => (string)@$monitorData[MonitorParams::MATERIAL_SOURCE],
				MonitorParams::EVENT_TYPE => ''
		);
		if (is_object($message) && $message->event && $message->event->eventType) {
			$formatArray[MonitorParams::EVENT_TYPE] = (string) $message->event->eventType;
		}
		return implode(MonitorParams::DELIMITER, $formatArray);
	}
}