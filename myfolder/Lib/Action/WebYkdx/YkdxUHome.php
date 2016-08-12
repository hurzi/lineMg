<?php
/**
 * 当前登录用户操作类
 * @author paizhang
 * 2012-06-23
 */
class YkdxUHome
{
	//cookie 名称定义
	const C_CACHE_USER_NAME = 'C_CACHE_U_NAME';
	
	const C_USER_ID = 'C_U_ID';
	const C_OPENID = 'C_OPENID';
	const C_USER_NAME = 'C_U_NAME';

	const C_SIGNATURE = 'SSH_SIG';

	const RANDOM_STR = 'ZHP,MXG,ZHPENG,GRH,ZHX,ZHTP';

	const C_EXPIRE = 0;
	const C_U_NAME_EXPIRE = 1000000000;  //用户名的存储有效期
	//cookie 设置
	private static $C_DOMAIN = '';

	private static $C_PATH = '/';


	private static $CACHE_USER_NAME;  //cookie缓存用户名的变量
	
	private static $USER_ID;
	private static $OPENID;
	private static $USER_NAME;

	private static $IS_LOGIN = false;
	private static $IS_REGIST = false;

	public static function getUserId()
	{
		return self::$USER_ID;
	}

	public static function getOpenid()
	{
		return self::$OPENID;
	}
	public static function getUserName()
	{
		return self::$USER_NAME;
	}
	
	public static function isLogin()
	{
		return self::$IS_LOGIN;
	}
	
	public static function isRegist()
	{
		return self::$IS_REGIST;
	}

	public static function setPath($path)
	{
		if (! empty($path)) {
			self::$C_PATH = $path;
		}
	}
	
	/**
	 * 设置当前用户信息
	 * @param UHome_User $user
	 */
	public static function setUser($user,$saveLoginName = false)
	{
		if (  ! $user->openid) {
			return;
		}
		self::$USER_ID = $user->userId;
		self::$OPENID = $user->openid;
		self::$USER_NAME = $user->userName;
		self::setUserToCookie();
		
		if($saveLoginName){
			//明文存储用户名
			setcookie(self::C_USER_NAME, self::$USER_NAME,time()+self::C_U_NAME_EXPIRE, self::$C_PATH, self::$C_DOMAIN);
		}
	}
	
	//退出，清除cookie
	public static function logout()
	{
		self::setCookie(self::C_SIGNATURE, null, - 100);

		self::setCookie(self::C_USER_ID, null, - 100);
		self::setCookie(self::C_USER_NAME, null, - 100);
		self::setCookie(self::C_OPENID, null, - 100);
		
	}

	//初始化
	public static function init()
	{
		self::initData();
		$sig = self::getCookie(self::C_SIGNATURE);
		if (! $sig || ! self::$OPENID) {
			self::logout();
			return;
		}
		if (self::checkSignature($sig)) {
			self::$IS_LOGIN = true;
			if(self::$USER_ID){
				self::$IS_REGIST = true;
			}
		} else {
			self::logout();
		}
	}

	//初始化cookie信息
	private static function initData()
	{
		
		self::$USER_ID = (int) self::getCookie(self::C_USER_ID);
		self::$USER_NAME = self::getCookie(self::C_USER_NAME);
		self::$OPENID = self::getCookie(self::C_OPENID);
	}

	//验证签名
	private static function checkSignature($sig = null)
	{
		$sig or $sig = self::getCookie(self::C_SIGNATURE);
		if (! $sig) {
			return false;
		}
		return ($sig == self::genSignature());
	}

	//设置cookie信息
	private static function setUserToCookie()
	{
		self::setCookie(self::C_SIGNATURE, self::genSignature());
		
		self::setCookie(self::C_USER_ID, self::$USER_ID);
		self::setCookie(self::C_USER_NAME, self::$USER_NAME);
		self::setCookie(self::C_OPENID, self::$OPENID);
	}

	//生成签名
	private static function genSignature()
	{
		return md5(self::$USER_ID . self::$USER_NAME . self::$OPENID);
	}

	//设置cookie
	private static function setCookie($name, $value, $expire = self::C_EXPIRE)
	{
		$value = base64_encode($value);
		if ($value) {
			if (strlen($value) >= 4) {
				//前两位和后两位对调
				$pre = substr($value, 0, 2);
				$last = substr($value, -2);
				$value = substr_replace(substr_replace($value, $last, 0, 2), $pre, -2, 2);
			}
			$value = getRandStr(2).$value.getRandStr(4);
		}
		return setcookie($name, $value, $expire, self::$C_PATH, self::$C_DOMAIN);
	}

	//获取cookie
	private static function getCookie($name)
	{
		$data = trim(@$_COOKIE[$name]);
		if ($data) {
			if (strlen($data) > 6) {
				$data = substr_replace($data, '', 0, 2);//前两位去掉
				$data = substr_replace($data, '', -4, 4);//后四位去掉
				if (strlen($data) >= 4) {
					//前两位和后两位对调
					$pre = substr($data, 0, 2);
					$last = substr($data, -2);
					$data = substr_replace(substr_replace($data, $last, 0, 2), $pre, -2, 2);
				}	
			}
			$data = @base64_decode($data);
		}
		return $data;
	}
}

class YkdxUHome_User
{
	public $userId; //当前系统用户id
	public $userName; //当前系统用户名
	public $openid; //当前系统openid
}
