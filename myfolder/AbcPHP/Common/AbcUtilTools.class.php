<?php
/**
 * 这里可以获取业务中一些常用全局数据信息
 */

class AbcUtilTools
{
	//private static $_accountToInfo;//这个是由account_id获取到的APPinfo信息
	//private static $_appIdToInfo;//这个是由app_id获取到的APPinfo信息
	//private static $_apiKeyToInfo;//这个是由app_id获取到的APPinfo信息
	
	/**
	 * 获取核心API接口对象
	 * @return NULL|WeiXinApi
	 */
	public static function getApiClient(){
		$appId = C('APP_ID');
		$appSecret = C('APP_SECRET');
		$token = getToken($appId, $appSecret);
		if (!$token) {
			Logger::error(__METHOD__.' getToken error:appid/$appSecret: ' . $appId."/".$appSecret);
			return null;
		}		
		$apiClient = WeiXinApiCore::getClient($appId, $appSecret, $token);
		if (!$apiClient) {
			Logger::error(__METHOD__.' AbcUtilTools:getApiClient error: ,token/appid/appsecret:['.$token.'/'.$appId.'/'.$appSecret.']' );
			return null;
		}
		return $apiClient;
	}
	/**
	 * 获取卡券APIclient接口对象
	 * @return NULL|WeiXinCardApi
	 */
	public static function getCardApiClient() {
		
		$appId = C('APP_ID');
		$appSecret = C('APP_SECRET');
		$token = getToken($appId, $appSecret);
		if (!$token) {
			Logger::error(__METHOD__.' getToken error:appid/$appSecret: ' . $appId."/".$appSecret);
			return null;
		}
		$wxCardApi = WeiXinApiCore::getCardClient($appId, $appSecret, $token);
		if (!$wxCardApi) {
			Logger::error(__METHOD__.' AbcUtilTools:getCardApiClient error: ,token/appid/appsecret:['.$token.'/'.$appId.'/'.$appSecret.']' );
			return null;
		}
		return $wxCardApi;
	}
	/**
	 * 获取默认的appinfo对象
	 * @return Ambigous <boolean, multitype:boolean string NULL multitype: multitype:string  >
	 */
	public static function getDefaultAppInfo(){
		$appinfo['app_id'] = C('APP_ID');
		$appinfo['app_secret'] = C('APP_SECRET');
		$appinfo['app_name'] = C('APP_NAME');
		return $appinfo;
	}
	
	/**
	 * @param string $accountId 微信公众号account_id
	 * @return array false为出错，null为$accountId不存在
	 */
	public static function getAppInfoByAccount ($accountId) {
		return self::getDefaultAppInfo();
	}
	
	/**
	 * @param string $appId 微信公众号appid
	 * @return array false为出错，null为appid不存在
	 */
	public static function getAppInfoByAppId ($appId) {
		return self::getDefaultAppInfo();
	}

	/**
	 * 根据api key获账号app信息
	 * @param string $apiKey
	 * @return array
	 */
	public static function getAppInfoByKey($apiKey) {
		return self::getDefaultAppInfo();
	}
	public static function getCardTicket ($appId, $appInfo = null) {
		if (!$appId) {
			Logger::error(__METHOD__. ' appid empty see:'.getBacktrace());
			return null;
		}
		$cacheId = GlobalCatchId::APP_CARD_TICKET . $appId;
		$cacher = Factory::getGlobalCacher();
		$ticket = $cacher->get($cacheId);
		if ($ticket) {
			if (is_array($ticket) && @$ticket['ticket']) {
				$ticket = $ticket['ticket'];
			}
			Logger::debug(__METHOD__.' cache_card_ticket:'.$ticket.'  app_id:'.$appId);
			return $ticket;
		}
		$appInfo = self::getDefaultAppInfo();
		
		$wxCardApi = self::getCardApiClient();
		$ticket = $wxCardApi->getCardTicket();
		if (!$ticket) {
			Logger::error(__METHOD__. ' wx_api_getcardticket_error:'.WeiXinApiRequest::$response);
			return null;
		}
		$cacher->set($cacheId, $ticket['ticket'], GlobalCatchExpired::APP_CARD_TICKET);
		Logger::debug(__METHOD__.' api_card_ticket:'.$ticket['ticket'].'  app_id:'.$appId);
		return $ticket['ticket'];
	}
	
	
}