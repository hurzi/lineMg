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

	
		//提交
		$('#submitA').click(function(){
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var mobile = $("#mobile").val();	
			if(!mobile){
				tip('show_msg', '手机号不能为空');
				return false;
			}
			if(!reg_phone.test(mobile)){
				tip('show_msg', '手机号输入不正确');
				return false;
			}
			$("#mobileP").html(mobile);
			$("#subDIV").show();
		});
		
		//提交
		$('#goonA').click(function(){
			$('#tipdiv').hide();
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var mobile = $("#mobile").val();
			var url = '<?php echo url("Lock","ajax_submit");?>';
			var params = {
					mobile : mobile
				};
			$("#subDIV").hide();
			submit_operate(url,params,'show_msg');
		});


		//提交
		$('#goon2A').click(function(){
			$('#tipdiv').hide();
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var mobile = $("#mobile").val();
			var url = '<?php echo url("Lock","ajax_submit");?>';
			var params = {
					mobile : mobile,
					iscontinue : 1
				};
			$("#subDIV").hide();
			submit_operate(url,params,'show_msg');
		});

		
		$('#closeTipA').click(function(){
			$('#tipdiv').hide();
		});
		$('#cancelA').click(function(){
			$('#subDIV').hide();
		});
		$('#cancelContinuedivA').click(function(){
			$('#continueDIV').hide();
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
	 			$("#mobileContinue").html(result.data.mobile);
		 		$('#tipdiv').hide();
				$('#continueDIV').show();
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
	 $("#maskcontent").html("");
	 $('#tipdiv').show();
}

</script>
</head>
<body class="apply-body">
 <div class="apply-tips">
     <h2 style="font-size: 14px">您已经注册爱锁</h2>
     <p style="font-size: 12px">现在您可以绑定您想绑定的他(她)。</p>
 </div>
<div class="apply">
    <h2  style="font-size: 14px">请输入恋人的手机号</h2>
    <div class="hermobile"><input type="tel" id="mobile" value="" placeholder="请输入恋人的手机号"></div>
    <dl><dt>温馨提醒：</dt><dd>48小时内您只能绑定一次，<br>请您仔细填写！</dd></dl>
</div>
<div class="btn"><a href="javascript::void(0)" class="base-btn" id="submitA">确认锁定</a></div>
 
  <!-- 弹层 -->
 <div class="mask" id="subDIV" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="maskcontent">
             <div class="apply-conform">
                 <h2>您将锁定的手机号码是</h2>
                <p id="mobileP"></p>
             </div>
         </div>
         <div class="maskbtns"><a href="javascript::void(0)" id="cancelA">取消</a><a href="javascript::void(0)" id="goonA">好</a></div>
     </div>
 </div>
 
  <!-- 提示 -->
 <div id="tipdiv" class="mask" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle" id="show_msg">手机号码错误</div>
         <div class="maskcontent">

         </div>
         <div class="maskbtns"><a href="javascript::void(0)" class="alone" id="closeTipA">确定</a></div>
     </div>
 </div>
 
   <!-- 弹层 -->
 <div class="mask" id="continueDIV" style="display: none;">
      <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle">您填写的手机号</div>
         <div class="maskcontent">
             <div class="apply-conform">
                 <p id="mobileContinue"></p>
                 <h2>不是爱锁会员，去召唤他(她)吧</h2>
             </div>
         </div>
         <div class="maskbtns"><a href="javascript::void(0)"  id="cancelContinuedivA">取消</a><a href="javascript::void(0)" id="goon2A">继续申请</a></div>
     </div>
 </div>
 
</body>
</html>
