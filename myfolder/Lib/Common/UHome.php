<?php
/**
 * 当前登录用户操作类
 * @author paizhang
 * 2012-06-23
 */
class UHome
{
		//cookie 设置
	private static $C_DOMAIN = '';
	private static $C_PATH = '/';
	const C_USER_ID = 'C_U_ID';
	const C_USER_NAME = 'C_U_NAME';
	const C_USER_LEVEL = 'C_U_LEVEL';
	const C_EXPIRE = 0;

	private static $USER_ID;
	
	private static $USER_NAME;
	private static $USER_LEVEL;
	
	private static $IS_LOGIN = false;

	public static function getUserId(){
		return self::$USER_ID;
	}

	public static function getUserName(){
		return self::$USER_NAME;
	}

	public static function getUserLevel()
	{
		return self::$USER_LEVEL;
	}
	public static function isLogin(){
		return self::$IS_LOGIN;
	}
	
	
	public static function setUser($user){
		if(!$user->userId || !$user->userName){
				return;
		}
		self::$USER_ID=$user->userId;
		self::$USER_NAME=$user->userName;
		self::$USER_LEVEL = $user->userLevel;
		self::setUserToCookie();
	}
	
	//初始化
	public static function init()
	{
		self::initData();
		if (! self::$USER_ID || ! self::$USER_NAME) {
			self::$IS_LOGIN = false;
			self::logout();
		}else{
			self::setUserToCookie();
			self::$IS_LOGIN = true;
		}
	}
	
	//获取cookie
	private static function getCookie($name)
	{
		$data = trim(@$_COOKIE[$name]);
		if ($data) {
			$data = base64_decode($data);
		}
		return $data;
	}
	

	//设置cookie信息
	private static function setUserToCookie(){
		self::setCookie(self::C_USER_ID, self::$USER_ID);
		self::setCookie(self::C_USER_NAME, self::$USER_NAME);
		self::setCookie(self::C_USER_LEVEL, self::$USER_LEVEL);
	}	
	
	//初始化cookie信息
	private static function initData(){
		self::$USER_ID = (int) self::getCookie(self::C_USER_ID);
		self::$USER_NAME = self::getCookie(self::C_USER_NAME);
		self::$USER_LEVEL = self::getCookie(self::C_USER_LEVEL);
	}

	//退出，清除cookie
	public static function logout(){
		self::setCookie(self::C_USER_ID, null, - 100);
		self::setCookie(self::C_USER_NAME, null, - 100);
		self::setCookie(self::C_USER_LEVEL, null, - 100);

	}
	
	
	//设置cookie
	private static function setCookie($name, $value, $expire = self::C_EXPIRE)	{
		$value = base64_encode($value);
		return setcookie($name, $value, $expire, self::$C_PATH, self::$C_DOMAIN);
	}

	public static function getEntId () {
		return Config::ENT_ID;
	}
}

class UHome_User{
	public $userId; //当前系统用户id
	public $userName; //当前系统用户名
	public $userLevel; //当前用户等级
}



