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
var pageindex = 1;
var hasnext = 1;
$(document).ready(function(){

	
		//提交
		$('#show_msg').click(function(){
			submit_operate();
		});

		
		$('#closeTipA').click(function(){
			$('#tipdiv').hide();
		});	

		//初始加载一次数据
		submit_operate();
			
});

//提交操作
function submit_operate() {
	var url = '<?php echo url("Message","ajax_inboxList");?>';
	_loading = true;
	tip('show_msg', '正在加载数据,请稍后...');
	if(!hasnext){
		tip('show_msg', '没有可加载的数据了!');
		return;
	}
	var params = {
			pageindex : pageindex
		};
	$.post(url, params, function (result) {
		_loading = false;
		try{
			result = eval("(" + result + ")");
			if (result.data.hasNext == 1) {
				pageindex = pageindex+1;
				hasnext = 1;
	 		}else{
	 			hasnext = 0;
	 		}
	 		var htmlstr = "";
			for(var i=0; i<result.data.list.length; i++)  
			{  
			    htmlstr += "<li>";
			    htmlstr += '<img src="'+result.data.list[i].headimgurl+'" width="40" height="40" alt="">';
			    htmlstr += '<div class="msg-detail">';
			    htmlstr += '       <h2>'+result.data.list[i].truthname+'</h2>';
			    htmlstr += '      <p>'+result.data.list[i].content+'</p>';
			    htmlstr += '       <div class="msg-tool">'+result.data.list[i].create_time+'<span></span><!--<i class="write-msg"></i>--></div>';
			    htmlstr += '   </div>';
			   	htmlstr += '</li> ';
			}  
			$("#listul").append(htmlstr);
			tip('show_msg', '加载更多...');
	 	}catch(e){
	 		tip('show_msg', '系统异常,请稍后提供...');
			//window.location.reload();
	 	}
	});
}
function tip (tipid, mesg) {
	 $('#'+tipid).html(mesg);
	 $('#'+tipid).show();
}

</script>
</head>
<body>
<div class="tab"><p><a href="<?php echo url("Message","index")?>" >寄信</a><a href="<?php echo url("Message","inbox");?>" class="current">收信</a></p></div>    
<div class="msglist">
    <h2 class="msg-total">共有<?php echo $inboxCount;?>封信件</h2>
    <ul id="listul">
        
    </ul> 
    <div class="loading-more" id="show_msg">加载更多...</div>   
</div>

</body>
</html>
