<?php
/**
 * 正文显示model
 */

class MsgTextModel extends Model
{
	private static $_db;
	
	public function __construct(){
		self::$_db = Factory::getDb();
	}
	
	public function auth () {
		$apiKey = HttpRequest::get(Config::REQUEST_AUTH_API_KEY);
		$timestamp = HttpRequest::get(Config::REQUEST_AUTH_TIMESTAMP);
		$sig = HttpRequest::get(Config::REQUEST_AUTH_SIGNATURE);
		$materialId = trim(HttpRequest::get(MonitorHttpParams::MATERIAL_ID, ''));
		$messageIndex = (int)HttpRequest::get(MonitorHttpParams::INDEX, 0);
		$monitorData = (string)HttpRequest::get(MonitorHttpParams::MONITOR_DATA, '', false, 'all');
		$openId = (string)HttpRequest::get(MonitorHttpParams::OPEN_ID, '', false, 'all');
		$MFrom = trim(HttpRequest::get(MonitorHttpParams::M_FROM, '', false, 'all'));
		if (!$apiKey || !$timestamp || !$sig || !$monitorData || !$openId || !$materialId || $messageIndex <= 0) {
			Logger::error('MsgTextModel->auth params incomplete or invalid', HttpRequest::get());
			return false;
		}

		$entAppInfo = $this->getApiInfo($apiKey);
		if (!$entAppInfo) {
			Logger::error('MsgTextModel->auth error: '.$this->getError(), HttpRequest::get());
			return false;
		}
		$authData = array(
				Config::REQUEST_AUTH_API_KEY => $apiKey,
				Config::REQUEST_AUTH_TIMESTAMP => $timestamp,
				MonitorHttpParams::MATERIAL_ID => $materialId,
				MonitorHttpParams::INDEX => $messageIndex,
				MonitorHttpParams::MONITOR_DATA => $monitorData,
				MonitorHttpParams::OPEN_ID => $openId,
				);
		$newSig = getQrcAuthSig($apiKey, $entAppInfo['api_secret'], $authData);
		if ($newSig != $sig) {
			Logger::error('MsgTextModel->auth error: invalid sig', HttpRequest::get());
			return false;
		}

		$monitorData = $this->parseMonitorData($authData[MonitorHttpParams::MONITOR_DATA]);
		//如果不是来自oauth并要求强制通过oauth转换openid,或是来源于分享
		$oauthRedirect = '';
		$useOauth = @$monitorData[MonitorParams::USE_OAUTH];
		$oauthed = (int)HttpRequest::get(MonitorHttpParams::OAUTHED, 0);
		$params = array_merge($authData,
							array(
									MonitorHttpParams::M_FROM => $MFrom,
									MonitorHttpParams::OAUTHED => $oauthed
							)
					);
		if ($useOauth && (int)$oauthed != 1) {
			$entAppInfo['scope'] = $this->getOAuthScope($entAppInfo['ent_id']);
			if (!$entAppInfo['scope']) {
				Logger::error('MsgTextModel->getOAuthScope error: '.$this->getError(), HttpRequest::get());
				return false;
			}
			$oauthRedirect = $this->getOAuthRedirectUrl(Config::NEWS_TEXT_URL, $entAppInfo, $monitorData, $params);
		}
		$params[MonitorHttpParams::FACK_ID] = $this->uniqid('T_');
		return array('ent_info'=>$entAppInfo,
					'monitor_data'=> $monitorData,
					'oauth_redirect' => $oauthRedirect,
					'params' => $params
				);
	}

	/**
	 * 获取消息正文信息
	 * @param array $entAppInfo  企业app info
	 * @param int $mid 消息id
	 * @param int $index 消息index
	 * @return array | null | false
	 */
	public function getMsgInfo ( $mid, $index, $isCache = true) {
		if ($isCache) {
			$catcher = Factory::getCacher();
			$catcherId = GlobalCatchId::MATERIAL_TEXT_INFO.implode('_',
					array( $mid, $index));
			$info = $catcher->get($catcherId);
			if ($info) {
				return $info;
			}
		}
		try {
			$db = self::$_db;
			$sql = "SELECT * FROM `wx_material_news` WHERE `material_id` = '{$mid}' AND `news_index` = {$index} AND `is_deleted` = 0";
			$ret = $db->getRow($sql);
			if (!$ret) {
				return null;
			}
			if ($isCache) {
				$catcher->set($catcherId, $ret, GlobalCatchExpired::MATERIAL_TEXT_INFO);
			}
			return $ret;
		} catch (Exception $e) {
			Logger::error('MsgTextModel->getMsgInfo error: '.$e->getMessage()."\n sql: ".$db->getLastSql());
			return false;
		}
	}
	/**
	 * 获取消息正文信息
	 * @param array $entAppInfo  企业app info
	 * @param int $mid 消息id
	 * @param int $index 消息index
	 * @return array | null | false
	 */
	public function getPreviewMsgInfo ( $mid, $index) {
		try {
			$db =self::$_db;
			$sql = "SELECT * FROM `wx_material_news_preview` WHERE `material_id` = '{$mid}' AND `news_index` = {$index}";
			$ret = $db->getRow($sql);
			if (!$ret) {
				return null;
			}
			return $ret;
		} catch (Exception $e) {
			Logger::error('MsgTextModel->getPreviewMsgInfo error: '.$e->getMessage()."\n sql: ".$db->getLastSql());
			return false;
		}
	}

	/**
	 * 获取原文链接参数
	 * @param string $target 原文url
	 * @param array $entAppInfo
	 * @param array $params
	 * @return string
	 */
	public function getNewsOriginalUrl ($target, $entAppInfo, $monitorData, $params) {
		if (!$target) return null;
		$queryData = array(
				MonitorHttpParams::MONITOR_DATA => implode(MonitorParams::DELIMITER, $monitorData),
				MonitorHttpParams::OPEN_ID => $params[MonitorHttpParams::OPEN_ID]
				);
		$queryData = getQrcAuthQueryData($entAppInfo['api_key'], $entAppInfo['api_secret'], $queryData);
		$queryData[MonitorHttpParams::M_FROM] = $params[MonitorHttpParams::M_FROM];
		$queryData[MonitorHttpParams::OAUTHED] = $params[MonitorHttpParams::OAUTHED];
		$queryData[MonitorHttpParams::TARGET] = $target;
		return ConfigBase::NEWS_ORIGINAL_URL.'?'.http_build_query($queryData);
	}

}