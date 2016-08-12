<?php tpl('Common.header_xinchi');?>
<style type="text/css">
i.write-msg {
	width: 30px;
	height: 30px;
	background-position:10px -160px;
}
</style>
 <script type="text/javascript">
var _loading = false;
var pageindex = 1;
var hasnext = 1;
var asPubmsgId = '<?php echo $msginfo["as_pubmsg_id"];?>';
$(document).ready(function(){

	
		//提交
		//$('#show_msg_div').click(function(){
		//	submit_operate(asPubmsgId);
		//});

		
		$('#closeTipA').click(function(){
			$('#tipdiv').hide();
		});	

		//初始加载一次数据
		submit_operate(asPubmsgId);
			
});

//提交操作
function submit_operate(asPubmsgId) {
	var url = '<?php echo url("Pubmsg","ajax_nextReplyList");?>';
	_loading = true;
	tipList('show_msg_div', '正在加载数据,请稍后...');
	if(!hasnext){
		tipList('show_msg_div', '没有可加载的数据了!');
		return;
	}
	var params = {
			pageindex : pageindex,
			as_pubmsg_id : asPubmsgId
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
				$("#show_msg_div").hide();	
	 		}
	 		var htmlstr = "";

			var selfUid = $("#selfUid").val();

			if(result.data.list.length == 0){
				if($(".zan-list p a").length == 0){
					$("#zanReplyDiv").hide();
				}
				$(".pl-cot-list").hide();
			}
	 		
			for(var i=0; i<result.data.list.length; i++)  
			{  
				var showname = result.data.list[i].truthname;
		   		if(result.data.list[i].is_private == 1){
		   			showname = "<?php echo AbcConfig::DEFAULT_USER_TRUTHNAME?>";
			   	}
			   	var content = result.data.list[i].content;			   	
// 			   	if(result.data.list[i].reply_to_name){
// 			   		showname = showname+"回复"+result.data.list[i].reply_to_name;
// 			   	}
			   	var replyuid = result.data.list[i].uid;
			   	var replyname = result.data.list[i].truthname;
			   	var asPubmsgReplyId = result.data.list[i].as_pubmsg_reply_id;
				var delReply = "";
// 				if(selfUid == replyuid){
// 					delReply += '  <span class="delReply" onclick="delReply(this)" as_pubmsg_reply_id="'+result.data.list[i].as_pubmsg_reply_id+'" style="color: #a2b3d6;float:right;">删除</span>';
// 			   	}
				
				htmlstr += '<section  id="reply'+result.data.list[i].as_pubmsg_reply_id+'" >';
				htmlstr += '<table width="100%">';
				htmlstr += '<tr>';
				htmlstr += '        <td rowspan="2" width="40" style="vertical-align: top;"><a href="javascript:jumpUid(\''+result.data.list[i].uid+'\',\''+showname+'\')"><img src="'+result.data.list[i].headimgurl+'" alt=""></a></td>';
				if(result.data.list[i].reply_to_name){
			   		//showname = showname+"回复"+result.data.list[i].replyList[j].reply_to_name;
					var replytouid = result.data.list[i].reply_to_uid;
			   		var replytoname = result.data.list[i].reply_to_name;
			   		//htmlstr += '            <dd><a href="<?php echo url("Pubmsg","onePeople")?>&uid='+result.data.list[i].replyList[j].uid+'">'+showname+'</a> 回复 <a href="<?php echo url("Pubmsg","onePeople")?>&uid='+replytouid+'">'+replytoname+'</a>：<a href="javascript:showPl(\''+result.data.list[i].as_pubmsg_id+'\',\''+replyuid+'\',\''+replyname+'\')">'+content+'</a></dd>';
			   		htmlstr += '        <td ><a href="javascript:jumpUid(\''+replyuid+'\',\''+showname+'\')" class="pl-uname" style="font-weight: bold;">'+showname+'</a> 回复 <a href="javascript:jumpUid(\''+replytouid+'\',\''+replytoname+'\')" class="pl-uname" style="font-weight: bold;">'+replytoname+'</a>'+delReply+'</td>';
				}else{			
			   		htmlstr += '        <td ><a href="javascript:jumpUid(\''+replyuid+'\',\''+showname+'\')" class="pl-uname" style="font-weight: bold;">'+showname+'</a>'+delReply+'</td>';
			   	}
				
				htmlstr += '        <td align="right" width="100px"><span class="pl-time" >'+result.data.list[i].create_time+'</span></td>';
				htmlstr += '    </tr>';
				htmlstr += '     <tr>';
				htmlstr += '        <td colspan="3"><p><a href="javascript:showPl(\'<?php echo $msginfo["as_pubmsg_id"];?>\',\''+replyuid+'\',\''+replyname+'\',\''+asPubmsgReplyId+'\')" style="color:#22292c">'+content+'</a></p></td>';
				htmlstr += '    </tr>';
				htmlstr += '</table>';
				htmlstr += '</section>';
				
			}  
			$("#listul").append(htmlstr);
			/*
			if(hasnext == 1){
				tipList('show_msg_div', '加载更多...');
			}else{
				tipList('show_msg_div', '');
				$("#show_msg_div").hide();				
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
<input type="hidden" value="<?php echo $selfObj['uid']; ?>" id="selfUid"/>
<!-- <input type="hidden" value="<?php echo $msginfo['content'];?>" id="mainContent"/>
 -->
<p style="display: none" id="mainContentP"><?php echo $msginfo['content'];?></p>
<div class="tab"><p><a href="<?php echo url("Pubmsg","index");?>" class="current">公共信池</a><a href="<?php echo url("Pubmsg","myStore");?>" >我的收藏</a></p></div>   
<div class="msglist">
    <ul>
        <li>
            <img src="<?php echo $msgOwnerObj['headimgurl']?>" width="40" height="40" alt="">
            <div class="msg-detail">
                <h2><?php echo $msgOwnerObj['truthname']?></h2>
                <p id="p_mainContent" style="width: 100%;display:block;word-break: break-all;word-wrap: break-word;">
                <script type="text/javascript">
                var mainContent = $("#mainContentP").html();
               // var htmlcontentstr = toHtml(mainContent);
                var htmlcontentstr = mainContent;
//             	if(mainContent.length>100){
//                 	htmlcontentstr = trim(mainContent.substr(0,100))+'<span class="zk" onclick="zktext(this)" style="color: #576b95;">... 展开</span><span style="display:none">'+mainContent.substr(101)+'</span>'+'</p>';
// 				}
            	$("#p_mainContent").html(htmlcontentstr);
                </script>
                </p>
                <div class="msg-tool">
                    <span><?php echo date('m月d日 H:i',strtotime($msginfo['create_time']));?></span>
                    <?php if($allowDel){?>
                    <a href="javascript::void(0)" pubmsgid="<?php echo $msginfo['as_pubmsg_id']?>" class="del">删除</a>
                    <?php }?>
                    <i  onclick="write_msg_tip(this);" class="write-msg"></i>
                    <div class="btn-tool">
                        <a href="javascript:void(0)"  pubmsgid="<?php echo $msginfo["as_pubmsg_id"];?>" class="zan"><i></i>点赞</a><a href="javascript:void(0)" pubmsgid="<?php echo $msginfo["as_pubmsg_id"];?>"  class="pl"><i></i>评论</a><a href="javascript:void(0)" pubmsgid="<?php echo $msginfo["as_pubmsg_id"];?>"  class="sc"><i></i>收藏</a>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="msg-plbox single-page" id="zanReplyDiv">
                <i class="arrow"></i>
                <?php if($zanlist){?>
                <div class="zan-list">
                    <i class="zan-icon"></i>
                    <p>
                    	<?php 
                    		foreach ($zanlist as $v){
                    	?>
                        <a href="#"><img src="<?php echo $v['headimgurl']?>" alt=""></a>
                        <?php }?>
                    </p>
                </div>
                <?php }?>
                <div class="clear"></div>
                <div class="pl-cot-list">
                    <i class="pl1-icon"></i>
                    <div class="plcot" id="listul" style="border-top: 1px solid #dfdfdd;">
                        
                    </div>
                    <div class="loading-more" id="show_msg_div"><i class="loading-img"></i>正在加载...</div>
                </div>
            </div>
        </li>
    </ul>      
</div>
<a href="<?php echo url("Pubmsg","index");?>"><img src="images/fh.png" width="45"  height="45" style="position:fixed;top:50%;left:9px;z-index:9999"></a>

<div id="plbox">

</div>
<script>

/**
 * 展开
 */
function zktext(thisobj){
	$(thisobj).next().show();
   	$(thisobj).hide();
}

/**
 * 跳转用户
 */
function jumpUid(uid,uname){
	var selfUid = $("#selfUid").val();
	var url = '<?php echo url("Pubmsg","onePeople");?>';
	if(selfUid!=uid && uname == "匿名"){
		tip('show_msg','匿名不能查看');
		return;
	}else{
		location.href=url+"&uid="+uid;
	}	
}

function write_msg_tip(thisobj){
	 if($(thisobj).next().hasClass("on")){
	 	$(thisobj).next().removeClass("on");
	 }else{                
	     $(".btn-tool").removeClass("on");
	     $(thisobj).next().addClass("on");
	 }
}

/**
 * 删除回复
 */
function delReply(asPubmsgReplyId){
	var as_pubmsg_reply_id = asPubmsgReplyId;//$(thisObj).attr("as_pubmsg_reply_id");            	
  	confirmTop("你确定要删除吗？",function(){
		var url = '<?php echo url("Pubmsg","ajax_deletePubmsgReply");?>';
		var params = {
      			as_pubmsg_reply_id : as_pubmsg_reply_id
      		};
  		ajax_submit(url,params,false,function(retData){
      				$('<div class="error-box">删除回复成功</div>').appendTo("body");
	                 removeMsg();
	                 $("#reply"+as_pubmsg_reply_id).remove();
	                 //window.location.reload();
         });
    });
}
    $(function(){
//         // 弹出工具按钮
//         $(document).on("click",".write-msg",function(){
//         	　$(".btn-tool").removeClass("on");            
// 	       	 if($(this).next().hasClass("on")){
// 	          	$(this).next().removeClass("on");
// 	          }else{                
// 		            $(".write-msg").removeClass("on");
// 		            $(this).next().addClass("on");
// 	          }
//         })
        
        // 删除贴子
        $(document).on("click",".del",function(){
        	var as_pubmsg_id = $(this).attr("pubmsgid");  
        	      	
	      	confirmTop("你确定要删除吗？",function(){
        	   var url = '<?php echo url("Pubmsg","ajax_deletePubmsg");?>';
        	   var jumpUrl = '<?php echo url("Pubmsg","index");?>';
  		       	var params = {
		      			as_pubmsg_id : as_pubmsg_id
		      		};
		      	ajax_submit(url,params,false,function(retData){
			           		 $('<div class="error-box">删除帖子成功</div>').appendTo("body");
			                 removeMsg();
			                 window.location.href=jumpUrl;
		         });
        	});
        });



        // 点赞
        $(document).on("click",".zan",function(){
            that = $(this);
            i = that.find("i").eq(0);
            var showDiv = $(this).parent();
            if(i.hasClass("on")){               
                var url = '<?php echo url("Pubmsg","ajax_deleteZan");?>';
            	var as_pubmsg_id = $(this).attr("pubmsgid");            	
            	var params = {
            			as_pubmsg_id : as_pubmsg_id
            		};
            	ajax_submit(url,params,false,function(retData){
	           		 $('<div class="error-box">取消赞成功</div>').appendTo("body");
	                 removeMsg();
                },function(retData){
                	showDiv.removeClass("on");
                });
            }else{
                var url = '<?php echo url("Pubmsg","ajax_addZan");?>';
            	var as_pubmsg_id = $(this).attr("pubmsgid");            	
            	var params = {
            			as_pubmsg_id : as_pubmsg_id
            		};
            	ajax_submit(url,params,false,function(retData){
                	$('<div class="error-box">赞成功</div>').appendTo("body");
                    removeMsg();
                    window.location.reload();
                },function(retData){
                	showDiv.removeClass("on");
                });
            }
        })

        // 收藏
        $(document).on("click",".sc",function(){
            that = $(this);
            i = that.find("i").eq(0);
            var showDiv = $(this).parent();
            if(i.hasClass("on")){
            	var url = '<?php echo url("Pubmsg","ajax_deleteStore");?>';
            	var as_pubmsg_id = $(this).attr("pubmsgid");            	
            	var params = {
            			as_pubmsg_id : as_pubmsg_id
            		};
            	ajax_submit(url,params,false,function(retData){
            		$('<div class="error-box">取消收藏成功</div>').appendTo("body");
                    removeMsg();
                },function(retData){
                	showDiv.removeClass("on");
                });                
            }else{
	           	var url = '<?php echo url("Pubmsg","ajax_addStore");?>';
	          	var as_pubmsg_id = $(this).attr("pubmsgid");            	
	          	var params = {
	          			as_pubmsg_id : as_pubmsg_id
	          		};
	          	ajax_submit(url,params,false,function(retData){
	          		$('<div class="error-box">收藏成功</div>').appendTo("body");
	                removeMsg();
	            },function(retData){
                	showDiv.removeClass("on");
                });                
            }
        })

        // 评论
        $(document).on("click",".pl",function(){
            showPl($(this).attr("pubmsgid"),'','');
        })
        //取消发布
        $(document).on("click","#cancel-mask",function(){
            $("#plbox").html("");
        })
		//发布
		 $(document).on("click","#sub_btn",function(){
           
			var content = $("#content").val();			
			var is_private = $('input[name="is_private"]:checked').val();
			var url = '<?php echo url("Pubmsg","ajax_addReply");?>';
			var as_pubmsg_id = $(this).attr("pubmsgid");     
			var replyuid = $("#replyuid").val();     
			var replyname = $("#replyname").val();     
			if(!content){
				tip('show_msg', '内容不能为空');
				return false;
			}
			if(is_private == null || is_private==undefined || is_private == ""){
				is_private = 0;
			}
			$("#content").val("");
			var params = {
					content : content,
					is_private :is_private,
					as_pubmsg_id : as_pubmsg_id,
					replyuid : replyuid,
					replyname : replyname
				};
			ajax_submit(url,params,false,function(retData){
				$("#plbox").html("");
          		$('<div class="error-box">发布成功</div>').appendTo("body");
                removeMsg();
                window.location.reload();
            });
        })
    })
function removeMsg(){
    var t = setTimeout(function(){$(".error-box").hide();},2000)
}
function showPl(pubmsgid,replyuid,replyname,asPubmsgReplyId){
	var selfUid = $("#selfUid").val();
	//alert(asPubmsgReplyId);          	
  	if(selfUid == replyuid){
		delReply(asPubmsgReplyId);
		return;
	}
	
	var tipStr = "说几句吧...";
	if(replyname){
		tipStr = "回复"+replyname;
	}
   var str = '<div class="send-div">' +
    '<div class="sendmsg-box">' +
        '<textarea placeholder="'+tipStr+'" id="content" style="font-size:14px;word-wrap:break-word; word-break:break-all;"></textarea>' + 
        '<div class="send-btns">' +
	        '<input type="hidden" id="replyuid" value="'+replyuid+'">'+
	    	'<input type="hidden" id="replyname" value="'+replyname+'">'+
	        '<label><input type="radio" name="is_private" value="0" checked="checked"/>公开</label><label><input type="radio" value="1"  name="is_private" />匿名</label>' +
            '<input type="button" class="sbtn" id="cancel-mask" value="取消" /><input type="button" pubmsgid="'+pubmsgid+'"  id="sub_btn" class="sbtn" value="发布">' +
       '</div>' +
    '</div>' +
'</div>' + '<div class="mask"></div>';
    $("#plbox").html(str);
    maskInit();
}
function maskInit(){
    var height = ($(document).height()>$(window).height())?$(document).height():$(window).height();
    $(".mask").css("height",height);
}
</script>
 <?php tpl("Common.foot_co");?>