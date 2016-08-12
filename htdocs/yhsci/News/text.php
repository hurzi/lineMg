<?php
/**
 * 这里是显示正文内容文件
 */
$startT = microtime(true);
define("APP_GROUP", 'News');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';


define("__ACTION_NAME__", 'MsgText');
define("__ACTION_METHOD__", 'index');
$actionName = 'MsgText';
if (AbcPHPConfig::getAppGroup()) {
	$actionName = AbcPHPConfig::getAppGroup() . '.' . $actionName;
}
loadAction($actionName)->index($startT);
