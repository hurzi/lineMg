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
	var url = '<?php echo url("Pubmsg","ajax_nextList");?>';
	_loading = true;
	tipList('show_msg_div', '正在加载数据,请稍后...');
	if(!hasnext){
		tipList('show_msg_div', '没有可加载的数据了!');
		return;
	}
	var timestamp = '<?php echo $timestamp; ?>';
	var params = {
			pageindex : pageindex,
			timestamp : timestamp
		};
	var selfUid = $("#selfUid").val();
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
// 			    htmlstr += "<li>";
// 			    htmlstr += '<img src="'+result.data.list[i].headimgurl+'" width="50" height="50" alt="">';
// 			    htmlstr += '<div class="msg-detail">';
// 			    htmlstr += '       <h2>'+result.data.list[i].truthname+'</h2>';
// 			    htmlstr += '      <p>'+result.data.list[i].content+'</p>';
// 			    htmlstr += '       <div class="msg-tool">'+result.data.list[i].create_time+'<span></span><!--<i class="write-msg"></i>--></div>';
// 			    htmlstr += '   </div>';
// 			   	htmlstr += '</li> ';

			   	htmlstr += '<li id="mid'+result.data.list[i].as_pubmsg_id+'">';
			   	if(result.data.list[i].is_private==1 && result.data.list[i].uid!=selfUid){
			   		htmlstr += '<a href="javascript:void(0);" onclick="tip(\'show_msg\',\'匿名不能查看\');"><img src="'+result.data.list[i].u_headimgurl+'" width="40" height="40" alt=""></a>';
			   	}else{
			   		htmlstr += '<a href="<?php echo url("Pubmsg","onePeople")?>&uid='+result.data.list[i].uid+'"><img src="'+result.data.list[i].u_headimgurl+'" width="40" height="40" alt=""></a>';
			   	}
			   	htmlstr += '<div class="msg-detail">';
			   	htmlstr += '     <h2>'+result.data.list[i].u_truthname+'</h2>';
			   	if(result.data.list[i].content.length<200){
			   		htmlstr += '    <p class="msg-content" onclick="jumpOnePubMsg(this)" pubmsgid="'+result.data.list[i].as_pubmsg_id+'"  style="color:#22292c;width: 100%;display:block;word-break: break-all;word-wrap: break-word;">'+toHtml(trim(result.data.list[i].content))+'</p>';
			   	}else{
			   		htmlstr += '    <p class="msg-content" onclick="jumpOnePubMsg(this)" pubmsgid="'+result.data.list[i].as_pubmsg_id+'"  style="color:#22292c;width: 100%;display:block;word-break: break-all;word-wrap: break-word;">'+toHtml(trim(result.data.list[i].content.substr(0,100)))+'<span class="zk"  style="color: #576b95;">......</span><span style="display:none">'+result.data.list[i].content.substr(101)+'</span>'+'</p>';
				}
			   	htmlstr += '    <div class="msg-tool">';
			   	htmlstr += '        <span>'+result.data.list[i].create_time+'</span>';
			   	if(result.data.openid == result.data.list[i].openid){
			   		htmlstr += '        <a href="javascript:void(0)" pubmsgid="'+result.data.list[i].as_pubmsg_id+'" class="del">删除</a>';
			   	}
			   	htmlstr += '        <i  onclick="write_msg_tip(this);"  class="write-msg"></i>';
			   	htmlstr += '        <div class="btn-tool">';
			   	htmlstr += '            <a href="javascript:void(0)" pubmsgid="'+result.data.list[i].as_pubmsg_id+'" class="zan"><i></i>点赞</a><a href="javascript:void(0)" pubmsgid="'+result.data.list[i].as_pubmsg_id+'" class="pl"><i></i>评论</a><a href="javascript:void(0)" pubmsgid="'+result.data.list[i].as_pubmsg_id+'" class="sc"><i></i>收藏</a>';
			   	htmlstr += '        </div>';
			   	htmlstr += '    </div>';
			   	htmlstr += '    <div class="clear"></div>';
			   	if(result.data.list[i].zanList.length>0 || result.data.list[i].replyList.length>0){
			   	   	htmlstr += '    <div class="msg-plbox">';		   	   	
				   	htmlstr += '        <i class="arrow"></i>';
				   	if(result.data.list[i].zanList.length>0){
						htmlstr += '        <div class="zan-list"><i class="zan-icon"></i>';
						for(var zi=0; zi<result.data.list[i].zanList.length; zi++)  {						
							htmlstr += ((zi!=0)?'<span style="color: #22292c;">,</span>':'')+'        <strong id="'+result.data.list[i].zanList[zi].uid+'"><a href="<?php echo url("Pubmsg","onePeople")?>&uid='+result.data.list[i].zanList[zi].uid+'">'+result.data.list[i].zanList[zi].truthname+'</a></strong>';
						}
						htmlstr += '        </div>';
					}
				   	if(result.data.list[i].replyList.length>0){
					   	if(result.data.list[i].zanList.length>0){					   		
					   		htmlstr += '        <dl class="pllist" style="border-top: 1px solid #dfdfdd;">';
					   	}else{
					   		htmlstr += '        <dl class="pllist">';
					   	}					   	
				   		for(var j=0; j<result.data.list[i].replyList.length; j++)  {
					   		var showname = result.data.list[i].replyList[j].truthname;
					   		if(result.data.list[i].replyList[j].is_private == 1){
					   			showname = "<?php echo AbcConfig::DEFAULT_USER_TRUTHNAME?>";
						   	}
						   	var content = trim(result.data.list[i].replyList[j].content);
							if(content.length>50){
								content = content.substr(0,50)+'...';
							}
						   	var replyuid = result.data.list[i].replyList[j].uid;
						   	var replyname = result.data.list[i].replyList[j].truthname;
						   	var delReply = "";
						   	var asPubmsgReplyId = result.data.list[i].replyList[j].as_pubmsg_reply_id;
// 							if(selfUid == replyuid){
// 								delReply += '  <span class="delReply" onclick="delReply(this)" as_pubmsg_reply_id="'+result.data.list[i].replyList[j].as_pubmsg_reply_id+'" style="color: #a2b3d6;float:right;">删除</span>';
// 						   	}
						   	
							if(result.data.list[i].replyList[j].reply_to_name){
						   		//showname = showname+"回复"+result.data.list[i].replyList[j].reply_to_name;
								var replytouid = result.data.list[i].replyList[j].reply_to_uid;
						   		var replytoname = result.data.list[i].replyList[j].reply_to_name;
						   		htmlstr += '            <dd id="reply'+asPubmsgReplyId+'"><a href="javascript:jumpOnePeople(\''+result.data.list[i].replyList[j].uid+'\',\''+showname+'\')" style="font-weight: bold;">'+showname+'</a> 回复 <a href="javascript:jumpOnePeople(\''+replytouid+'\',\''+replytoname+'\')" style="font-weight: bold;">'+replytoname+'</a>：<a href="javascript:showPl(\''+result.data.list[i].as_pubmsg_id+'\',\''+replyuid+'\',\''+replyname+'\',\''+asPubmsgReplyId+'\')" as_pubmsg_reply_id="'+asPubmsgReplyId+'" style="color:#22292c;width: 100%;word-break: break-all;word-wrap: break-word;">'+toHtml(content)+'</a>'+delReply+'</dd>';
						   	}else{
						   		htmlstr += '            <dd id="reply'+asPubmsgReplyId+'"><a href="javascript:jumpOnePeople(\''+result.data.list[i].replyList[j].uid+'\',\''+showname+'\')" style="font-weight: bold;">'+showname+'：</a><a href="javascript:showPl(\''+result.data.list[i].as_pubmsg_id+'\',\''+replyuid+'\',\''+replyname+'\',\''+asPubmsgReplyId+'\')" as_pubmsg_reply_id="'+asPubmsgReplyId+'" style="color:#22292c;width: 100%;word-break: break-all;word-wrap: break-word;">'+toHtml(content)+'</a>'+delReply+'</dd>';
						   	}			
						   		   	
				   		//htmlstr += '            <dd><a href="#">你大爷</a>回复<a href="#">十三姨</a>：周末郊游去？</dd>';
				   		}
// 				   		if(result.data.list[i].reply_count>3){
// 				   			htmlstr += '            <dd id="reply'+asPubmsgReplyId+'" onclick="jumpOnePubMsg(this)" pubmsgid="'+result.data.list[i].as_pubmsg_id+'" style="font-weight: bold;color: #576b95;">查看更多...</dd>';
// 				   		}
				   		htmlstr += '        </dl>';
				   		if(result.data.list[i].reply_count>3){
				   			htmlstr +='<div class="zan-list" style="display:block;text-align:left;height:35px;font-size:1.3rem;padding-top:9px;border-top: 1px solid #FDFDFD;border-bottom: 1px solid #F3F3F3; font-weight:normal"><font color="#576B95" onclick="jumpOnePubMsg(this)" pubmsgid="'+result.data.list[i].as_pubmsg_id+'">点击查看更多</font></div>';
				   		}
					}
				   	htmlstr += '    </div>';
			   	}
			   
			   	htmlstr += '</div>';
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
	 $("#tipdiv").css("top",$(document).scrollTop());
	 //$("#tipdiv").css("position","fixed");
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
<div class="topic"><img src="images/temp4.jpg" alt=""><strong><?php echo $selfObj['truthname'];?></strong><a href="<?php echo url("Pubmsg","onePeople",array("uid"=>$selfObj['uid']));?>"><img class="head-pic" src="<?php echo $selfObj['headimgurl'];?>" alt=""></a></div> 

