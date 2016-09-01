<?php
define('APP_GROUP', 'Manage');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';
include_once LIB_PATH . '/Action/Manage/BaseAction.class.php'; //导入Manage系统的父类Action
include_once LIB_PATH . '/Model/Manage/BaseModel.class.php'; //导入Manage系统的父类Model

define('URL',CDN_PATH.APP_GROUP."/");

AbcPHP::run();



