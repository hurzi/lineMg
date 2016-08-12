 <?php tpl('Common.header_xinchi');?>
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
			var is_private = $('input[name="is_private"]:checked').val();
			var url = '<?php echo url("Pubmsg","ajax_addPubmsg");?>';
			if(!content){
				tip('show_msg', '内容不能为空');
				return false;
			}
			if(is_private == null || is_private==undefined || is_private == ""){
				is_private = 0;
			}
			var params = {
					content : content,
					is_private :is_private
				};
			submit_operate(url,params,'show_msg');
		});

		
		$('#closeTipA').click(function(){			
			$('#tipdiv').hide();
			$("#content").val("");
			var url = '<?php echo url("Pubmsg","index");?>';
			location.href = url;
		});	
			
});


//提交操作
function submit_operate(url, params, tipid) {
	_loading = true;
	//tip('show_msg', '正在处理请求,请稍后...');
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
	 $('#tipdiv').show();
}

</script>
    
</head>
<body>
<div class="tab"><p><a href="<?php echo url("Pubmsg","index");?>"  class="current">公共信池</a><a href="<?php echo url("Pubmsg","myStore");?>">我的收藏</a></p></div>   
<!-- 写信 -->
<div class="sendmsg-box">
    <textarea placeholder="说几句吧..." id="content" style="word-wrap:break-word; word-break:break-all;"></textarea>
    <div class="send-btns">
        <label><input type="radio" name="is_private" value="0" checked="checked"/>公开</label><label><input type="radio" value="1"  name="is_private" />匿名</label>
        <!-- <input type="button" class="sbtn" value="取消" /> -->
        <input type="button" class="sbtn" id="sub_btn" value="发布">
    </div>
</div>
 <?php tpl("Common.foot_co");?>
