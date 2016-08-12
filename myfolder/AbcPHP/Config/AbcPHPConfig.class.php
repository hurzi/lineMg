<?php
class AbcPHPConfig
{
	const ACTION_NAME = 'a';
	const METHOD_NAME = 'm';
	const TEMPLATE_DIR = '/Tpl/'; //以lib目录为根
	const TEMPLATE_PREFIX = '.php';
	const APP_NAME = APP_NAME;
	const APP_PLATE = APP_PLATE;

	protected static $_CONFIGS = array (
			'PUBLIC_SERVICE' => false, //是否为正式服务，否：cache不会启用redis
			'DEBUGGING' => false, //debug 模式
			'ENABLE_RUN_LOG' => TRUE, //是否开启运行日志
			'ENABLE_SQL_LOG' => TRUE, //是否开启sql日志
			'ENABLE_SYSTEM_LOG' => FALSE, //是否开启system日志
			'RUN_SHELL' => false, //运行方式是否为脚本方式
			'RUN_LOG_LEVEL' => LOG_E_ALL, //运行日志级别
			'LOG_PATH' => './', //日志目录，以“/”结束
			'PHP_CLI_PATH' => '/usr/local/php/bin/php', //php脚本命令
			//DB CONFIG'
			'DB_HOST' => '',
			'DB_USER' => '',
			'DB_PASSWORD' => '',
			'DB_NAME' => '',

			'DEFAULT_ACTION' => 'Index', //默认ACTION
			'DEFAULT_METHOD' => 'index', //默认METHOD
			'APP_GROUP' => '', //App GROUP
			'VAR_AJAX_SUBMIT' => 'ajax', //ajax请求标识
			'ON_INIT_AFTER' => NULL, //AbcPHP 初始化后调用callback(暂时无效)
			'DEFAULT_CACHER' => 'file', //默认cache方式,redis|file|remote
			//redis配置
			'REDIS_HOST' => '',
			'REDIS_PORT' => '',
			//远程cache方式配置
			'REMOTE_CACHE_HOST' => '', //URL
			'REMOTE_CACHE_PORT' => '',
			//file cache
			'FILE_CACHE_PATH' => '/tmp/', //文件缓存的目录
			//数据库配置
			'DB_CONFIGS' => array (),
			'WX_APP' => array (
					'EXEC_TYPE' => '', //file｜http
					'FILF_PATH' => '', //文件路径｜url
					'CLASS_NAME' => '', //类名
					'METHOD_NAME' => '', //方法名
					'CLASS_TYPE' => ''  //类执行方式instance｜static
			)
	);

	//数据库配置
	protected static $DB_CONFIGS = array ();

	/**
	 * 获取配置数据
	 */
	public static function get($name)
	{
		if (! $name) {
			return self::$_CONFIGS;
		}
		return @self::$_CONFIGS[$name];
	}

	/**
	 * 获取配置数据
	 */
	public static function set($name, $value)
	{
		if (! $name) {
			return false;
		}

		self::$_CONFIGS[$name] = $value;
		return true;
	}

	/**
	 * 设置配置数据
	 */
	public static function setArray($config)
	{
		if (! is_array($config)) {
			return;
		}
		foreach ($config as $key => $value) {
			if ('DB_CONFIGS' == $key) {
				self::setDbConfig($value);
			}
			@self::$_CONFIGS[$key] = $value;
		}
	}

	/**
	 * 获取db配置
	 * @param string $dbName
	 * @param string
	 */
	public static function setDbConfig($config)
	{
		if (! $config || ! is_array($config))
			return;
		self::$DB_CONFIGS = $config;
	}

	/**
	 * 获取db配置
	 * @param string $dbName
	 */
	public static function getDbConfig($dbName)
	{
		return @self::$DB_CONFIGS[$dbName];
	}

	/**
	 * 生成数据库配置数组
	 * @param string $host host:port
	 * @param string $user db user
	 * @param string $pass db password
	 * @param string $dbname
	 */
	public static function genDbConfig($host = '', $user = '', $pass = '', $dbname = '')
	{
		$host or $host = self::get('DB_HOST');
		$user or $user = self::get('DB_USER');
		$pass or $pass = self::get('DB_PASSWORD');
		return array (
				'DB_HOST' => $host,
				'DB_NAME' => $dbname,
				'DB_USER' => $user,
				'DB_PWD' => $pass
		);
	}

