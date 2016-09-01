<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="zh" class="no-js">
<!--<![endif]-->
<!-- begin HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo @$title;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<link rel="shortcut icon" href="favicon.ico"/>
<link href="<?=URL;?>css/font-awesome.min.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/simple-line-icons.min.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/bootstrap.min.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/uniform.default.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css">
<link href="<?=URL;?>css/darkblue.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" href="<?=URL;?>css/bootstrap-datepicker3.min.css?version=<?= VERSION ?>"/>
<link href="<?=URL;?>css/components.css?version=<?= VERSION ?>" id="style_components" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/plugins.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/layout.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/zstyle.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="<?=URL;?>css/fileinput.min.css?version=<?= VERSION ?>" />
<link href="<?=URL;?>css/jquery.fileupload.css?version=<?= VERSION ?>" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?=URL;?>plug/bootstrap-toastr/toastr.min.css?version=<?= VERSION ?>"/>

<!--[if lt IE 9]>
<script src="assets/global/plugins/respond.min.js?version=<?= VERSION ?>"></script>
<script src="assets/global/plugins/excanvas.min.js?version=<?= VERSION ?>"></script>
<![endif]-->
<script src="<?=URL;?>../Common/js/jquery-1.11.0.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/custom/common.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/jquery-ui.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/bootstrap.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/bootstrap-hover-dropdown.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/jquery.slimscroll.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/jquery.blockui.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/jquery.uniform.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/bootstrap-datepicker.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/metronic.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/layout.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/demo.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/components-pickers.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/page.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/fileinput.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>js/ui-alert-dialog-api.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>plug/bootstrap-toastr/toastr.min.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>plug/bootstrap-toastr/ui-toastr.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>plug/bootstrap-modal/js/bootstrap-modalmanager.js?version=<?= VERSION ?>"></script>
<script src="<?=URL;?>plug/bootbox/bootbox.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>plug/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/custom/category.js?version=<?= VERSION ?>"></script>

<!-- 自定义的公共js -->
<script type="text/javascript" src="<?=URL;?>../Common/js/ssCommon.js?version=<?= VERSION ?>"></script>

<!-- 管理后台自定义菜单js -->
<script src="<?=URL;?>js/custom/customMenu.js?version=<?= VERSION ?>"></script>

<!-- 富文本编辑器 -->
<link href="<?=URL;?>../Common/js/ueditor/css/umeditor.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="<?=URL;?>../Common/js/ueditor/umeditor.js"></script>
<script type="text/javascript" src="<?=URL;?>../Common/js/ueditor/umeditor.config.js"></script>
<script type="text/javascript" src="<?=URL;?>../Common/js/ueditor/umeditor.min.js"></script>
    

<!-- 素材选择器 -->
<script type="text/javascript" src="<?=URL;?>../Common/js/material/materialSelecter.js?version=<?= VERSION ?>"></script>
<script type="text/javascript" src="<?=URL;?>../Common/js/material/messageSelector.js?v=<?php echo VERSION;?>"></script>
<link type="text/css" rel="stylesheet" href="<?=URL;?>../Common/js/material/material.css?version=<?= VERSION ?>" />
<script type="text/javascript" src="<?=URL;?>../Common/js/material/wxFace.js?v=<?php echo VERSION;?>"></script>

<!-- 素材编辑器 -->
<script type="text/javascript" src="<?=URL;?>../Common/js/material/messageEditor.js?v=<?php echo VERSION;?>"></script>

</head>
<body class="page-boxed page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">
<!--[if lt IE 11]>
<div style="height:86px;">
<div id="J_BrowserPromotion" style="background:#ffed9a url('img/old-bg.png') repeat-x;height:86px;width:100%;position:absolute;z-index: 9999;">
<div id="J_BrowsersList" style="background:url('img/old-warning.png') no-repeat;width:530px;height:59px;margin:13px auto 14px;position:relative;left:-2px;">
<a class="Chrome" target="_blank" href="http://www.google.com/chrome/" style="position:absolute;right:158px;width:42px;height:59px;text-indent:-9999em;" title="下载 Chrome 最新版"">Chrome</a>
<a class="Firefox" target="_blank" href="http://www.mozilla.com/firefox/" style="position:absolute;right:106px;width:43px;height:59px;text-indent:-9999em;" title="下载 Firefox 最新版"">Firefox</a>
<a class="Safari" target="_blank" href="http://www.apple.com/safari/download/" style="position:absolute;right:56px;width:38px;height:59px;text-indent:-9999em;" title="下载 Safari 最新版"">Safari</a>
<a class="IE" target="_blank" href="http://windows.microsoft.com/zh-CN/internet-explorer/downloads/ie/" style="position:absolute;right:0;width:44px;height:59px;text-indent:-9999em;" title="下载 Internet Explorer 最新版">Internet Explorer</a></div></div></div>
<![endif]-->
<!-- begin head -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="./index.php">
			<img src="" alt="" class="logo-default" width="90px">
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<!-- BEGIN NOTIFICATION DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
					<a href="<?php echo url("Motice");?>" title="通知消息" class="dropdown-toggle" data-close-others="true">
					<i class="icon-envelope-open"></i>
					<?php if($unReadCount>0){?>
					<span class="badge badge-default"><?php echo $unReadCount>9?"9+":$unReadCount;?> </span>
					<?php } ?>
					</a>
					<ul class="dropdown-menu">
					</ul>
				</li>
				<!-- END NOTIFICATION DROPDOWN -->
				
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-user"></i>
					<span class="username username-hide-on-mobile"><?=UHome::getUserName()?> </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="<?=url('Agent', 'index')?>">
							<i class="icon-user"></i>修改基本信息 </a>
						</li>
						<li>
							<a href="<?=url('User', 'pass')?>">
							<i class="icon-key"></i>修改密码</a>
						</li>
						<li>
							<a href="<?=url('Login', 'loginOut')?>">
							<i class="icon-key"></i>退出</a>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- end head -->
<div class="clearfix"></div>