<?php
define('APP_PLATE', 'WXApp');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';
include_once LIB_PATH.'/../AbcPHP/CPU/LinePHPCPU.class.php';
include_once LIB_PATH . '/Config/Line.Config.php';
include_once LIB_PATH . '/../AbcPHP/LineAPI/LINEBot.php';

LinePHPCPU::run();
