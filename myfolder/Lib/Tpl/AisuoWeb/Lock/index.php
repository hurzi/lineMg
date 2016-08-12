  <?php tpl('Common.header');?>
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
	 $("#tipdiv").css("top",$(document).scrollTop());
	 $('#tipdiv').show();
}

</script>
    
 
</head>
<body>

  <div class="M_content zaiWidth" >
        

        <div class="TXimg" style="margin-bottom:5px;background-image:url('<?php echo $selfObj['headimgurl']?$selfObj['headimgurl']:"images/touxiang.png";?>');"></div>

        <h3 class="user_xy color_t_Green" style="margin-bottom: 30px"><?php echo $selfObj['truthname'];?></h3>
        
        <div class="Ta_c">请输入对方手机号</div>
        <div class="mobile_an lrm20 zaiWidth_input">

           <div class="form_button duliInput">
                <div class="fb_right">
                     <input type="number" id="mobile"  value="" class="phone_input">
                    
                </div>
           </div>

        </div>

       <p class="user_xy" >温馨提示：48小时内只能申请一次</p>

       <div class="button WJ_Green" id="submitA">提交</div>

  </div>   
    <!-- 弹层 -->
 <div class="mask" id="subDIV" style="display: none;">
     <div class="maskbg"></div>
     <div class="maskdiv">
     	<div class="masktitle">您将锁定的手机号码是
     	<p id="mobileP"></p>
     	</div>
         <div class="maskbtns"><a href="javascript::void(0)" id="cancelA">取消</a><a href="javascript::void(0)" id="goonA">好</a></div>
     </div>
 </div>
 
 <!-- 弹层 -->
 <div class="mask" id="continueDIV" style="display: none;">
      <div class="maskbg"></div>
     <div class="maskdiv">
         <div class="masktitle" style="padding-bottom: 5px">您填写的手机号</div>
         <div class="maskcontent">
             <div class="apply-conform">
                 <p id="mobileContinue" style="font-size: 16px;text-align: center;font-weight: bold;" ></p>
                 <h2 style="font-size: 14px;text-align: center;" >不是爱锁会员，去召唤他(她)吧</h2>
             </div>
         </div>
         <div class="maskbtns"><a href="javascript::void(0)"  id="cancelContinuedivA">取消</a><a href="javascript::void(0)" id="goon2A">继续申请</a></div>
     </div>
 </div>
     
 <?php tpl("Common.foot");?>
    
    
    
    