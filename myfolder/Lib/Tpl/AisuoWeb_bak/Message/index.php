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
		$('#sub_btn').click(function(){
			$('#tipdiv').hide();
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var content = $("#content").val();			
			var url = '<?php echo url("Message","ajax_submit");?>';
			if(!content){
				tip('show_msg', '内容不能为空');
				return false;
			}
			
			var params = {
					content : content
				};
			submit_operate(url,params,'show_msg');
		});

		
		$('#closeTipA').click(function(){			
			$('#tipdiv').hide();
			$("#content").val("");
		});	
			
});


//提交操作
function submit_operate(url, params, tipid) {
	_loading = true;
	//tip('show_msg', '正在处理请求,请稍后...');
	$(".present").show();
	setTimeout(function(){
		$.post(url, params, function (result) {
			_loading = false;
			$(".present").hide();
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
	},2000);
	
	
}
function tip (tipid, mesg) {
	 $('#'+tipid).html(mesg);
	 $("#maskcontent").html("");
	 $('#tipdiv').show();
}

</script>
</head>
<body class="write-body">
<div class="tab"><p><a href="<?php echo url("Message","index")?>" class="current">寄信</a><a href="<?php echo url("Message","inbox");?>">收信</a></p></div>    
<div class="write-message">
    <div class="write-cot">
        <ul>
            <li><span class="fl" id="datespan"><?php echo $currDate;?></span><span class="fr">您的邮编ID: <strong><?php echo $ascode;?></strong></span></li>
            <li><textarea placeholder="说两句吧..." id="content"></textarea><label>私密投递(<?php echo $selfObj['truthname']."-".$ascode?>)</label>
            <a href="javascript::void(0)" id="sub_btn" class="base-btn">投递</a><p class="al-r"><font id="wordcount"></font></p></li>
        </ul>
    </div>
    <p class="intro">说明：<br>您发布的私密日志不可见，解锁后对方可见。</p>
</div>
<!-- 发送中弹层 -->
<div class="present" style="display: none;"></div>

 <!-- 弹层 -->
 <div id="tipdiv" class="mask" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle" id="show_msg">操作成功</div>
         <div class="maskcontent">

         </div>
         <div class="maskbtns"><a href="javascript::void(0)" class="alone" id="closeTipA">确认</a></div>
     </div>
 </div>
 

</body>
</html>