<?php if($unReady && $unReady["unReadyCount"]>0){?>
<div class="msgbox"><div class="new-msg"><img src="<?php echo $unReady["headimgurl"];?>" width="30" height="30" alt=""><a href="<?php echo url("Pubmsg","pubMsgNotice");?>"><font style="font-family:Arial, Helvetica, sans-serif"><?php echo $unReady["unReadyCount"]; ?></font>条新消息</a></div></div>
<?php }?>

<input type="hidden" value="<?php echo $selfObj['uid']; ?>" id="selfUid"/>
<div class="msglist">
    <ul id="listul">
        
    </ul>
    <div class="loading-more" id="show_msg_div"><i class="loading-img"></i>正在加载...</div>   
</div>


 <div class="write-msg-btn" style="padding-bottom:6;position:fixed;bottom:0;left:0; background-color:#fff;opacity:0.8;filter:alpha(opacity=80)">
    <a href=""></a>
    </div>
     <div class="write-msg-btn" style="padding-bottom:6;position:fixed;bottom:0;left:0;">
        <a href="<?php echo url('Pubmsg','writePubmsg')?>" ><i class="icon-write"></i>写公开信</a>
    </div>

<div id="plbox">

</div>
<script>

var __OP_STATE = false;

function write_msg_tip(thisobj){
	// $(".btn-tool").removeClass("on");
	 if($(thisobj).next().hasClass("on")){
	 	$(thisobj).next().removeClass("on");
	 }else{                
	     $(".btn-tool").removeClass("on");
	     $(thisobj).next().addClass("on");
	 }
}

