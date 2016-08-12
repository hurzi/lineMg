<?php tpl('Common.header');?>
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
    	$("#getCode_btn").attr("value", "验证码已发送");
        //$("#getCode_btn").attr("value", wait + "秒重试");
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
					tip('show_msg', '验证码已发送成功，请查收短信');
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
<body>

  <div class="M_content zaiWidth">
        
		
        <div class="TXimg" style="background-image:url('images/phone_ico.png');"></div>

        <div class="zaiWidth_input">
	        <div class="mobile_an">
	
	           <div class="form_button">
	                <h2 style="padding-left: 5px">手机号码</h2>
	                <div class="fb_right" style="width: 60%;text-align: right;">
	                     <input class="phone_input" id="mobile" type="number" value=""  style="width: 100%;"/>
	                </div>
	           </div>
	
	        </div>
	
	       <div class="mobile_an">
	
	           <div class="form_button" style="float:left;width:30%;">
	                <input class="yz_input" id="code" type="text" value="" style="font-size: 14px;text-align: center;"/>
	           </div>
	           <input class="form_button yz_an WJ_Green" readonly="readonly"  value="获取验证码" id="getCode_btn"></input>
	
	        </div>
	
	       <div class="user_xy" ><div class="yz_text">该手机号码仅用于身份验证<br/>接收验证码不产生任何费用</div></div>
		</div>
		
       <div class="mobile_an">
         <div class="button WJ_Green" id="sub_btn" >确定</div>
       </div>

  </div>

</body>
</html>    
    
<?php tpl("Common.foot");?>
