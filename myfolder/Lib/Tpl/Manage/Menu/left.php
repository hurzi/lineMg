<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>菜单</title>
<link type="text/css" rel="stylesheet" href="./Public_1/css/publice.css?v=<?php echo VERSION;?>" />
<script type="text/javascript" src="./Public_1/js/jquery.min.js?v=<?php echo VERSION;?>"></script>
</head>
<body>
<div class="menu">
		<h3 class="mut gd_t"><i class="ic kf"></i>素材管理</h3>
		<ul class="menu_ul">
				</li>
				<li id="operatorgroup-index">
						<a target="mainFrame" href="index.php?a=MaterialNews&amp;m=index">图文素材</a>
				</li>
		</ul>
		<h3 class="mut gd_t"><i class="ic kf"></i>自定义菜单管理</h3>
		<ul class="menu_ul">
				<li id="user-index" >
						<a target="mainFrame" href="index.php?a=CustomMenu&amp;m=index">自定义菜单管理</a>
				</li>
				<li id="user-index" >
						<a target="mainFrame" href="index.php?a=ZgykdxEvaluating&m=index">调查管理</a>
				</li>
		</ul>
		<h3 class="mut gd_t"><i class="ic kf"></i>管理</h3>
		<ul class="menu_ul">
				<!-- <li id="operatorgroup-index">
						<a target="mainFrame" href="index.php?a=CWish">祝福管理</a>
				</li> -->
				<li id="operatorgroup-index">
						<a target="mainFrame" href="index.php?a=SysAlarm">系统告警</a>
				</li>
		</ul>
</div>
<script type="text/javascript">
	var __menu_timer__ = 1000;
	$(function(){
		$(".menu h3").click(function(){
		    //$(".menu h3").removeClass('gd_t');
			$(this).toggleClass("gd_t");
			$(this).siblings("h3").removeClass("gd_t");

			$(this).next("ul").slideToggle("slow")
			.siblings("ul:visible").slideUp("slow");
			setTimeout(MenuHeight, __menu_timer__);
		});
		$('.menu > a,.menu li').click(function(){
			$('.menu .active').removeClass('active');
			$(this).toggleClass("active");
			window.top.loadMf($('.con_if'));
			//$(this).siblings("h3").removeClass("active");
		});
		autoMenuOpen();
	});
	//自动展开菜单
	function autoMenuOpen () {
		try {
			//当不是菜单并且需要开新tab时定义
			var linkMap = {
					'materialnews-add': {
						'key': 'materialnews-index'//菜单中选中项id格式 a-m 小写
						}
					}
			var target = '<?php echo @$target;?>';
			var targetUrl = '<?php echo @$targetUrl;?>';
			var indexUrl = '<?php echo url('Index');?>';
			if (!target) return;
			var obj = $("#"+target);
			var href = '';
			if (linkMap[target]) {
				obj = $('#'+linkMap[target]['key']);
			}
			//菜单中存在
			if (obj.length > 0) {
				href = targetUrl || $("#"+target+" a").attr('href');
			} else {
				window.top.location.href = indexUrl;
				return;
			}
			obj.parent().show();
			obj.find('a').click();
			window.top.$('#mainFrame').attr('src', href);
			MenuHeight();
		} catch (e){}
	}

	function menuSelected (key) {
		var obj = $("#"+key);
		if (!obj) {
			return;
		}
		if (obj.parent().css('display') == 'none') {
			obj.parent(). prev().click();
		}
		$('.menu .active').removeClass('active');
		$(obj).toggleClass("active");
		setTimeout(MenuHeight, __menu_timer__);
	}
	function MenuHeight () {
		var hh = $('body').height();
		window.top.conMenuH(hh);
	}
</script>
</body>

</html>