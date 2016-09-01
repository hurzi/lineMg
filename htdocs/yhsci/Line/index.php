<?php
define('APP_GROUP', 'Line');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';
include_once LIB_PATH . '/Action/Line/BaseAction.class.php'; //导入Manage系统的父类Action
include_once LIB_PATH . '/Config/Line.Config.php';

define('URL',CDN_PATH.APP_GROUP."/");

AbcPHP::run();



