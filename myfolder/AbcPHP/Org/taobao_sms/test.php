<?php
	include "TopSdk.php";
	date_default_timezone_set('Asia/Shanghai'); 

	$httpdns = new HttpdnsGetRequest;
	$client = new ClusterTopClient("23299811","047602f853cd8a04155b14d15a99df21");
	//$client->gatewayUrl = "http://api.daily.taobao.net/router/rest";
	$client->format="json";
	
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setExtend("123456");
	$req->setSmsType("normal");
	$req->setSmsFreeSignName("注册验证");
	$req->setSmsParam('{"code":"1234","produdct":"随视"}');
	$req->setRecNum("15101019215");
	$req->setSmsTemplateCode("SMS_4471603");
	var_dump($client->execute($req));

?>