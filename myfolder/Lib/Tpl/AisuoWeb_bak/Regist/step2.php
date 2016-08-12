<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>爱锁</title>
    <meta content="爱锁" name="keywords" />
    <meta content="爱锁" name="description" />
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript">

var _loading = false;
$(document).ready(function(){
		//get validate code
		$("#getCode_btn").click(function (){
			if (_loading) {
				return false;
			}
			if($("#getCode_btn").attr("disabled")){
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var mobile = $("#mobile").val();			
			var url = '<?php echo url('Regist','ajax_step2_sendcode') ?>';	
			if(!mobile){
				tip('show_msg', '手机号码不能为空');
				return false;
			}
			if(!reg_phone.test(mobile)){
				tip('show_msg', '手机号码格式有误');
				return false;
			}
			
			var params = {
					mobile : mobile
				};
			timebtn();
			submit_operate(url,params,'show_msg');
		});

		//验证
		$("#sub_btn").click(function (){
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var reg_code = /^\d{6}$/;
			var mobile = $("#mobile").val();			
			var code = $("#code").val();
			var url = '<?php echo url('Regist','ajax_step2_check') ?>';	
			if(!mobile){
				tip('show_msg', '手机号码不能为空');
				return false;
			}
			if(!reg_phone.test(mobile)){
				tip('show_msg', '手机号码格式有误');
				return false;
			}
			if(!code){
				tip('show_msg', '验证不能为空');
				return false;
			}
			if(!reg_code.test(code)){
				tip('show_msg', '验证码错误');
				return false;
			}
			var params = {
					code : code,
					mobile : mobile
				};
			submit_operate(url,params,'show_msg');
		});

		$('#closeTipA').click(function(){
			$('#tipdiv').hide();
		});

		//跳转第二页
		$('#linkStep2').click(function(){
			var truthname = $("#truthname").val();			
			var sex = $('input[name="sex"]:checked').val();
			var url = '<?php echo url('Regist','ajax_step1_data',null,'AisuoWeb') ?>';	
			var params = {
					truthname : truthname,
					sex : sex
				};
			submit_operate(url,params,'show_msg');
		});
		
});

var wait = 60;
function timebtn() {
    if (wait == 0) {
    	$("#getCode_btn").attr("disabled",false);
    	$("#getCode_btn").attr("value","获取验证码");
    	$('#tipdiv').hide();
        wait = 60;
    } else {
    	$("#getCode_btn").attr("disabled", true);
    	$("#getCode_btn").attr("value", wait + "秒未收到，点击重发");
        wait--;
        setTimeout(function () {
        	timebtn();
        },
        1000)
    }
}

//提交操作
function submit_operate(url, params, tipid) {
	_loading = true;
	tip('show_msg', '正在处理请求,请稍后...');
	$.post(url, params, function (result) {
		_loading = false;
		try{
	 		result = eval("(" + result + ")");
			if (result.error == 0) {
				if(result.data.jumpUrl){
					location.href = result.data.jumpUrl;
					return;
				}else if(result.data == 2){
					$("#code_div").show();
					tip('show_msg', '发送验证码成功，请查收短信');
				}else {			 		
					tip('show_msg', '操作成功');
		 		}
	 		}
			else{
	 			tip('show_msg', result.msg);
	 			return false;
	 	 	}
	 	}catch(e){
			alert(result);
	 		tip('show_msg', '系统异常,请稍后提供...');
			//window.location.reload();
	 	}
	});
}
function tip (tipid, mesg) {
	 $('#'+tipid).html(mesg);
	 $('#tipdiv').show();
}
</script>
</head>
<body class="reg-body">
<div class="reginput"><input type="text" class="text" id="mobile"><p class="mobile-tips">请输入真实的手机号码，方便对方确认身份</p></div>
<div class="btn"><a href="javascript::void(0)" class="base-btn" id="getCode_btn">获取验证码</a></div>
<div class="reginput" id="code_div" style="display: none;"><input type="text" class="text" id="code"><p class="mobile-tips">请输入收到的验证码</p></div>
<div class="btn read-btn"><a href="javascript::void(0)" id="sub_btn" class="base-btn">同意爱锁用户协议并确定</a></div>
<div class="read"><a href="#">阅读爱锁用户协议</a></div>

  <!-- 弹层 -->
 <div id="tipdiv" class="mask" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle" id="show_msg">手机号码错误</div>
         <div class="maskcontent">

         </div>
         <div class="maskbtns"><a href="javascript::void(0)" class="alone" id="closeTipA">确定</a></div>
     </div>
 </div>
 
</body>
</html>
