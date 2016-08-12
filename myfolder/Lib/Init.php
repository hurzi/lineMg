<?php
define("LIB_PATH", dirname(__FILE__));
define("APP_NAME", 'weixindemo');
//define("WEB_PATH", 'http://localhost/hysci/');
define("WEB_PATH", 'http://'.$_SERVER['HTTP_HOST'].'/yhsci/');
define('CDN_PATH','http://'.$_SERVER['HTTP_HOST'].'/yhsci/');


error_reporting(E_ALL);
ini_set("display_errors", true);

include_once LIB_PATH . '/../AbcPHP/AbcPHP.class.php';
include_once LIB_PATH . '/../AbcPHP/Log/SystemLog.class.php';

include_once LIB_PATH . '/WXApp/Manager.class.php';
include_once LIB_PATH . '/WXApp/MessageManager.class.php';
include_once LIB_PATH . '/WXApp/FilterManager.class.php';
include_once LIB_PATH . '/WXApp/QueueManager.class.php';
include_once LIB_PATH . '/WXApp/DispatcherManager.class.php';

include_once LIB_PATH.'/Common/AbcFactory.class.php';
include_once LIB_PATH.'/Common/Functions.php';
include_once LIB_PATH.'/Common/ThirdPartyTools.class.php';
include_once LIB_PATH.'/Common/MonitorTools.class.php';
include_once LIB_PATH.'/Common/MessageTools.class.php';
include_once LIB_PATH .'/Common/PublicFunction.php';
include_once LIB_PATH . '/Common/Page.class.php';
include_once LIB_PATH . '/Common/UHome.php';
include_once LIB_PATH . '/Common/Face.class.php';


include_once LIB_PATH.'/Config/Define.Config.php';
include_once LIB_PATH.'/Config/Abc.Config.php';

$config = include LIB_PATH.'/Config/Base.Config.php';

if (defined("PHP_SHELL_RUN") && true == PHP_SHELL_RUN) {
	C('RUN_SHELL', true);
	C('ENABLE_SYSTEM_LOG', false);
}

if(defined("APP_GROUP")){
	$config["APP_GROUP"] = APP_GROUP ;
}
$config['LOG_PATH'] = dirname(__FILE__)."/../log/";
//本地运行(临时)
if($config['LOCAL_RUNING']){
	$config['DB_HOST'] = 'localhost';
	$config['DB_USER'] = 'root';
	$config['DB_PASSWORD'] = 'root';
	$config['DB_NAME'] = 'yhsci';
	//$config['LOG_PATH'] = 'E:/project/hyscilog/';
}

//是否需要微信支付
if($config['NEED_WXPAY']){
	include_once LIB_PATH . '/Config/WxPay.Config.php';
	include_once LIB_PATH . '/../AbcPHP/API/WxPay/WxPay.Exception.php';
	include_once LIB_PATH . '/../AbcPHP/API/WxPay/WxPay.Data.php';
	include_once LIB_PATH . '/../AbcPHP/API/WxPay/WxPay.Notify.php';
	include_once LIB_PATH . '/../AbcPHP/API/WxPay/WxPay.Api.php';
}

AbcPHP::init($config);


Factory::getSystemLog()->start();
Factory::getSystemLog()->push("http param", HttpRequest::get());