<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微信.客服呼叫中心 - 管理中心</title>
<script type="text/javascript" src="./Public_1/js/jquery.min.js?v=2.2.4"></script>
<link type="text/css" rel="stylesheet" href="./Public_1/css/publice.css?v=2.2.4" />
<link type="text/css" rel="stylesheet" href="./Public_1/css/index.css?v=2.2.4" />
<link type="text/css" rel="stylesheet" href="./Public_1/css/button.css?v=2.2.4" />
<link type="text/css" rel="stylesheet" href="./Public_1/css/style.css?v=2.2.4"/>
<script type="text/javascript" src="./Public_1/js/jquery.weixinH.js?v=2.2.4"></script>
<script type="text/javascript" src="./Public_1/js/common.js?v=2.2.4"></script>
<!--加载提示-->
<link type="text/css" rel="stylesheet" href="./Public_1/cj/load/load.css?v=2.2.4" />
<script type="text/javascript" src="./Public_1/cj/load/load.js?v=2.2.4"></script>
<!--日期组件-->
<link type="text/css" rel="stylesheet" href="./Public_1/cj/date/css/zebra_datepicker_metallic.css?v=2.2.4" />
<script type="text/javascript" src="./Public_1/cj/date/javascript/zebra_datepicker.js?v=2.2.4"></script>
<!--弹出框-->
<link type="text/css" rel="stylesheet" href="./Public_1/cj/jsbox/jsbox.css?v=2.2.4" />
<script type="text/javascript" src="./Public_1/cj/jsbox/jsbox.js?v=2.2.4"></script>
<!--播放音频组件-->
<script type="text/javascript" src="./Public_1/cj/Jplayer/jquery.jplayer.min.js" ></script>
<!-- 表情 -->
<script type="text/javascript" src="./Public/js/WxFace/wxFace.js?v=2.2.4"></script>
<!--单图文-->
<link type="text/css" rel="stylesheet" href="./Public_1/css/Amass.css?v=2.2.4" />

<link type="text/css" rel="stylesheet" href="./Public/js/Group/GroupSelector.css?v=2.2.4"/>
<link type="text/css" rel="stylesheet" href="./Public_1/cj/EasyUI/css/icon.css?v=2.2.4" />
<link type="text/css" rel="stylesheet" href="./Public_1/cj/EasyUI/css/easyui.css?v=2.2.4" />
<link type="text/css" rel="stylesheet" href="./Public_1/css/sessionHistoryDetail.css?v=2.2.4" />
<script type="text/javascript" src="./Public_1/cj/ckplayer/ckplayer.js?v=2.2.4"></script>
<script type="text/javascript" src="./Public_1/js/materialSelecter.js?v=2.2.4"></script>
<script type="text/javascript" src="./Public_1/cj/MsgEditor/messageSelector.js?v=2.2.4"></script>
<script type="text/javascript" src="./Public_1/js/qrCodeParam.js?v=2.2.4"></script>
<script type="text/javascript" src="./Public/js/Group/GroupSelector.js?v=2.2.4"></script>
<script type="text/javascript" src="./Public_1/cj/EasyUI/jquery.easyui.min.js?v=2.2.4"></script>

<script>
if (!window.top || window.top == window) {
	var redirect = '<?php echo @$rederectTarget?$rederectTarget:'';?>';
	if (redirect) {
		window.location.href = redirect;
	}
}
function menuSelected (key) {
	window.top.$('#left_frame')[0].contentWindow.menuSelected(key);
}
</script>
</head>
<?php if (@$isPopup == 1) {?>
<body style="background-color: #fcfcfc;">
<?php } else {?>
<body>
<?php }?>
