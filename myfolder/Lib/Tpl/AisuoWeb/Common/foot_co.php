 <!-- 弹层 -->
 <div class="mask" id="tipdiv" style="display: none;">
      <div class="maskbg"></div>
      <div class="maskdiv">
         <div class="masktitle"  id="show_msg">手机号码错误</div>
         <div class="maskcontent"></div>
         <div class="maskbtns"><a style="width:100%;" href="javascript:void(0)" id="closeTipA">确定</a></div>
     </div> 
  </div>
  
   <!-- 确认弹层 -->
 <div class="mask" id="tipConfirmDiv" style="display: none;">
      <div class="maskbg"></div>
      <div class="maskdiv">
         <div class="masktitle"  id="show_msg_confirm">手机号码错误</div>
         <div class="maskcontent"></div>
         <div class="maskbtns">
         <a style="width:49%" href="javascript:$('#tipConfirmDiv').hide();" id="closeConfirmTipA">取消</a>
         <a style="width:49%"   href="javascript:void();" id="confirmTipA">确认</a>
         </div>
     </div> 
  </div>
  
  <div class="Footer">
       <div class="aiso_f_logo"></div>
       <p class="Ta_c tm5" style="font-size:10px;">北京爱锁国际文化有限公司</p>
       <p class="Ta_c tm5" style="font-size:8px;"><span style="font-weight: bold;">I</span>ONLY <span style="font-weight: bold;">I</span>NTERNATIONAL <span style="font-weight: bold;">C</span>ULTURE CO., LTD</p>
  </div>
  <script type="text/javascript">
  function confirmTop(msg,callbackfn){
	  	$(".mask").hide();
		$("#tipConfirmDiv").css("top",$(document).scrollTop());
	  	$("#tipConfirmDiv").show();
	  	$("#show_msg_confirm").html(msg);
	  
	  	$("#confirmTipA").click(function(){
			$('#tipConfirmDiv').hide();
			if(typeof callbackfn == 'function'){				
				callbackfn();
		  	}
		});
   }
		
</script>
  <script type="text/javascript" src="js/zf.js"></script>
  
</body>
</html>