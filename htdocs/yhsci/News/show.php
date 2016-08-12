<?php
/**
 * 这里是正文内容预览文件
 */
$startT = microtime(true);
define("APP_GROUP", 'News');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';


define("__ACTION_NAME__", 'MsgText');
define("__ACTION_METHOD__", 'show');
$actionName = 'MsgText';
if (AbcPHPConfig::getAppGroup()) {
	$actionName = AbcPHPConfig::getAppGroup() . '.' . $actionName;
}
loadAction($actionName)->show($startT);