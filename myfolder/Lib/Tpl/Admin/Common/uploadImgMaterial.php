<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="./Public/js/jquery.js"></script>
<script src="./Public/js/jquery.windows-engine.js"
	type="text/javascript"></script>
<title>图片上传</title>
<style type="text/css">
.a_pop_box,.rt_pay_box,.put_box {
	text-align: center;
	padding-top: 40px;
	padding-bottom: 50px;
}

.a_pop_txt {
	font-size: 18px;
	color: #FB9C3C;
	margin-bottom: 20px;
}

.a_pop_else {
	margin: 0 auto;
	width: 260px;
	border: 1px solid #ccc;
	border-radius: 3px;
	background-color: #fafafa;
	padding: 8px 15px;
}

.put_txt {
	font-size: 14px;
	margin-bottom: 30px;
}

.put_txt strong {
	font-size: 16px;
	margin: 0 10px;
}
</style>
</head>

<body>
	<!-- 上传图片弹出层 -->
	<div class="rt_pay_box">
		<br />
		<br />
		<div class="wbtool_con2">
			<div class="wbimg">
				<form id="FormImgS" method="post" target="hidden_frames"
					enctype="multipart/form-data"
					action="<?php echo WEB_PATH;?>Common/UploadImage.php">
					<input type="file" id="AidImg" name="upfile" value="上传图片"
						onchange="uploadImg2()" style="left: 0px; top: 5px;" />
					<input type="hidden" name="printFormat" value="proxy">
					<input type="hidden" name="callback"
						value="parent.imgUpload_callback">
					<input type="hidden" name="proxy_url"
						value="http://<?php echo $_SERVER['SERVER_NAME']?>/Plugins/Location/imgCallback.php">
					<div class="sjjt" style="display: none;" id="imgError"></div>
				</form>
			</div>
			<div style="display: none;" class="wbtool_load">图片上传中</div>
			<!-- 图片上传等待 -->
			<p class="put_txt" style="margin-bottom: 5px; text-align: center; padding-left: 30px;">
				<span class="btn3" id="addImgId" onclick="commitImg();">提交</span>
				&nbsp;&nbsp;
				<span class="btn2" onclick="closeThis();">取消</span>
			</p>
		</div>
		<h1 class="a_pop_txt"></h1>
		<iframe name="hidden_frames" style="display: none;"></iframe>
	</div>
	<!-- 上传图片弹出层end -->
</body>
<script type="text/javascript">
	//没找到弹出框插件的传参设置  改用class取对象
	var news_num = $("div[class='window-container']",window.parent.document).attr('id');
	var a = parent.document.getElementById('closeIFrame');
	var file = false;
	function closeThis(){
		a.click();
	}

	function commitImg(){
		if(file==false){
			alert('请选择需要上传的图片');
			return;
		}
		$('.wbtool_load').show();
		$('#FormImgS').submit();
		$('.wbtool_load').hide();
	}
	function uploadImg2(){
		file = true;
	}

	//上传图片回调地址
	function imgUpload_callback(msg,type){
		if(type== false){
			return;
		}else{
			$("#upload_url_"+news_num,window.parent.document).attr('value',msg);
			a.click();
		}
	}
</script>
</html>
