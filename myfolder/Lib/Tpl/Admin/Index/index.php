<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微信企业应用系统</title>
<script type="text/javascript">
//处理iframe内部重叠
if(window.top && window.top != window){
	window.top.location.href = window.location.href;
}
</script>
<script type="text/javascript" src="./Public_1/js/jquery.min.js?v=<?php echo VERSION;?>"></script>
<link type="text/css" rel="stylesheet" href="./Public_1/css/publice.css?v=<?php echo VERSION;?>" />
<!--加载提示-->
<link type="text/css" rel="stylesheet" href="./Public_1/cj/load/load.css?v=<?php echo VERSION;?>" />
<script type="text/javascript" src="./Public_1/cj/load/load.js?v=<?php echo VERSION;?>"></script>
<!--弹出框-->
<link type="text/css" rel="stylesheet" href="./Public_1/cj/jsbox/jsbox.css?v=<?php echo VERSION;?>" />
<script type="text/javascript" src="./Public_1/cj/jsbox/jsbox.js?v=<?php echo VERSION;?>"></script>
<!--图片弹出-->
<script type="text/javascript" src="./Public_1/cj/LigImg/LigImg.js?v=<?php echo VERSION;?>"></script>
<link type="text/css" rel="stylesheet" href="./Public_1/css/button.css?v=<?php echo VERSION;?>" />

<script type="text/javascript" src="./Public_1/cj/showMsg/showSendInfo.js?v=<?php echo VERSION;?>"></script>
<style >
.top_span{
	font-size:12px;
	font-weight:normal;
	padding-left: 10px;
}
.top_color{
	color:#E0E2E6;
}
</style>
</head>
<body>
	<div class="zon_top">
		<div class="zon_top_nav w_z">
			<div class="logo"></div>
			<div class="user">
				<div class="ud">
					<span></span>
					<span class='top_span'>
						<a class="top_color" href="<?php echo url('Login', 'alterPassword');?>" target="mainFrame">修改密码</a>
					</span>
					<span class='top_span'>
						<a class="top_color" href="<?php echo url('Login', 'logout');?>" target="_top">安全退出</a>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="zon_con w_z m_h_700">
		<div class="clearF">
			<div class="con_l w_l">
				<iframe id="left_frame" class="menu_if m_h_700" frameborder="0" scrolling="no" width="100%" src="<?php echo url('Menu', 'left', array('target'=>HttpRequest::get('target'), 'targetUrl'=>HttpRequest::get('targetUrl',null, false, 'all')));?>"></iframe>
			</div>
			<div class="con_con w_c">
				<iframe class="con_if m_h_700" id="mainFrame" name="mainFrame"frameborder="0" scrolling="no" width="100%" src="<?php echo (HttpRequest::get('target') ? '' : url('Index', 'welcome'));?>"></iframe>
			</div>
		</div>
	</div>
	<footer class="footer">
		Copyright©2008-2011 
	</footer>

	<!--页面执行-->
	<script type="text/javascript">
	$(function(){
		loadMf($('.con_if'));
		//弹出层统一关闭
		$('.uniteC').live('click',function(){
		     $('.jsbox_close').click();
	    });
	});
	//高度控制
	function conIfH(hh){
		if(hh<200)hh=200;
		$('.con_if').height(hh);
	}

	function conMenuH(hh) {
		if(hh<700)hh=700;
		$('.menu_if').height(hh);
	}

	//关闭弹出层
	function close_tcc(){
 		$('.jsbox_close').click();
	}
	//提示
	function qrOks(texts){

	}
	//初始化图片弹出
	var ligImg = new LigImg();

	//iframe弹出层
	function maptss(title,url,conw,conh){
		if(!conw){conw = 500};
		if(!conh){conh = 500};
		var wb = new jsbox({
		onlyid:"maptss",
		title:title,
		conw:conw,
		conh:conh,
		FixedTop:170,
		url:url,
		iframe:true,
		range:true,
		mack:true
		}).show();
	}
    </script>
</body>
</html>