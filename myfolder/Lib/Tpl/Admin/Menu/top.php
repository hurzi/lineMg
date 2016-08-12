<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>导航</title>
<link href="./Public/css/top.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="topnav">
	<div class="sitenav">
		<div class="welcome">你好，<span class="username"><?php echo(UHome::getUserName()) ;?></span></div>
		<div class="sitelink">
			<a href="<?php echo url('Index', 'index');?>" target="_blank">网站主页</a> |
            <a target="mcMainFrame"  href="<?php echo url('Setting', 'clear');?>">清除缓存</a>  |
            <a href="<?php echo url('Login', 'modifyPassword');?>" target="mcMainFrame" >修改密码</a>  |
			<a href="<?php echo url('Login', 'logout');?>" target="_top">安全退出</a>
		</div>
	</div>
</div>
</body>
</html>