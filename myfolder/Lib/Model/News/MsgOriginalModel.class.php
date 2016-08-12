<?php
/**
 * 这里是图文消息原文处理model
 */
class MsgOriginalModel extends Model
{
	private static $_db;
	
	public function __construct(){
		self::$_db = Factory::getDb();
	}
	/**
	 * 验证参数并
	 */
	public function auth () {
		$apiKey = HttpRequest::get(Config::REQUEST_AUTH_API_KEY);
		$timestamp = HttpRequest::get(Config::REQUEST_AUTH_TIMESTAMP);
		$sig = HttpRequest::get(Config::REQUEST_AUTH_SIGNATURE);
		$monitorData = (string)HttpRequest::get(MonitorHttpParams::MONITOR_DATA, '', false, 'all');
		$openId = (string)HttpRequest::get(MonitorHttpParams::OPEN_ID, '', false, 'all');
		$target = trim(HttpRequest::get(MonitorHttpParams::TARGET, '', false, 'all'));
		$MFrom = trim(HttpRequest::get(MonitorHttpParams::M_FROM, '', false, 'all'));
		if (!$apiKey || !$timestamp || !$sig || !$monitorData || !$openId || !$target) {
			Logger::error('MsgOriginalModel->auth params incomplete or invalid', HttpRequest::get());
			return false;
		}
		$authData = array(
				Config::REQUEST_AUTH_API_KEY => $apiKey,
				Config::REQUEST_AUTH_TIMESTAMP => $timestamp,
				MonitorHttpParams::MONITOR_DATA => $monitorData,
				MonitorHttpParams::OPEN_ID => $openId
		);
		$newSig = getQrcAuthSig($apiKey, $entAppInfo['api_secret'], $authData);
		if ($newSig != $sig) {
			Logger::error('MsgOriginalModel->auth error: invalid sig', HttpRequest::get());
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
									MonitorHttpParams::OAUTHED => $oauthed,
									MonitorHttpParams::TARGET => $target
							)
					);
		if ($useOauth && (int)$oauthed != 1) {
			$entAppInfo['scope'] = $this->getOAuthScope($entAppInfo['ent_id']);
			if (!$entAppInfo['scope']) {
				Logger::error('MsgOriginalModel->getOAuthScope error: '.$this->getError(), HttpRequest::get());
				return false;
			}
			$oauthRedirect = $this->getOAuthRedirectUrl(Config::NEWS_ORIGINAL_IRL, $entAppInfo, $monitorData, $params);
		}
		$params[MonitorHttpParams::FACK_ID] = $this->uniqid('O_');
		return array('ent_info'=>$entAppInfo,
					'monitor_data'=> $monitorData,
					'oauth_redirect' => $oauthRedirect,
					'params' => $params
				);
	}
	/**
	 * 生成原文的目标地址
	 * @param array $entAppInfo
	 * @param array $monitorData
	 * @param array $params
	 * @return string
	 */
	public function getTargetUrl ($entAppInfo, $monitorData, $params) {
		$target = @$params[MonitorHttpParams::TARGET];
		$queryData = array(
				MonitorHttpParams::MONITOR_DATA => implode(MonitorParams::DELIMITER, $monitorData),
				MonitorHttpParams::OPEN_ID => $params[MonitorHttpParams::OPEN_ID]
		);
		$queryData = getQrcAuthQueryData($entAppInfo['api_key'], $entAppInfo['api_secret'], $queryData);
		$queryData[MonitorHttpParams::M_FROM] = $params[MonitorHttpParams::M_FROM];
		$queryData[MonitorHttpParams::OAUTHED] = $params[MonitorHttpParams::OAUTHED];
		$queryData[MonitorHttpParams::FACK_ID] = $params[MonitorHttpParams::FACK_ID];
		return resetUrl($target, $queryData);
	}
}