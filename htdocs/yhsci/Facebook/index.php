<?php
define('APP_GROUP', 'Facebook');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';
include_once LIB_PATH . '/Action/Facebook/BaseAction.class.php'; //导入Manage系统的父类Action
include_once LIB_PATH . '/Config/Facebook.Config.php';

define('URL',CDN_PATH.APP_GROUP."/");

AbcPHP::run();



