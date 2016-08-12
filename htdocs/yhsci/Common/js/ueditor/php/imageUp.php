<?php
    header("Content-Type:text/html;charset=utf-8");
    error_reporting( E_ERROR | E_WARNING );
    date_default_timezone_set("Asia/chongqing");
    include "Uploader.class.php";
    //上传配置
    $config = array(
        "savePath" => (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')?(dirname(__FILE__)."/tmp/"):"/tmp/", //存储文件夹
        "maxSize" => 2000 ,                   //允许的文件最大尺寸，单位KB
        "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" ),//允许的文件格式
        'pathFormat' => 'ue_upload'
    );
    $up = new Uploader( "upfile" , $config );
    $info = $up->getFileInfo();
    if ('SUCCESS' != @$info['state']) {
    	uploadPrint($info);exit;
    }
    $url = 'http://pic.weibopie.com/business_manage/ueditor/uploader.php';
    $param = array('upfile'=>'@'.$info['url']);
    $result = request($url, $param);
    uploadPrint(json_decode($result, true));
    @unlink($info['url']);
    exit;
    
function uploadPrint ($result) {
	$callback = @$_GET['callback'];
	if($callback) {
		echo '<script>'.$callback.'('.json_encode($result).')</script>';
	} else {
		echo json_encode($result);
	}
}
    /**/
 function request($url, $params) {
 	$curl = curl_init ();
 	curl_setopt ( $curl, CURLOPT_URL, $url );
 	curl_setopt ( $curl, CURLOPT_TIMEOUT, 10 );
 	curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT,10 );
 	curl_setopt ( $curl, CURLOPT_POST, 1 );
 	curl_setopt ( $curl, CURLOPT_POSTFIELDS, $params );
 	$urlArr = parse_url ( $url );
 	$port = empty ( $urlArr ['port'] ) ? 80 : $urlArr ['port'];
 	curl_setopt ( $curl, CURLOPT_PORT, $port );
 	curl_setopt ( $curl, CURLOPT_HTTPHEADER, array ('Expect:') );
 	// 获取的信息以文件流的形式返回,不直接输出
 	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
 	$response = curl_exec ( $curl );
 	// 	$_HTTP_CODE = curl_getinfo($curl, CURLINFO_HTTP_CODE);
 	// 	$_HTTP_INFO = curl_getinfo($curl);
 	// 	$_HTTP_ERROR_CODE = curl_errno($curl);
 	// 	$_HTTP_ERROR = curl_error($curl);
 	// 	file_put_contents("D:/a.txt", "http_code[{$_HTTP_CODE}|$_HTTP_ERROR_CODE|$_HTTP_ERROR] ". $response);
 	curl_close($curl);
 	return $response;
 }
 function getFileType ($fileName) {
 	$type = substr($fileName, strrpos($fileName, '.'));
 	return strtolower(trim($type));
 }