/**
 * 展开
 */
function zktext(thisobj){
	$(thisobj).next().show();
   	$(thisobj).hide();
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
      				$('<div class="error-box">删除成功</div>').appendTo("body");
	                 removeMsg();
	                 $("#reply"+as_pubmsg_reply_id).remove();
	                 //window.location.reload();
         });
    });
}

/**
 * 跳转到某一个人的主页
 */
function jumpOnePeople(uid,nickname){
	var selfUid = $("#selfUid").val();	
	if(selfUid!=uid && nickname == '匿名'){
		tip('show_msg','匿名不能查看');
		return;
	}
	var url = '<?php echo url("Pubmsg","onePeople");?>&uid='+uid;
	window.location.href=url;
}

/**
 * 跳转单个贴子
 */
function jumpOnePubMsg(thisobj){
	var url = '<?php echo url("Pubmsg","onePubmsg");?>';
  	var as_pubmsg_id = $(thisobj).attr("pubmsgid");            	
  	location.href = url+"&pid="+as_pubmsg_id;
}
    $(function(){
//     	$(".write-msg").on("click",function(){
// 			$(".btn-tool").removeClass("on");
// 			$(this).next().addClass("on");

//         });
//         $(document).on("click",".write-msg",function(){
// 			$(".btn-tool").removeClass("on");
// 			$(this).next().addClass("on");

//         });
       // 弹出工具按钮
        //$(document).on("click",".write-msg",function(){
//         $(".write-msg").live("click",function(){
//             alert("abc");
//             $(".btn-tool").removeClass("on");
//             if($(this).next().hasClass("on")){
//             	$(this).next().removeClass("on");
//             }else{                
// 	            $(".write-msg").removeClass("on");
// 	            $(this).next().addClass("on");
//             }
//         })
        
       

//     	$(".msg-content .zk").live("click",function(){
// 	       	$(this).next().show();
// 	       	$(this).hide();
//          });
        
        // 删除贴子
        $(".del").live("click",function(){
        	var as_pubmsg_id = $(this).attr("pubmsgid");            	
	      	confirmTop("你确定要删除吗？",function(){
        		var url = '<?php echo url("Pubmsg","ajax_deletePubmsg");?>';
		      	var params = {
		      			as_pubmsg_id : as_pubmsg_id
		      		};
		      	ajax_submit(url,params,false,function(retData){
			           		 $('<div class="error-box">删除帖子成功</div>').appendTo("body");
			                 removeMsg();
			                 window.location.reload();
		         });
            });
            	
        });

                

        // 点赞
        $(".zan").live("click",function(){
            if(__OP_STATE==true){
            	tip('show_msg','不能连续点赞，请等待.');
        		return false;
            }
            __OP_STATE = true;
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
	                 __OP_STATE = false;
	                 window.location.reload();
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
//                 	$('<div class="error-box">赞成功</div>').appendTo("body");
//                     removeMsg();
	                 __OP_STATE = false;
                    window.location.reload();
                },function(retData){
                	showDiv.removeClass("on");
                });
            }
            __OP_STATE = false;
        })

        // 收藏
        $(".sc").live("click",function(){
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
//             		$('<div class="error-box">取消收藏成功</div>').appendTo("body");
//                     removeMsg();
                    window.location.reload();
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
	                window.location.reload();
	            },function(retData){
                	showDiv.removeClass("on");
                });                
            }
        })

        // 评论
        $(".pl").live("click",function(){
            showPl($(this).attr("pubmsgid"),'','');
        })
        //取消发布
        $("#cancel-mask").live("click",function(){
            $("#plbox").html("");
        })
		//发布
		 $("#sub_btn").live("click",function(){
			if(__OP_STATE==true){
				return false;
	        }
	        __OP_STATE = true;
			var content = $("#content").val();			
			var is_private = $('input[name="is_private"]:checked').val();
			var url = '<?php echo url("Pubmsg","ajax_addReply");?>';
			var as_pubmsg_id = $(this).attr("pubmsgid");     
			var replyuid = $("#replyuid").val();     
			var replyname = $("#replyname").val();     
			if(!content){
				//tip('show_msg', '内容不能为空');
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
//           		$('<div class="error-box">发布成功</div>').appendTo("body");
//                 removeMsg();
				__OP_STATE = true;
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
        '<textarea placeholder="'+tipStr+'" id="content"  style="font-size:14px"></textarea>' + 
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
