<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>微信授权提示</title>
<style>
*{
	font-size:0.875em;
	font-family:Verdana, Geneva, sans-serif;
}
body,h1,h2,h3,h4,h5,h6,p{
	margin:0;
	padding:0;
}
a{
	text-decoration:none;
}
body{
	background:#e6e3dc;
	width:100%;
}
.tt{
	margin:0 auto;
	width:320px;
}
.tb{
	margin:10% 0 0  auto;
	text-align: center;
}
.wz{
	text-align:center;
	color:#626262;
}
.wz p{
	text-align:center;
	color:#626262;
	line-height:180%;
	padding-top:15%;
	font-size:1.7em;
}
.wz p a{
	color:#626262;
}
.wz p a span{
	color:#37b03d;
	text-decoration:underline;
	font-size:1.1em;
	font-weight:bold;
}
.no{
	background: none repeat scroll 0 0 #D4D4CC;
    border: 1px solid #ABA8A1;
    border-radius: 5px 5px 5px 5px;
    margin: 10% auto;
    padding: 19px;
    text-align: center;
	width:70%
}
.no p{
	color: #626262;
    font-size: 1.7em;
}
.tb_1, .tb_2{
	font-weight:bold;
	font-size: 16px;
}
.c_red{
	color: red;
}
img{ vertical-align:middle;}
</style>
</head>
<body>
<div class="tt">
        <div class="tb">
            <img src="http://pic.weibopie.com/imgUpload/weixin/upload/20140721/14059382549125.png" alt="img" width="35%"  />
        </div>
        <div class="wz">
        </div>
        <?php if (@$message) {?>
        <div class="no" style="padding-top:10px;">
        	<p>
        	<?php echo @$message;?></p>
        </div>
        <?php }?>
    </div>
</body>
</html>
