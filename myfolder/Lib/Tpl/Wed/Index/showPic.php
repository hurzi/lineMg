<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 	<title>温馨婚纱照</title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
    <meta id="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1, user-scalable=no" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">

<link type="text/css" href="css/pic.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="css/common.css">

<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/jquery.event.drag-1.5.min.js"></script>
<script type="text/javascript" src="js/jquery.touchSlider.js"></script>
<?php tpl('Index.header');?>
<script type="text/javascript">
$(document).ready(function(){
	$(".main_visual").height($(window).height());
	
	$(".main_visual").hover(function(){
		$("#btn_prev,#btn_next").fadeIn();
	},function(){
		$("#btn_prev,#btn_next").fadeOut();
	});
	
	$dragBln = false;
	
	$(".main_image").touchSlider({
		flexible : true,
		speed : 200,
		btn_prev : $("#btn_prev"),
		btn_next : $("#btn_next"),
		paging : $(".flicking_con a"),
		counter : function (e){
			$(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
		}
	});
	
	$(".main_image").bind("mousedown", function() {
		$dragBln = false;
	});
	
	$(".main_image").bind("dragstart", function() {
		$dragBln = true;
	});
	
	$(".main_image a").click(function(){
		if($dragBln) {
			return false;
		}
	});
	
	timer = setInterval(function(){
		$("#btn_next").click();
	}, 5000);
	
	$(".main_visual").hover(function(){
		clearInterval(timer);
	},function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		},5000);
	});
	
	$(".main_image").bind("touchstart",function(){
		clearInterval(timer);
	}).bind("touchend", function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		}, 5000);
	});
	
});
</script>
</head>
<body style="height: 100%">

<div class="main_visual">
	<div class="flicking_con">
		<a href="#">1</a>
		<a href="#">2</a>
		<a href="#">3</a>
		<a href="#">4</a>
		<a href="#">5</a>
		<a href="#">6</a>
		<a href="#">7</a>
		<a href="#">8</a>
	</div>
	<div class="main_image">
		<ul>
			<li><span class="img_1"></span></li>
			<li><span class="img_2"></span></li>
			<li><span class="img_3"></span></li>
			<li><span class="img_4"></span></li>
			<li><span class="img_5"></span></li>
			<li><span class="img_6"></span></li>
			<li><span class="img_7"></span></li>
			<li><span class="img_8"></span></li>
		</ul>
		<a href="javascript:;" id="btn_prev"></a>
		<a href="javascript:;" id="btn_next"></a>
	</div>
</div>
<audio id="bgsound" src="images/music.mp3" autoplay="" loop=""></audio>
    <dl class="bgsoundsw">
        <dt></dt>
        <dd></dd>
    </dl>
<!--main_visual end-->
<?php tpl('Index.base_bottom');?>
<div style="text-align:center;">
</div>
<?php tpl('Index.footer');?>
</body>
</html>