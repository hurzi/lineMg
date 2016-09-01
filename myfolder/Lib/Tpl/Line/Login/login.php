<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微信客服系统登录</title>
<script type="text/javascript">
//处理iframe内部重叠
if(window.top && window.top != window){
	window.top.location.href = window.location.href;	
}
</script>
<script type="text/javascript" src="./Public_1/js/jquery.min.js" ></script>
<style>
body {
    background-color: #434A5D;
}
* {
	margin: 0;
}
.login_zon {
    border-radius: 20px 20px 20px 20px;
    margin: 50px auto;
    min-height: 300px;
    width: 350px;
}
.qspps {
    animation: 5s linear 0s normal none infinite slidePPs;
}
.login_con {
    background: none repeat scroll 0 0 #FFFFFF;
    border-radius: 20px 20px 20px 20px;
    float: left;
    min-height: 250px;
    width: 350px;
	border: 1px solid #BDC5CD;
	padding-bottom: 5px;
}
.login_con h3 {
    border-bottom: 2px solid #CCCCCC;
    font-size: 21px;
    font-weight: bold;
    padding: 5px 20px;
}
.login_con p {
    padding: 5px 20px;
}
.login_con label {
    display: block;
    font-size: 14px;
    font-weight: bold;
    line-height: 21px;
}
.login_con .inps {
    border: 1px solid #CCCCCC;
    border-radius: 2px 2px 2px 2px;
    box-shadow: 1px 1px 2px #BBBBBB inset;
    height: 21px;
    padding: 3px;
    width: 300px;
}
input, textarea {
    outline: medium none;
}
body, input, textarea, select {
    font-family: "微软雅黑";
    font-size: 12px;
	color: #444444;
}
.btn3 {
    background: -moz-linear-gradient(center top , #7DB72F, #4E7D0E) repeat scroll 0 0 transparent;
    border: 1px solid #538312;
    color: #E8F0DE;
	background-color: #4E7D0E;
	border-radius: 3px 3px 3px 3px;
    color: #FFFFFF;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    font-weight: bold;
    padding: 5px 15px;
}
.verify{
	display: block;
    float: left;
    padding: 3px 3px 3px 15px;
    text-decoration: none;
    width: 90px;
}
.logo {
    background: url("./Public_1/images/logo.png") no-repeat scroll 0 0 transparent;
    float: left;
    height: 50px;
    margin: 10px 0 0 5px;
    width: 320px;
}

</style>
<script type="text/javascript">
$(document).ready(function(){
	$('#admin_name').focus();//文本框默认得到焦点
});
document.onkeydown = function(e){
	e = e ? e : window.event;
	var keyCode = e.which ? e.which : e.keyCode;
	if(keyCode == 13){
		submitLogin();
	}
}

function g(id) {
	return document.getElementById(id);
}
function fleshVerify(){
	var timenow = new Date().getTime();
	var url = "<?php echo url('Login', 'verify');?>";
	document.getElementById('verifyImg').src= url+'&'+timenow;
}
function checkform()
{
	if(""==g("admin_name").value)
	{
		alert("用户名不能为空！");
		return false;
	}
	if(""==g("admin_pwd").value)
	{
		alert("密码不能为空！");
		return false;
	}
	if(""==g("seccode").value)
	{
		alert("验证码不能为空！");
		return false;
	}
	return true;
}
function submitLogin(){
	if(checkform()){
		var admin_name = g('admin_name').value;
		var admin_pwd  = g('admin_pwd').value;
		var seccode    = g('seccode').value;
		jQuery.ajax({
			type:"POST",
			url :"<?php echo url('Login', 'doLogin');?>",
			data:{'admin_name':admin_name,'admin_pwd':admin_pwd,'seccode':seccode},
			dataType:"json",
			success:function(msg){
				if(msg.error == 0){
					top.location.href = "<?php echo url('Index', 'index');?>";
				}else{
					fleshVerify();
					alert(msg.msg);
				}
			}
		});
	}
}
</script>
</head>
<body>
	<div class="login_zon">
		<div style="margin-left:25px;" class="logo"></div>
		<div class="login_con qspps">
			<h3>登录</h3>
			<p><label >用户名</label><input class="inps" type="text" name="admin_name" id="admin_name" /></p>
			<p><label>密码</label><input class="inps" type="password" name="admin_pwd" id="admin_pwd" /></p>
			<p><label>验证码</label>
				<input class="inps" type="text" name="seccode" id="seccode" style="width:180px;float:left;" />
				<a href="javascript:fleshVerify();">
					<img src="<?php echo url('Login', 'verify');?>" name="verifyImg"
					border="0" id="verifyImg" title="如果您无法识别验证码，请点图片更换" />
					换一换
				</a>
			</p>
			<p style="margin-top:10px;clear:both;">
				<input class="btn3" type="button" value="登录" onclick="submitLogin()" />
			</p>
		</div>
	  </div>
</body>
</html>