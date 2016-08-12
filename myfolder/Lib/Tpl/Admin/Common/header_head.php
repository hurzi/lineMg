<?php if ($action == 'AdminUserPermission') { ?>
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/icon.css" />
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/easyui.css" />
	<script type="text/javascript" src="./Public/js/EasyUI/jquery.easyui.min.js"></script>
<?php } else if ($action == 'AdminUser') { ?>
	<script type="text/javascript" src="./Public/js/adminUser.js"></script>
<?php } else if ($action == 'Monitor' || $action == 'MonitorTwo') { ?>
	<script type="text/javascript" src="./Public/js/monitor.js"></script>
<?php } else if ($action == 'Operator' || $action == 'OperatorTwo') { ?>
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/icon.css" />
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/easyui.css" />
	<script type="text/javascript" src="./Public/js/EasyUI/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="./Public/js/operator.js"></script>
<?php } else if ($action == 'OperatorGroup' || $action == 'OperatorGroupTwo') { ?>
	<script type="text/javascript" src="./Public/js/operatorGroup.js"></script>
<?php } else if ($action == 'UserGroup') { ?>
	<script type="text/javascript" src="./Public/js/userGroup.js"></script>
<?php } else if ($action == 'User') { ?>
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/icon.css" />
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/easyui.css" />
	<script type="text/javascript" src="./Public/js/EasyUI/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="./Public/js/user.js"></script>
<?php } else if ($action == 'SessionGroup') { ?>
	<script type="text/javascript" src="./Public/js/sessionGroup.js"></script>
<?php } else if ($action == 'SessionHistory') { ?>
	<link type="text/css" rel="stylesheet" href="./Public/css/date/css/zebra_datepicker.css" />
	<script type="text/javascript" src="./Public/js/date/javascript/zebra_datepicker.js" ></script>
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/icon.css" />
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/easyui.css" />
	<script type="text/javascript" src="./Public/js/EasyUI/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="./Public/js/addImage.js"></script>
	<script type="text/javascript" src="./Public/js/jquery.jplayer.min.js"></script>
<?php } else if ($action == 'ReportOperator') { ?>
	<link type="text/css" rel="stylesheet"href="./Public/css/date/css/zebra_datepicker.css" />
	<link type="text/css" rel="stylesheet" href="./Public/css/jsbox.css" />
	<link type="text/css" rel="stylesheet" href="./Public/css/jquery.windows-engine.css" />
	<link type="text/css" rel="stylesheet" href="./Public/css/twShow.css" />
	<script type="text/javascript" src="./Public/js/date/javascript/zebra_datepicker.js"></script>
	<script type="text/javascript" src="./Public/js/jquery.windows-engine.js"></script>
	<script type="text/javascript" src="./Public/js/showImg.js"></script>
	<script type="text/javascript" src="./Public/js/jsbox.js"></script>
<?php } else if ($action == 'ReportUser') { ?>
	<!--日期组件-->
	<link type="text/css" rel="stylesheet" href="./Public/css/date/css/zebra_datepicker.css" />
	<link type="text/css" rel="stylesheet" href="./Public/css/load.css" />
	<script type="text/javascript" src="./Public/js/date/javascript/zebra_datepicker.js"></script>
	<script type="text/javascript" src="./Public/js/addImage.js"></script>
	<!--图表组件-->
	<script type="text/javascript" src="./Public/js/highcharts.js"></script>
<?php } else if ($action == 'MassMessage') { ?>

<?php } else if ($action == 'MassHistory') { ?>
	<link type="text/css" rel="stylesheet"href="./Public/css/date/css/zebra_datepicker.css" />
	<link type="text/css" rel="stylesheet" href="./Public/css/jsbox.css" />
	<link type="text/css" rel="stylesheet" href="./Public/css/jquery.windows-engine.css" />
	<link type="text/css" rel="stylesheet" href="./Public/css/twShow.css" />
	<script type="text/javascript" src="./Public/js/date/javascript/zebra_datepicker.js"></script>
	<script type="text/javascript" src="./Public/js/jquery.windows-engine.js"></script>
	<script type="text/javascript" src="./Public/js/showImg.js"></script>
	<script type="text/javascript" src="./Public/js/jsbox.js"></script>
<?php }else if ($action == 'BranchAllot'){?>
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/icon.css" />
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/easyui.css" />
	<script type="text/javascript" src="./Public/js/EasyUI/jquery.easyui.min.js"></script>
<?php }else if ($action == 'BranchUser'){?>
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/icon.css" />
	<link type="text/css" rel="stylesheet" href="./Public/js/EasyUI/css/easyui.css" />
	<script type="text/javascript" src="./Public/js/EasyUI/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="./Public/js/user.js"></script>
<?php }else if ($action == 'ReportSession'){?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<link type="text/css" rel="stylesheet"href="./Public/css/date/css/zebra_datepicker.css" />
	<script type="text/javascript" src="./Public/js/date/javascript/zebra_datepicker.js"></script>
	<!--图表组件-->
	<script type="text/javascript" src="./Public/js/highcharts.js"></script>
	<script type="text/javascript" src="./Public/js/reportSes.js"></script>
<?php }else if ($action == 'CustomMenu'){?>
	<link href="./Public/css/publice.css" type="text/css" rel="stylesheet" />
	<link href="./Public/css/index.css" type="text/css" rel="stylesheet" />
	<link href="./Public/css/button.css" type="text/css" rel="stylesheet" />
	
	
	<!--弹出框-->
	<link type="text/css" rel="stylesheet" href="./Public/cj/jsbox/jsbox.css" />
	<link type="text/css" rel="stylesheet" href="./Public/cj/tjj/Tjj.css" />
	<link type="text/css" rel="stylesheet" href="./Public/css/load2.css" />
	<script type="text/javascript" src="./Public/cj/jsbox/jsbox.js"></script>
	<script type="text/javascript" src="./Public/cj/tjj/jquery.zn.js"></script>
	<script type="text/javascript" src="./Public/js/jquery.weixinH.js"></script>
	<script type="text/javascript" src="./Public/js/load2.js"></script>
	
	<!-- 查看图文 -->
	<link type="text/css" rel="stylesheet" href="./Public/css/twShow.css" />
	<script type="text/javascript" src="./Public/js/showImg.js"></script>

	<script type="text/javascript" src="./Public/js/customMenu.js"></script>
<?php }?>

