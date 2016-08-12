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
		$('#goonA').click(function(){
			$('#tipdiv').hide();
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var url = '<?php echo url("UnLock","ajax_submit");?>';
			var params = {
					test : 1
				};
			submit_operate(url,params,'show_msg');
		});

		
		$('#closeTipA').click(function(){
			$('#tipdiv').hide();
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
					tip('show_msg', result.msg);
					return true;
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
	 $("#maskcontent").html("");
	 $('#tipdiv').show();
}

</script>
</head>
<body class="apply-body">
 <div class="apply-tips">
     <h2>温馨提醒：48小时之内，您只能使用一次解锁功能!</h2>
     <p style="font-size: 12px">解锁后，即使再次与原锁定方锁定，其双方原日记内容将彻底删除，所以48小时内我们将会跟您再次进行[解锁]确认</p>
 </div>
<div class="unlockbg"></div>
<!-- 解锁弹层 -->
<div class="unlock-div"  style="height: 76px;">
    <div class="unlock-conform">
        <!-- <div class="unlock-title">您是否确认解锁？</div> -->
        <a class="unlock-btn" href="javascript::void(0)" id="goonA">确定解锁</a>
    </div>
</div>
<!-- 提示 -->
 <div id="tipdiv" class="mask" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle" id="show_msg">操作成功</div>
         <div class="maskcontent">

         </div>
         <div class="maskbtns"><a href="javascript::void(0)" class="alone" id="closeTipA">确定</a></div>
     </div>
 </div>
</body>
</html>
