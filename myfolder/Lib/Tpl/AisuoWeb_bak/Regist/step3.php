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
		//绑定
		$("#sub_btn").click(function (){
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var reg_code = /^\d{6}$/;
			var truthname = $("#truthname").val();			
			var sex = $('#sex').val();
			var mobile = $("#mobile").val();	
			var url = '<?php echo url('Regist','ajax_step3submit') ?>';	
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
					$('#tipdiv').hide();		
					$("#ascode").html(result.data.ascode);
					$('#succDiv').show();
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
     <h2>您好，请注册爱锁，享受更多功能服务！</h2>
     <p>温馨提醒：以下您填写的信息注册后将不可更改，请您仔细填写</p>
 </div>
 <div class="reg-forms">
     <ul>
         <li>
             <strong>您的姓名</strong>
             <p class="value"><a href="#"><span style="font-size:1.7rem;height:25px;width: 100%;text-align: right;"><?php echo $truthname;?></span><i class="link"></i></a></p>
         </li>
         <li>
             <strong>您的性别</strong>
             <p class="value"><a href="#"><span style="font-size:1.7rem;"><?php echo ($sex && $sex == 1)? '男':'女';?></span><i class="link"></i></a></p>
         </li>
         <li>
             <strong>您的手机<em>已认证</em></strong>
             <p class="value"><a href="#"><span style="font-size:1.7rem;height:25px"><?php echo $mobile;?></span><i class="link"></i></a></p>
         </li>
     </ul>
     <input type="hidden" id="truthname" value="<?php echo $truthname;?>"/>
     <input type="hidden" id="sex" value="<?php echo $sex;?>"/>
     <input type="hidden" id="mobile" value="<?php echo $mobile;?>"/>
     <div class="btn"><a href="javascript::void(0)" class="base-btn" id="sub_btn">提交</a></div>
 </div>
 <!-- 弹层 -->
 <div id="succDiv" class="mask" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle">注册成功</div>
         <div class="maskcontent">
            <div class="apply-conform">
                 <p>您的爱锁邮编：<font id="ascode"></font></p>
            </div>
        <div class="maskbtns"><a href="<?php echo url("Lock","index");?>">情锁他(她)心</a><a href="<?php echo url("Index","introduce")?>">爱锁详解</a></div>
	     </div>
	 </div>
 </div>
 
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



</body>
</html>