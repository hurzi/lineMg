<!DOCTYPE html>
<html>
<head>
<title><?php echo $message['title'];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<meta
	content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0"
	name="viewport">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<link href="<?php echo Config::CDN_MATERIAL_CACHE_CSS;?>" type="text/css" rel="stylesheet">
</head>
<style>
body {
	background: none repeat scroll 0 0 #F8F7F5;
	color: #222222;
	font-family: Helvetica, STHeiti STXihei, Microsoft JhengHei,
		Microsoft YaHei, Tohoma, Arial;
	height: 100%;
	padding: 15px 15px 0;
	position: relative;
}
.activity-info .text-ellipsis {
    display: inline-block;
    max-width: 104px;
    overflow: hidden;
    white-space: nowrap;
}
#post-user {
    color: #607FA6;
    text-decoration: none;
    margin-left: 8px;
    font-size: 11px;
}
</style>
<body id="activity-detail">
	<div class="page-bizinfo">
		<div class="header">
			<h1 id="activity-name"><?php echo $message['title'];?></h1>
			<p class="activity-info" style="vertical-align: middle; line-height: 12px;">
				<span id="post-date" class="activity-meta no-extra"><?php echo date('Y-m-d');?></span>
				<!--
				<a href="javascript:viewProfile();" id="post-user" class="activity-meta">
                    <span class="text-ellipsis"><?php echo @$entAppInfo['app_weixin_name'];?></span><i class="icon_link_arrow"></i>
                </a>
                 -->
			</p>
		</div>
	</div>
	<div class="page-content">
		<?php if($message['show_hdimg']==1){?>
		<div id="media" class="media">
			<img src="<?php echo $message['picurl'];?>" />
		</div>
		<?php }?>
		<div class="text"><?php echo $message['news_text'];?></div>
		<?php if (@$info['originalUrl']){?>
		<p class="page-url"><a  class="page-url-link" href="<?php echo $info['originalUrl'];?>">阅读原文</a></p>
		<?php }?>
	</div>
</body>
<?php if (@$info['fackId']) {?>
<script type="text/javascript">
var __ABC_TRACKER_SET__ = {
	title: "<?php echo $message['title']; ?>",//分享标题
	desc: <?php echo json_encode($message['description']);?>,//分享描述
	imgUrl: "<?php echo $message['picurl']; ?>",//分享图片
	link: "",//分享url
	appId: '<?php echo $entAppInfo['app_id'];?>',//微信appid
	hideOptionMenu: false,
	hideToolbar: false,
	fackId: "<?php echo @$info['fackId'];?>",
	timelineAfter: null,//分享朋友圈后callbcak
	friendAfter: null, //转发好友后callback
	fn: '__ABC_TRACKER__.text'
};

function viewProfile() {
	var appFromUser = "<?php echo @$entAppInfo['app_from_user'];?>";
	if (!appFromUser) {
		return ;
	}
	if (typeof WeixinJSBridge != "undefined" && WeixinJSBridge.invoke){
		WeixinJSBridge.invoke('profile',{
		'username': appFromUser,
		'scene':'57'});
     }
}

</script>
<script type="text/javascript" src="<?php echo Config::NEWS_MONITOR_JS_PATH;?>?v=1.1"></script>
<?php }?>
</html>
