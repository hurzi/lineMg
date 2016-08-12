<?php
/**
 * 微信api获取实例
 */
set_time_limit(0);
include_once ABC_PHP_PATH . '/API/WeiXinStruct.class.php';
class WeiXinApiCore
{
	public static function getClient($app_id, $app_secret, $token = NULL)
	{
		$client = null;
		include_once ABC_PHP_PATH . '/API/WeiXinApi.class.php';
		$client = new WeiXinApi($app_id, $app_secret, $token);
		return $client;
	}

	/**
	 * 获取组件服务的client
	 * @param unknown $app_id 组件应用id
	 * @param unknown $app_secret 组件应用密钥
	 * @param unknown $ticket 组件应用ticket
	 * @param string $token 组件应用token
	 * @return WeiXinComponectApi
	 */
	public static function getComponectClient($app_id, $app_secret,$ticket, $token = NULL)
	{
		$client = null;
		include_once ABC_PHP_PATH . '/API/WeiXinComponentApi.class.php';
		$client = new WeiXinComponectApi($app_id, $app_secret,$ticket, $token);
		return $client;
	}
	
	/**
     * 获取OAuth 授权方式api client
     * @param string $app_id
     * @param string $app_secret
     * @param int $version
     * @return WeiXinOAuthApi
     */
	public static function getOAuthClient($app_id, $app_secret, $version = 1)
	{
		include_once ABC_PHP_PATH . '/API/WeiXinOAuthApi.class.php';
		return new WeiXinOAuthApi($app_id, $app_secret);
	}

    /**
        * 获取卡券api client
        * @param string $app_id  公众号app id
        * @param string $app_secret 公众号app secret
        * @return WeiXinCardApi
        */
   	public static function getCardClient($app_id, $app_secret, $access_token = NULL)
   	{
   		include_once ABC_PHP_PATH . '/API/WeiXinCardApi.class.php';
   		return new WeiXinCardApi($app_id, $app_secret,$access_token);
   	}

    /**
     * @param string $app_id 公众号app id
     * @param string $token 授权token
     * @return WeiXinShakearoundApi
     */
    public static function getShakearoundClient($app_id,$token){
        include_once ABC_PHP_PATH . '/API/WeiXinShakearoundApi.class.php';
        return new WeiXinShakearoundApi($app_id,$token);
    }
    
    /**
     * @param string $app_id 公众号app id
     * @param string $token 授权token
     * @return WeiXinPoiApi
     */
    public static function getPoiClient($app_id, $app_secret, $token){
    	include_once ABC_PHP_PATH . '/API/WeiXinPoiApi.class.php';
    	return new WeiXinPoiApi($app_id, $app_secret, $token);
    }


}