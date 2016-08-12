  <?php tpl('Common.header');?>
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
<body>

  <div class="M_content zaiWidth">
        

        <div class="TXimg" style="background-image:url('<?php echo $headimgurl?$headimgurl:'images/touxiang.png';?>');"></div>

        <div class="mobile_an">

           <div class="form_button">
                <h2 style="padding-left: 5px">你的姓名</h2>
                <div class="fb_right" style="padding-top: 10px;padding-right:15px;font-size: 16px;">
                	 <?php echo $truthname;?>                     
                </div>
           </div>

        </div>

       <div class="mobile_an">

           <div class="form_button">
                <h2 style="padding-left: 5px">你的性别</h2>
                <div class="fb_right"  style="padding-top: 10px;padding-right:15px;font-size: 16px;">
                	 <label><?php echo $sex==2?"女":"男";?></label>
                     
                </div>
           </div>

        </div>

        <div class="mobile_an">

           <div class="form_button">
                <h2 style="padding-left: 5px">手机号码</h2>
                <div class="fb_right" style="padding-top: 10px;padding-right:15px;font-size: 16px;">
                	<label><?php echo $mobile;?></label>
                                          
                </div>
           </div>

        </div>
	<input type="hidden" id="truthname" value="<?php echo $truthname;?>"/>
     <input type="hidden" id="sex" value="<?php echo $sex;?>"/>
     <input type="hidden" id="mobile" value="<?php echo $mobile;?>"/>
     
       <p class="user_xy" ><a href="<?php echo url('index','agreement')?>">阅读爱锁用户协议</a></p>

       <div class="mobile_an">
         <div class="button WJ_Green" id="sub_btn">同意协议并提交</div>
       </div>

  </div>
	<!-- 弹层 -->
 <div id="succDiv" class="mask" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle">注册成功</div>
         <div class="maskcontent">
            <div class="apply-conform">
                 <!-- <p>您的爱锁邮编：<font id="ascode"></font></p> -->
            </div>
        <div class="maskbtns"><a href="<?php echo url("Lock","index");?>">申请锁定</a><a href="<?php echo url("Index","introduce")?>">功能详解</a></div>
	     </div>
	 </div>
 </div>

 
 <?php tpl("Common.foot");?>
        
   