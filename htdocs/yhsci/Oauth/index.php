<?php
$startTime = microtime(true);
define("APP_GROUP", 'Oauth');
include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';
include_once LIB_PATH . '/Action/Oauth/OAuthAction.class.php';

AbcPHP::run();