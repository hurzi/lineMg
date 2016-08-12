  <?php tpl('Common.header');?>
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

		$('#closeSubSucc').click(function(){			
			$('#sub_succ_div').hide();
			$("#content").val("");
		});	
			
});


//提交操作
function submit_operate(url, params, tipid) {
	_loading = true;
	//tip('show_msg', '正在处理请求,请稍后...');
	//$(".present").show();
	//setTimeout(function(){
		$.post(url, params, function (result) {
			_loading = false;
	//		$(".present").hide();
			try{
				result = eval("(" + result + ")");
				if (result.error == 0) {
					if(result.data.jumpUrl){
						location.href = result.data.jumpUrl;
						return;
					}else {			 
						//tip('show_msg', result.msg);
						$('#sub_succ_div').show();
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
	//},3000);
	
	
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
  
  <header class="header">           
     <ul class="header_nav">
      <li class="nav_active"><a href="<?php echo url("Message","index")?>">寄信</a></li>
      <li><a href="<?php echo url("Message","inbox");?>">收信</a></li>
     </ul>
  </header>
   <div class="newsCon">
        <h2><font color="#06be04"><?php echo $otherObj['truthname']?></font>（邮政编码：<?php echo $otherObj['as_code']?>）</h2>
        <textarea placeholder="此为私密投递信件" id="content" style="font-size:14px;height: 200px"></textarea>
        <div style="text-align: right;">
           <div class="funbutton WJ_Green fun_mini_but" id="sub_btn" ><span style="font-size:14px">寄信</span></div>
        </div>
   </div>    
   <div class="mobile_an" style="margin-top: 0px">
   <p class="user_xy" style="text-align:center; color:#b1b1b2;" >这是一封私密信件，投递成功后将被系统收录封存<br/>直至双方解锁后，对方才可以查阅此信件。</p> 
   </div>


  <div class="mask" id="sub_succ_div" style="display: none;">
     <div class="maskbg"></div>
	<div class="maskdiv">
         <div class="masktitle"><img src="images/GIF20140807-GG.gif" height="148" width="175" ></div>
         <div class="maskcontent"></div>
         <div class="maskbtns" style="text-align:center" ><a style="width:100%;" href="javascript::void(0)" id="closeSubSucc">私信已投递</a></div>
     </div>
  </div>


  <div class="mask present"  style="display: none;">
     <div class="maskbg"></div>

     <div class="maskdiv dhCon">
          <div class="Nomail_Animation"></div>
     </div>   

  </div>
  
 <?php tpl("Common.foot_co");?>
    
    
