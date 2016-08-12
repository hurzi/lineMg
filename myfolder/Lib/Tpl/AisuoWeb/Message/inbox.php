  <?php tpl('Common.header');?>
 <link href="css/style.css?version=<?php AbcConfig::VERSION?>" rel="stylesheet" type="text/css"/>
   
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
			    htmlstr += '<li style="border-bottom: 1px solid #eeeeee;">';
			    htmlstr += '<a href="<?php echo url("Pubmsg","onePeople");?>&uid='+result.data.list[i].uid+'"><img src="'+result.data.list[i].headimgurl+'" width="50" height="50" alt=""></a>';
			    htmlstr += '<div class="msg-detail">';
			    htmlstr += '       <h2>'+result.data.list[i].truthname+'</h2>';
			    htmlstr += '      <p style="padding-left: 10px;width: 100%;display:block;word-break: break-all;word-wrap:break-word;">'+toHtml(result.data.list[i].content)+'</p>';
			    htmlstr += '       <div class="msg-tool" style="padding-top: 8px;padding-bottom: 5px;">'+result.data.list[i].create_time+'<span></span><!--<i class="write-msg"></i>--></div>';
			    htmlstr += '   </div>';
			   	htmlstr += '</li> ';	   
			}  
			$("#listul").append(htmlstr);
			if(hasnext == 1){
				tip('show_msg', '加载更多...');
			}else{
				tip('show_msg', '');
			}
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
  
  <header class="header">           
     <ul class="header_nav">
      <li><a href="<?php echo url("Message","index")?>">寄信</a></li>
      <li class="nav_active"><a href="<?php echo url("Message","inbox");?>">收信</a></li>
     </ul>
  </header>

   <div class="msglist">
    <h2 class="msg-total color_t_Green">共有<?php echo $inboxCount;?>封信件</h2>
    <ul id="listul">
       
      
    </ul>
    <div class="loading-more" id="show_msg">加载更多...</div>
</div>
    
   <?php tpl("Common.foot");?>  
    