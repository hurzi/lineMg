<?php
/**
 * 这里是进入图文原文入口文件
 */
$startT = microtime(true);
define("APP_GROUP", 'News');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';

define("__ACTION_NAME__", 'MsgOriginal');
define("__ACTION_METHOD__", 'index');
$actionName = 'MsgOriginal';
if (AbcPHPConfig::getAppGroup()) {
	$actionName = AbcPHPConfig::getAppGroup() . '.' . $actionName;
}
loadAction($actionName)->index($startT);