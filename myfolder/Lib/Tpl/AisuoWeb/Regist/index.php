
    <?php tpl('Common.header');?>
    <script type="text/javascript">

var _loading = false;
$(document).ready(function(){
		$("#text_row_select").change(function(){
	      $(this).prev().text($(this).val()==2?"女":"男");
	    });

		//绑定
		$("#sub_btn").click(function (){
			if (_loading) {
				return false;
			}
			var reg_phone = /^1(\d{10})$/;
			var reg_code = /^\d{6}$/;
			var open_id = '<?php echo @$openid;	?>';
			var truthname = $("#truthname").val();			
			//var sex = $('input[name="sex"]:checked').val();
			var sex = $("#text_row_select").val();
			var province = $("#province").val();			
			var city = $("#city").val();			
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
					mobile : mobile,
					province : province,
					city : city
				};
			submit_operate(url,params,'show_msg');
		});

		$('#closeTipA').click(function(){
			$('#tipdiv').hide();
		});

		//跳转第二页
		$('#linkStep2').click(function(){
			var truthname = $("#truthname").val();			
			//var sex = $('input[name="sex"]:checked').val();
			var sex = $("#text_row_select").val();
			var headimgurl = $("#headimgurl").val();
			var url = '<?php echo url('Regist','ajax_step1_data') ?>';	
			var params = {
					truthname : truthname,
					sex : sex,
					headimgurl : headimgurl
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
	 $("#tipdiv").css("top",$(document).scrollTop());	 
	 $('#tipdiv').show();
}
</script>
    
</head>
<body>

  <div class="M_content zaiWidth">
  		<div class="zaiWidth_input">
  			
	        <input type="hidden" id="province" value="<?php echo $province;?>"/>
			<input type="hidden" id="city" value="<?php echo $city;?>"/>
			<input type="hidden" id="headimgurl" value="<?php echo $wxObjImg?$wxObjImg:"images/aslogo.jpg";?>"/>
			<div class="TXimg" style="background-image:url('<?php echo $wxObjImg?$wxObjImg:"images/aslogo.jpg";?>');"></div>
	
	        <div class="mobile_an">	
	           <div class="form_button">
	                <h2>你的姓名</h2>
	                <div class="fb_right">
	                     <input class="text_input"  style="border-style: none;" id="truthname" type="text" value="<?php echo @$truthname;?>" />
	                </div>
	           </div>
	
	        </div>
	
	       <div class="mobile_an">
	
	           <div class="form_button">
	                <h2>你的性别</h2>
	                <div class="fb_right">
	                	<div class="fb_select">
		                  <div class="select_ico"></div>
		                  <span><?php echo $sex==2?'女':'男';?></span>
		                  <select id="text_row_select" name="sex">
		                    <option value="1" <?php echo ($sex != 2)? 'selected="selected"':'';?>>男</option>
		                    <option value="2" <?php echo ($sex && $sex == 2)? 'selected="selected"':'';?>>女</option>
		                  </select>
		                </div>
	                    <!-- <label><input type="radio"  value ="1" name="sex" <?php echo ($sex != 2)? 'checked="checked"':'';?> /><font size="3">男</font></label><label><input type="radio" value="2" name="sex" <?php echo ($sex && $sex == 2)? 'checked="checked"':'';?> /><font size="3">女</font></label>
	                     --> 
	                </div>
	           </div>
	
	        </div>
	
	        <div class="mobile_an">
	
	           <div class="form_button">
	                <h2>手机号码</h2>
	                <div class="fb_right">
	                     <input type="hidden" id="mobile" value="<?php echo $mobile;?>"/>
	                     <a href="javascript::void(0)" id="linkStep2" class="frbox">
	                         <div class="right_Point"></div>
	                         <div class="phone_text" style="color: #06be04">未输入</div>
	                     </a>
	                </div>
	           </div>
	
	        </div>
		</div>
       <p class="user_xy" ><a href="<?php echo url('index','agreement')?>">阅读爱锁用户协议</a></p>

       <div class="mobile_an">
         <div class="button WJ_Green" id="sub_btn">同意协议并提交</div>
       </div>

  </div>
	

 
 <?php tpl("Common.foot");?>
    
    
    
    