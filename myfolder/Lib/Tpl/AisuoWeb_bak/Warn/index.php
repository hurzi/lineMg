
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
			$("#warn_date").val($("#selectYear").val()+'-'+$("#selectMonth").val()+'-'+$("#selectDay").val());
			var reg_phone = /^1(\d{10})$/;
			var reg_date = /^(\d{4}-\d{2}-\d{2})$/;
			var warn_date = $("#warn_date").val();			
			var warn_type_id = $("#warn_type_id").val();			
			var warnid = $("#warnid").val();			
			var url = '<?php echo url("Warn","ajax_submit");?>';
			if(!warn_date){
				tip('show_msg', '日期不能为空');
				return false;
			}
			if(!reg_date.test(warn_date)){
				tip('show_msg', '日期不符合要求');
				return false;
			}
			if(!warn_type_id){
				tip('show_msg', '提醒类型为空');
				return false;
			}
			
			var params = {
					warn_date : warn_date,
					warn_type_id : warn_type_id,
					warnid : warnid
				};
			submit_operate(url,params,'show_msg');
		});

		$(".addWarnType").click(function(){
			if($("#opDiv").css("display") == "none"){
				$("#warnid").val("");
				$("#opDiv").show();
			}else{
				$("#opDiv").hide();
			}
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
					var htmlStr = "";
					htmlStr += "<dt>"+result.data.typeName+"-提醒</dt>";
					htmlStr += "<dd>"+result.data.warnDate+"</dd>";
					$("#warnContentDiv").append(htmlStr);
					tip('show_msg', result.msg);
					$("#opDiv").hide();
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
<body class="write-body">
 <div class="alert-tips">
      <p>输入重要日期，您将会收获意想不到的惊喜！</p>
 </div>
 <div class="alert-form">
    <dl id="warnContentDiv">
    	<?php if($list){
    			foreach ($list as $v){
    		?>
        <dt><?php echo $v['type_name']?>-提醒</dt>
        <dd><?php echo $v['warn_date']?></dd>
        <?php }} ?>
    </dl>
     <div class="adddate">增加恋爱提醒<i class="add addWarnType" ></i></div>
     <div id="opDiv" style="display: none;">
     	 <input id="warnid" type="hidden" value=""/>
	     <input id="warn_date" type="hidden" value=""/>
	     <div>
	     	 <select name="warn_type_id" id="warn_type_id">
	     	 	 <?php foreach ($typeList as $v){ ?>
	             <option value="<?php echo $v['warn_type_id']?>"><?php echo $v['type_name']?></option>
	             <?php } ?>
	         </select>
	     </div>
	     <div class="select">
	         <select id="selectYear">
	             <option value="2014">2014 年</option>
	         </select>
	         <select name="" id="selectMonth">
	             <option value="07">7 月</option>
	         </select>
	         <select name="" id="selectDay">
	             <option value="21">21 日</option>
	         </select>
	         <div class="clear"></div>
	     </div>
	     <div class="wftips">最多添加两条爱锁提醒</div>
	     <div class="btn"><a href="javascript::void(0)" class="base-btn" id="sub_btn">保存</a></div>
     </div>
 </div>
 
 
 
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
