<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo sprintf(TITLE, @$title);?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="css/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="css/select2.css" rel="stylesheet" type="text/css"/>
<link href="css/login-soft.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="<?=URL;?>css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="<?=URL;?>css/darkblue.css" rel="stylesheet" type="text/css"/>
<link href="<?=URL;?>css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
<style>
.login-tip{
	display: inline-block;
	line-height: 34px;
	height: 34px;
	font-size: 14px;
	padding-left: 10px;
	font-weight: bold;
	display: none;
}
</style>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="<?=url('Index')?>">
	<img src="./img/logo250.png" alt=""/>
	</a>
</div>
<!-- END LOGO -->
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<form class="" action="javascript:;" method="post" onSubmit="login();return false">
		<h3 class="form-title">登录</h3>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">用户名</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="用户名" name="username" id="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">密码</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="密码" name="password" id="password"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">验证码</label>
			<div class="input-icon">
				<i class="fa fa-shield" style="left:0;"></i>
				<input type="text"  class="form-control placeholder-no-fix" id="verify" placeholder="验证码"  style="width: 55%;display: inline-block;float:left;"/>
				<img id="verifyImg" onclick="this.src='<?=url('Login','verify');?>'+'&t='+new Date().getTime()" src="<?=url('login','verify',array('t'=>time()))?>" style="vertical-align: middle;float:left;margin:2px 0 0 10px;"/>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="form-actions">
			<input id="login-btn" type="submit" class="btn blue pull-left" value="立即登录"></input>
			<span id="login-tip" class="font-red login-tip">提示信息</span>
		</div>
		</form>
		<!--
		<div class="forget-password">
			<h4><a href="javascript:;" id="register-btn">注册新账号</a></h4>
		</div>
		 -->
	
	<!-- END LOGIN FORM -->
	<!-- BEGIN FORGOT PASSWORD FORM -->
	<form class="forget-form" action="index.html" method="post">
		<h3>取回密码</h3>
		<div class="form-group">
			<div class="input-icon">
				<i class="fa fa-envelope"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="邮箱" name="email"/>
			</div>
		</div>
		<div class="form-actions">
			<button type="button" id="back-btn" class="btn">返回</button>
			<button type="submit" class="btn blue pull-right">提交</button>
		</div>
	</form>
	<!-- END FORGOT PASSWORD FORM -->
	<!-- BEGIN REGISTRATION FORM -->
	<form class="register-form" action="index.html" method="post">
		<h3>注册</h3>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">姓名</label>
			<div class="input-icon">
				<i class="fa fa-font"></i>
				<input class="form-control placeholder-no-fix" type="text" placeholder="姓名" name="fullname"/>
			</div>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">邮箱</label>
			<div class="input-icon">
				<i class="fa fa-envelope"></i>
				<input class="form-control placeholder-no-fix" type="text" placeholder="邮箱" name="email"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">地址</label>
			<div class="input-icon">
				<i class="fa fa-location-arrow"></i>
				<input class="form-control placeholder-no-fix" type="text" placeholder="地址" name="address"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">用户名</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="用户名" name="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">密码</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="register_password" placeholder="密码" name="password"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">确认密码</label>
			<div class="controls">
				<div class="input-icon">
					<i class="fa fa-check"></i>
					<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="确认密码" name="rpassword"/>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label>
			<input type="checkbox" name="tnc"/> 我已认真阅读并同意 <a href="javascript:;">《使用协议》</a>
			</label>
			<div id="register_tnc_error">
			</div>
		</div>
		<div class="form-actions">
			<button id="register-back-btn" type="button" class="btn">返回</button>
			<button type="submit" id="register-submit-btn" class="btn blue pull-right">马上注册</i>
			</button>
		</div>
	</form>
	<!-- END REGISTRATION FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
	 2015 &copy; 随视传媒
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../assets/global/plugins/respond.min.js"></script>
<script src="../../assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="<?=URL;?>js/jquery.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/jquery-migrate.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/bootstrap.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/jquery.blockui.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/jquery.uniform.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/jquery.cokie.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?=URL;?>js/jquery.validate.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/jquery.backstretch.min.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?=URL;?>js/select2.min.js?version=<?= VERSION ?>"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?=URL;?>js/metronic.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/layout.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/demo.js?version=<?= VERSION ?>" type="text/javascript"></script>
<script src="<?=URL;?>js/login-soft.js?version=<?= VERSION ?>" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {
  Metronic.init(); // init metronic core components
Layout.init(); // init current layout
  Login.init();
  Demo.init();
       // init background slide images
       $.backstretch([
        "img/1.jpg",
        "img/2.jpg",
        "img/3.jpg",
        "img/4.jpg"
        ], {
          fade: 1000,
          duration: 8000
    }
    );
});
</script>
<script type="text/javascript">
var __login__ = false;
var login = function(){
	if (__login__ == true) {
		return;
	}
    var loginUrl = '<?=url('Login','doLogin');?>';
    var username = $.trim($('#username').val());
    if(!username){
    	tip('用户名不能为空', true);
        return;
    }
    var password = $.trim($('#password').val());
    if(!password){
    	tip('密码不能为空', true);
        return;
    }
    var verify = $.trim($('#verify').val());
    if(!verify){
        tip('验证码不能为空', true);
        return;
    }
    var postData = {
        'admin_name':username,
        'admin_pwd':password,
        'seccode':verify
    };
    __login__ = true;
    tip('');
    $('#login-btn').val('登陆中...');
    $.ajax({
        url : loginUrl,
        type : 'post',
        data : postData,
        dataType : 'json',
        error : function(){
        	__login__ = false;
        	tip('网络异常请重试', true);
        	$('#login-btn').val('立即登录');
            $("#verifyImg").click();
        },
        success : function(res){
        	__login__ = false;
            if(0 === Number(res.error)){
            	tip('通过验证，登陆中...', true);
                location.href = res.data;
            }else{
            	tip(res.msg, true);
                $("#verifyImg").click();
                $('#login-btn').val('立即登录');
            }
        }
    });
}
function tip (msg, enabled) {
	if (enabled === true) {
		$('#login-tip').show();
	} else {
		$('#login-tip').hide();
	}
	$('#login-tip').html(msg);
}
$(function(){
    //$("#login-btn").click(login);
})

</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>