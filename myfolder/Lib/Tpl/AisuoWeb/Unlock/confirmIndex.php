 <?php tpl('Common.header');?>
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
			var url = '<?php echo url("UnLock","ajax_submit_confirm");?>';
			var params = {
					test : 1
				};
			submit_operate(url,params,'show_msg');
		});

		//提交
		$('#cancelConfirmA').click(function(){
			$('#tipdiv').hide();
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var url = '<?php echo url("UnLock","ajax_submit_cancel");?>';
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
<body>

  <div class="M_content" style="padding-top:60px;">
        

        <div class="Ta_c bm10"></div>

        <div class="BigClock"></div>

        <h3 class="user_xy color_t_Green">解锁时间已到</h3>
        
        <p class="user_xy" >解锁后，再次成功锁定的一方，原私信收信箱数据全部清空，请谨慎操作！</p>

        <div class="button WJ_Green" id="goonA">确定解锁</div>

        <div class="button WJ_Green" id="cancelConfirmA">暂不解锁</div>

  </div>

<?php tpl("Common.foot");?>
    
    
