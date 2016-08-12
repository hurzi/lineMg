<?php
header("Content-Type: text/html; charset=utf-8");
date_default_timezone_set('PRC');
//判断LIP_PATH是否定义
if (! defined("LIB_PATH") || ! LIB_PATH) {
	exit('请定义常量 LIB_PATH 路径后重试');
}
//判断LIP_PATH是否定义
if (! defined("APP_NAME") || ! APP_NAME) {
	exit('请定义常量 APP_NAME 路径后重试');
}
define("ABC_PHP_PATH", dirname(__FILE__));

include_once ABC_PHP_PATH . '/Config/AbcPHPConfig.class.php';
include_once ABC_PHP_PATH . '/Common/FunctionsBase.php';
include_once ABC_PHP_PATH . '/Common/HttpRequest.class.php';
include_once ABC_PHP_PATH . '/Common/Factory.class.php';
include_once ABC_PHP_PATH . '/Common/Base.class.php';
include_once ABC_PHP_PATH . '/Common/Model.class.php';
include_once ABC_PHP_PATH . '/Common/Action.class.php';
include_once ABC_PHP_PATH . '/Common/Template.class.php';
include_once ABC_PHP_PATH . '/Common/Helper.class.php';
include_once ABC_PHP_PATH . '/Common/AbcBase.php';

include_once ABC_PHP_PATH . '/Log/Logger.class.php';
include_once ABC_PHP_PATH . '/DB/MySql.class.php';

include_once ABC_PHP_PATH . '/API/WeiXinApiCore.class.php';
include_once ABC_PHP_PATH . '/CPU/AbcPHPCPU.class.php';

class AbcPHP
{

	/**
	 * 运行
	 */
	public static function run()
	{
		self::_run();
	}

	/**
	 * 运行
	 */
	public static function wx()
	{
		AbcPHPCPU::run();
	}

	/**
	 * 验证通过后运行数据层
	 */
	protected static function _run()
	{
		$actionName = AbcPHPConfig::getAction();
		$method = AbcPHPConfig::getMethod();

		define("__ACTION_NAME__", $actionName);
		define("__ACTION_METHOD__", $method);
		define("__APP_GROUP__", AbcPHPConfig::getAppGroup());
		if (AbcPHPConfig::getAppGroup()) {
			$actionName = AbcPHPConfig::getAppGroup() . '.' . $actionName;
		}
		$action = loadAction($actionName);
		
		if (! $action || ! method_exists($action, $method)) {
			if (AbcPHPConfig::get('DEBUGGING') === true) {
				Logger::error('_run error: action not exist, action: ' . __ACTION_NAME__ . ', method: ' . __ACTION_METHOD__, HttpRequest::get());
				throw new Exception('action not exist, action: ' . __ACTION_NAME__ . ', method: ' . __ACTION_METHOD__);
			} else {
				Logger::error('_run error: action not exist, action: ' . __ACTION_NAME__ . ', method: ' . __ACTION_METHOD__, HttpRequest::get());
				myExit(); //TODO () 转向到404 页面
			}
		}
		$action->$method(HttpRequest::get());
	}

	/**
	 * 初始化配置数据
	 * @param  $config
	 */
	public static function init($config)
	{
		AbcPHPConfig::setArray($config);
		// set php log
		$phpLogDir = AbcPHPConfig::getPhpLogDir();
		if ($phpLogDir) {
			if (is_dir($phpLogDir)) {
				if (! is_writable($phpLogDir)) {
					trigger_error("set php log path is not writable： " . $phpLogDir, E_USER_WARNING);
				} else {
					//设置php log 路径文件
					ini_set("error_log", $phpLogDir . date('Y-m-d') . ".log");
				}
			} else {
				if (! @mkdir($phpLogDir, 0777, true)) {
					trigger_error("create php log path error： " . $phpLogDir, E_USER_WARNING);
				} else {
					//设置php log 路径文件
					ini_set("error_log", $phpLogDir . date('Y-m-d') . ".log");
				}
			}
		}
		HttpRequest::init();
		Logger::init();
		Template::init();
	}
}