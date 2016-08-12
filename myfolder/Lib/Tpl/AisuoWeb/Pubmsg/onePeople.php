<?php tpl('Common.header_xinchi');?>

 <script type="text/javascript">
var _loading = false;
var pageindex = 1;
var hasnext = 1;
$(document).ready(function(){

		//初始加载一次数据
		submit_operate();
		
		//提交
		//$('#show_msg_div').click(function(){
		//	submit_operate();
		//});

		
		$('#closeTipA').click(function(){
			$('#tipdiv').hide();
		});	

		// 删除
        $(document).on("click",".del",function(){
        	var as_pubmsg_id = $(this).attr("pubmsgid");            	
	      	confirmTop("你确定要删除吗？",function(){
        	    var url = '<?php echo url("Pubmsg","ajax_deleteStore");?>';
	        	//var as_pubmsg_id = $(this).attr("as_pubmsg_id");            	
	        	var params = {
	        			as_pubmsg_id : as_pubmsg_id
	        		};
	        	ajax_submit(url,params,false,function(){
	        		window.location.reload();
	        	}); 
        	});
        });
				
});

//提交操作
function submit_operate() {
	var url = '<?php echo url("Pubmsg","ajax_nextListByUid");?>';
	_loading = true;
	tipList('show_msg_div', '正在加载数据,请稍后...');
	if(!hasnext){
		tipList('show_msg_div', '没有可加载的数据了!');
		return;
	}
	var uid = $("#uid").val();
	var selfUid = $("#selfUid").val();
	var params = {
			pageindex : pageindex,
			uid	: uid
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
				if(result.data.list[i].is_private==1 && result.data.list[i].uid!=selfUid){
					continue;
				}
				var contentStr = result.data.list[i].content;
				if(result.data.list[i].content.length>100){
				   	contentStr = result.data.list[i].content.substr(0,100)+"...";
				}
				htmlstr += '<dl>';
        		htmlstr += '<dt><span>'+result.data.list[i].dtstr+'</span>'+result.data.list[i].monthStr+'</dt>';
       			htmlstr += '<a href="<?php echo url("Pubmsg","onepubmsg");?>&pid='+result.data.list[i].as_pubmsg_id+'"><dd style="width: 80%;display:block;word-break: break-all;word-wrap: break-word;">'+contentStr+'</dd></a>';
    			htmlstr += '</dl>';
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
	 $("#tipdiv").css("top",$(document).scrollTop());
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
<input type="hidden" value="<?php echo $uid; ?>" id="uid"/>
<input type="hidden" value="<?php echo $selfUid; ?>" id="selfUid"/>

<div class="tab"><p><a href="<?php echo url("Pubmsg","index");?>" class="current">公共信池</a><a href="<?php echo url("Pubmsg","myStore");?>" >我的收藏</a></p></div>   
<div class="topic"><img src="images/xc_header.jpg" alt=""><strong><?php echo $otherObj['truthname'];?></strong><img class="head-pic" src="<?php echo $otherObj['headimgurl'];?>" alt=""></div> 

<div class="page-list" id="listul">
    
</div>
 <div class="loading-more" id="show_msg_div"><i class="loading-img"></i>正在加载...</div> 
 <?php tpl("Common.foot_co");?>