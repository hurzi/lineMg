<?php

/**
 +----------------------------------------------------------
 * 把返回的数据集转换成Tree
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
	// 创建Tree
	$tree = array();
	if(is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] =& $list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] =& $list[$key];
			}else{
				if (isset($refer[$parentId])) {
					$parent =& $refer[$parentId];
					$parent[$child][] =& $list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 生成与第三方通讯认证参数
 * @return array
 */
function getAuthQueryData($param = array())
{
	$apiKey = C('API_KEY');
	$apiSecret = C('API_SECRET');
	$timestamp = time();

	$param[AuthQueryData::REQUEST_AUTH_API_KEY] = $apiKey;
	$param[AuthQueryData::REQUEST_AUTH_TIMESTAMP] = $timestamp;
	$param[AuthQueryData::REQUEST_AUTH_SIGNATURE] = getAuthSig($apiKey, $apiSecret, $param);

	return $param;
}

/**
 * 生成带参数二维码签名
 * @return 值升序
 */
function getAuthSig($apiKey, $apiSecret, $param)
{
	$param = array_merge(array_values($param), array (
			$apiKey,
			$apiSecret
	));
	$param = array_unique($param);
	foreach ($param as $key => $p) {
		$param[$key] = (string) $p;
	}
	sort($param, SORT_STRING);
	$str = implode('', $param);
	return md5($str);
}

/**
 * 获取当前openid
 * @return Ambigous <boolean, string, unknown>
 */
function getCurrOpenid($getByOauth = false){
	$openid = HttpRequest::get("openid");
	if($openid){
		$cookieid = md5($openid);
		$sendRet = setcookie(AbcConfig::COOKIE_UID_TOKEN,$cookieid,time()+3600*24*365*10);
		Factory::getCacher()->set($cookieid,$openid,AbcConfig::OPENID_VALID_DURTION);		
		return $openid;
	}

	$cookieToken = @$_COOKIE[AbcConfig::COOKIE_UID_TOKEN];
	if(!$cookieToken){
		//重新登录
		if($getByOauth){
			$currUrl = curPageURL();
			oauthJumpFun($currUrl);
			exit;
		}else{
			Logger::error("系统错误[没有获取到微信唯一ID]");
			return false;
		}
	}
	$openid = Factory::getCacher()->get($cookieToken);
	if(!$openid){
		Logger::error("cookieID[".$cookieToken."]存在，缓存中没有openid");
		//重新登录
		if($getByOauth){
			$currUrl = curPageURL();
			oauthJumpFun($currUrl);
			exit;
		}else{			
			Logger::error("系统错误[没有获取到微信唯一ID]");
			return false;
		}
	}

	return $openid;
}

function oauthJumpFun($link){
	$app_id = C('APP_ID');
	$redirect_uri = Config::REDIRET_URI.'&url='.urlencode($link);
	//TODO test
	$matchs = array('APP_ID','REDIRET_URI');
	$replace = array($app_id, urlencode($redirect_uri));
	$wxurl = str_replace($matchs, $replace, Config::WX_AUTH_PATH);
	Logger::debug('Oauth wxUrl:'.$wxurl);
	
	header("location:".$wxurl);
}

/**
 * 获得当前的url
 * @return string
 */
function curPageURL()
{
	$pageURL = 'http';

	if (isset($_SERVER["HTTPS"]) &&  $_SERVER["HTTPS"] == "on")
	{
		$pageURL .= "s";
	}
	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80")
	{
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	}
	else
	{
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}