<?php
define('APP_GROUP', 'WebYkdx');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';

include_once LIB_PATH.'/Action/WebYkdx/YkdxCommonAction.class.php';

define('URL',CDN_PATH.APP_GROUP."/");

AbcPHP::run();



