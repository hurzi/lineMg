<?php
/**
 * 这里是正文内容预览文件
 */
$startT = microtime(true);
define("APP_GROUP", 'Oauth');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';


define("__ACTION_NAME__", 'Index');
define("__ACTION_METHOD__", 'index');
$actionName = 'Index';
if (AbcPHPConfig::getAppGroup()) {
	$actionName = AbcPHPConfig::getAppGroup() . '.' . $actionName;
}
loadAction($actionName)->index($startT);