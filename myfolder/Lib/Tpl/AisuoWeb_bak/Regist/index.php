<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>爱锁</title>
    <meta content="爱锁" name="keywords" />
    <meta content="爱锁" name="description" />
    <link href="css/style.css?version=<?php AbcConfig::VERSION?>" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript">

var _loading = false;
$(document).ready(function(){
		

		//绑定
		$("#sub_btn").click(function (){
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var reg_code = /^\d{6}$/;
			var open_id = '<?php echo @$openid;	?>';
			var truthname = $("#truthname").val();			
			var sex = $('input[name="sex"]:checked').val();
			var mobile = $("#mobile").val();	
			var url = '<?php echo url('Regist','ajax_step1submit',null,'AisuoWeb') ?>';	
			if(!truthname){
				tip('show_msg', '姓名不能为空');
				return false;
			}
			if(!sex){
				tip('show_msg', '性别不能为空');
				return false;
			}
			if(!mobile){
				tip('show_msg', '手机号不能为空');
				return false;
			}
			var params = {
					truthname : truthname,
					sex : sex,
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
			var url = '<?php echo url('Regist','ajax_step1_data') ?>';	
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
        wait = 60;
    } else {
    	$("#getCode_btn").attr("disabled", true);
    	$("#getCode_btn").attr("value", wait + "秒重新获取");
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
				}else {			 		
					tip('show_msg', '操作成功');
		 		}
	 		}else if(result.error == 2){
				tip('show_msg',result.msg);
				return false;
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
 <div class="tips">
     <h2>爱情需严肃，填写要谨慎</h2>
 </div>
 <div class="reg-forms">
     <ul>
         <li>
             <strong>您的姓名</strong>
             <p class="value"><input type="text" id="truthname" style="font-size:1.7rem;height:25px;width: 100%;text-align: right;" class="inputtxt" value="<?php echo $truthname;?>"></p>
         </li>
         <li>
             <strong>您的性别</strong>
             <p class="value"><label><input type="radio"  value ="1" name="sex" <?php echo ($sex != 2)? 'checked="checked"':'';?> /><font size="3">男</font></label><label><input type="radio" value="2" name="sex" <?php echo ($sex && $sex == 2)? 'checked="checked"':'';?> /><font size="3">女</font></label></p>
         </li>
         <li>
             <strong>您的手机</strong>
             <p class="value"><input type="hidden" id="mobile" value="<?php echo $mobile;?>"/><a href="javascript::void(0)" id="linkStep2"><span style="font-size:1.2rem;">未输入</span><i class="link"></i></a></p>
         </li>
     </ul>
     <div class="btn"><a href="javascript::void(0)" class="base-btn" id="sub_btn">提交</a></div>
 </div>
 
 <!-- 弹层 -->
 <div id="tipdiv" class="mask" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle" id="show_msg">手机号码错误</div>
         <div class="maskcontent">

         </div>
         <div class="maskbtns"><a href="javascript::void(0)" class="alone" id="closeTipA">重新填写</a></div>
     </div>
 </div>
</body>
</html>