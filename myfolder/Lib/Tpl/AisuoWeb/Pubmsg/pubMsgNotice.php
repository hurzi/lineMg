<?php tpl('Common.header_xinchi');?>

 <script type="text/javascript">
var _loading = false;
var pageindex = 1;
var hasnext = 1;
$(document).ready(function(){

	
		//提交
		//$('#show_msg_div').click(function(){
		//	submit_operate();
		//});

		
		$('#closeTipA').click(function(){
			$('#tipdiv').hide();
		});	

		//初始加载一次数据
		submit_operate();
			
});

//提交操作
function submit_operate() {
	var url = '<?php echo url("Pubmsg","ajax_noticList");?>';
	_loading = true;
	tipList('show_msg_div', '正在加载数据,请稍后...');
	if(!hasnext){
		tipList('show_msg_div', '没有可加载的数据了!');
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
	 			tipList('show_msg_div', '');
	 		}
	 		var htmlstr = "";
			for(var i=0; i<result.data.list.length; i++)  
			{  

				htmlstr += '<li>';
				htmlstr += '    <img src="'+result.data.list[i].headimgurl+'" width="40" height="40" alt="">';
				htmlstr += '    <a href="<?php echo url("Pubmsg","onePubmsg")?>&pid='+result.data.list[i].as_pubmsg_id+'"><div class="new-msg-cot">';
				if(result.data.list[i].as_content.length>10){
					htmlstr += '        <div class="msg-content-txt">'+result.data.list[i].as_content.substr(0,10)+'...</div>';
				}else{
					htmlstr += '        <div class="msg-content-txt" >'+result.data.list[i].as_content+'</div>';
				}
				htmlstr += '        <div class="msg-detail">';
				htmlstr += '            <h2>'+result.data.list[i].truthname+'</h2>';
				if(result.data.list[i].notice_type == 1){
					htmlstr += '        <p><i class="zan-icon"></i></p>';
				}else{
					htmlstr += '            <p>'+result.data.list[i].content+'</p>';
				}
				htmlstr += '            <div class="msg-tool">';
				htmlstr += '                <span>'+result.data.list[i].create_time+'</span>';
				htmlstr += '            </div>';
				htmlstr += '        </div>';
				htmlstr += '    </div></a>';
				htmlstr += '</li>';
			   
			}  
			$("#listul").append(htmlstr);
			/*
			if(hasnext == 1){
				tipList('show_msg_div', '加载更多...');
			}else{
				tipList('show_msg_div', '');
			}
			*/
	 	}catch(e){
	 		tipList('show_msg_div', '系统异常,请稍后提供...');
	 		alert(result.msg);
			//window.location.reload();
	 	}
	});
}
function tipList (tipid, mesg) {
	 $('#'+tipid).html(mesg);
	 $('#'+tipid).show();
}

function tip (tipid, mesg) {
	 $('#'+tipid).html(mesg);
	 $("#maskcontent").html("");
	 $('#tipdiv').show();
}

</script>
 
 <script type="text/javascript">

//滚动条在Y轴上的滚动距离  
 function getScrollTop(){
	var scrollTop = 0, bodyScrollTop = 0, documentScrollTop = 0;
	if(document.body){
		bodyScrollTop = document.body.scrollTop;
	}
　　if(document.documentElement){
　　　　documentScrollTop = document.documentElement.scrollTop;
　　}
　　scrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
　　return scrollTop;
} 
 //文档的总高度 
 function getScrollHeight(){
　　var scrollHeight = 0, bodyScrollHeight = 0, documentScrollHeight = 0;
　　if(document.body){
　　　　bodyScrollHeight = document.body.scrollHeight;
　　}
　　if(document.documentElement){
　　　　documentScrollHeight = document.documentElement.scrollHeight;
　　}
　　scrollHeight = (bodyScrollHeight - documentScrollHeight > 0) ? bodyScrollHeight : documentScrollHeight;
　　return scrollHeight;
} 
 //浏览器视口的高度 
 function getWindowHeight(){
　　var windowHeight = 0;
　　if(document.compatMode == "CSS1Compat"){
　　　　windowHeight = document.documentElement.clientHeight;
　　}else{
　　　　windowHeight = document.body.clientHeight;
　　}
　　return windowHeight;
} 
 window.onscroll = function(){
　　if(getScrollTop() + getWindowHeight() == getScrollHeight()){
		if(hasnext == 1){
　　　　	submit_operate();
　　　　}
　　}
};
 
</script>
</head>
<body>
<div class="tab"><p><a href="<?php echo url("Pubmsg","index");?>" class="current">公共信池</a><a href="<?php echo url("Pubmsg","myStore");?>" >我的收藏</a></p></div>   
<div class="msglist">
    <ul class="msg-content"  id="listul"  style="margin-top: 10px">
        
    </ul>
</div>
<a href="<?php echo url("Pubmsg","index");?>"><img src="images/fh.png" width="45"  height="45" style="position:fixed;top:50%;left:9px;z-index:9999"></a>
 <?php tpl("Common.foot_co");?>