	/**
	 +----------------------------------------------------------
	 * 获得实际的操作名称
	 +----------------------------------------------------------
	 * @access private
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	public static function getAction()
	{
		$action = HttpRequest::get(self::ACTION_NAME);
		$action = ! empty($action) ? $action : self::get('DEFAULT_ACTION');
		return ucwords($action);
	}

	/**
	 * 获得实际的模块名称
	 * @access private
	 * @return string
	 */
	public static function getMethod()
	{
		$method = HttpRequest::get(self::METHOD_NAME);
		$method = ! empty($method) ? $method : self::get('DEFAULT_METHOD');
		return $method;
	}

	/**
	 * 获得实际的app_group名称
	 * @return string
	 */
	public static function getAppGroup()
	{
		return self::$_CONFIGS['APP_GROUP'];
	}

	//获取sql日志目录
	public static function getSqlLogDir()
	{
		return self::getBaseLogDir() . 'sql/';
	}
	//获取运行日志路径
	public static function getLogDir()
	{
		return self::getBaseLogDir() . 'log/';
	}
	//获取system日志路径
	public static function getSysLogDir()
	{
		if (true == self::$_CONFIGS['RUN_SHELL']) {
			$dir = self::$_CONFIGS['LOG_PATH'] . 'shell/' . self::APP_PLATE . '/';
		} else {
			$dir = self::$_CONFIGS['LOG_PATH'] . 'web/' . self::APP_PLATE . '/';
		}
		$dir .= 'sys/';
		return $dir;
	}
	//获取file cachess路径
	public static function getFileCacheDir()
	{
		$dir = self::get('FILE_CACHE_PATH');
		if (! $dir) {
			$dir = self::getBaseLogDir() . 'cache/';
		}
		return $dir;
	}
	//获取php日志路径
	public static function getPhpLogDir()
	{
		if (true == self::$_CONFIGS['RUN_SHELL']) {
			$dir = self::$_CONFIGS['LOG_PATH'] . 'shell/';
		} else {
			$dir = self::$_CONFIGS['LOG_PATH'] . 'web/';
		}
		$dir .= 'php_log/';
		return $dir;
	}
	//获取log基础目录
	private static function getBaseLogDir()
	{
		if (true == self::$_CONFIGS['RUN_SHELL']) {
			$dir = self::$_CONFIGS['LOG_PATH'] . 'shell/' . self::APP_PLATE . '/';
		} else {
			$dir = self::$_CONFIGS['LOG_PATH'] . 'web/' . self::APP_PLATE . '/';
		}

		if (self::$_CONFIGS['APP_GROUP']) {
			$dir .= self::$_CONFIGS['APP_GROUP'] . '/';
		}
		return $dir;
	}
}

/*错误级别*/
if (! defined('LOG_E_ALL')) {
	define('LOG_E_ALL', 10000);
	define('LOG_E_ERROR', 1);
	define('LOG_E_WARNING', 10);
	define('LOG_E_INFO', 20);
	define('LOG_E_DEBUG', 30);
}

/**
 * 微信event type
 * @author paizhang
 *
 */
class MessageEventType
{
	/**
	 * 订阅
	 * @var string
	 */
	const SUBSCRIBE = 'subscribe';
	/**
	 * 取消订阅
	 * @var string
	 */
	const UNSUBSCRIBE = 'unsubscribe';
	/**
	 * 自定义菜单拉取消息
	 * @var string
	 */
	const CLICK = 'click';
	/**
	 * 地理位置
	 * @var string
	 */
	const LOCATION = 'location';
	/**
	 * 带参数二维码
	 * @var string
	 */
	const SCAN = 'scan';
	/**
	 * 自定义菜单跳转链接
	 * @var string
	 */
	const VIEW = 'view';
	/**
	 * 模板消息结果
	 * @var string
	 */
	const TEMPLATESENDJOBFINISH = 'templatesendjobfinish';
	/**
	 * 群发消息结果
	 * @var string
	 */
	const MASSSENDJOBFINISH = 'masssendjobfinish';
}