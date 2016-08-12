<!DOCTYPE html>
<html>
<head>
    <title>参加婚礼</title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
    <meta id="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1, user-scalable=no" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    
    <link rel="stylesheet" type="text/css" href="css/common.css">
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/wish.css?version=<?=VERSION?>">
	<script type="text/javascript" src="js/common.js?version=<?=VERSION?>"></script>
	<script type="text/javascript" src="js/ylMap.js?version=<?=VERSION?>"></script>
	<script type="text/javascript" src="js/load/load.js?version=<?=VERSION?>"></script>
	<link rel="stylesheet" type="text/css" href="js/load/load.css?version=<?=VERSION?>">
	<?php tpl('Index.header');?>	
</head>
<body  style="height: 100%">
<div  style="height: 100%" class="common_bg">
    <div class="main"  style="height: 100%">
        <div class="m-map">
				<div id="ylMap" class="ylMap"></div>
				<div class="mapVal ">
					<input class="address" type="hidden"
						value="{'sign_name':'','contact_tel':'0730-8272666','address':'岳阳市区新陶园酒店'}">
					<input class="latitude" type="hidden" value="29.380263"> 
					<input class="longitude" type="hidden" value="113.138623">
				</div>
				<div id="transit_result"></div>				
		</div>
		<div style="text-align: center;">
			<br/>
			<span style="font-size: 20px;margin-top: 20px;">欢迎来参加婚礼</span><br/><br/>
			<span style="font-size: 30px;margin-top: 20px;">何钟强&杨华</span><br/><br/>
			<span style="font-size: 16px;margin-top: 10px">婚礼时间：2016年2月15(正月初八)</span><br/>
			<span style="font-size: 16px;margin-top: 10px">婚礼地点：岳阳市新陶园酒店二楼宴会厅</span>	<br/>		
		</div>
    </div>
    <?php tpl('Index.base_bottom');?>
</div>
<?php tpl('Index.footer');?>

<script type="text/javascript">
var mapS;
$(function(){
	mapS = new ylmap.init;
})

function tip (tipid, mesg) {
	 $('#'+tipid).html(mesg);
}
</script>
</body>
</html>