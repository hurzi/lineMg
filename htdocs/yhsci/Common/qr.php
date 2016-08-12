<?php    
define('APP_GROUP', 'Common');

include_once dirname(__FILE__) . '/../../../myfolder/Lib/Init.php';
//生成二维码图像
include( ABC_PHP_PATH.'/Org/phpqrcode/phpqrcode.php');
//QRcode::png ('http://www.baidu.com');

$errorCorrectionLevel = 'L';
if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
	$errorCorrectionLevel = $_REQUEST['level'];

$matrixPointSize = 10;
if (isset($_REQUEST['w'])){
	$cc = $_REQUEST['w']/33.0;
	$matrixPointSize = min(max($cc, 1), 10);
}
if (isset($_REQUEST['url'])) {
	//it's very important!
	if (trim($_REQUEST['url']) == '')
		die('not param url');	
	$url = urldecode($_REQUEST['url']);
	Logger::info("create qr url:".$url);
	QRcode::png($url, false, $errorCorrectionLevel, $matrixPointSize);
} else {	
	echo 'not param url';
}
exit;

