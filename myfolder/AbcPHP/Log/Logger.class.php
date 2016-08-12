<?php
/**
 * 微信 web logger 类
 *
 */
include_once ABC_PHP_PATH . '/Log/LogBase.class.php';
class Logger extends LoggerBase
{

	public static function init()
	{
		self::setLogDir(AbcPHPConfig::getLogDir());
		self::enabled(AbcPHPConfig::get('ENABLE_RUN_LOG'));
		self::setLogLevel(AbcPHPConfig::get('RUN_LOG_LEVEL'));
		parent::init();
	}
}
