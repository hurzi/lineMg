  <?php tpl('Common.header_xinchi');?>
   <script type="text/javascript">

var _loading = false;
$(document).ready(function(){
		//取消
		$("#cancel_btn").click(function(){
			var url = '<?php echo url('AsStatus','userinfo') ?>';	
			location.href = url;
		});
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
			var url = '<?php echo url('AsStatus','ajax_updateUserinfo') ?>';	
			if(!truthname){
				tip('show_msg', '姓名不能为空');
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
					//$("#ascode").html(result.data.ascode);
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
<div class="reg-forms">
 <ul style="padding-top: 0px;">
         <li>
             <strong>您的姓名</strong>
			 <div style="position: absolute;right: 20px;top: 2px;">
             <input type="text" style="border-style: none;text-align:right;font-size:16px" id="truthname" name="truthname" value="<?php echo $regObj['truthname'];?>"/>
			 </div>
         </li>
     </ul>
</div>
<div class="send-divnew">
    <div class="sendmsg-boxnew">
        <div class="send-btns">
            <input type="button" id="cancel_btn" class="sbtn" value="取消" /><input id="sub_btn" type="button" class="sbtn" value="保存">
        </div>
    </div>
</div>
 <?php tpl("Common.foot");?>
