<?php
class AiSuoFactory extends Factory
{
	protected static $_sendMessageInstance;
	
	protected static $apiClientInstance;
	
	public static function getSendMessage(){
		if (!class_exists("SendMessage")) {
			include dirname(__FILE__) . '/SendMessage.class.php';
		}
		if(!self::$_sendMessageInstance) {
				self::$_sendMessageInstance = new SendMessage(C("APP_ID"), C("APP_SECRET"));
		}
		return self::$_sendMessageInstance;
	}
	
	/**
	 * 获取api调用的实例
	 */
	public static function getApiClient(){		
		if(!self::$apiClientInstance) {
			$token = getToken(C("APP_ID"), C("APP_SECRET"));
			self::$apiClientInstance = WeiXinApiCore::getClient(C("APP_ID"), C("APP_SECRET"), $token);			
		}
		return self::$apiClientInstance;
	}
}