$(function(){		   
	$(window).resize(sjbh);	
});

function sjbh(){

 var legz = $(".loadMack").length;
 if(legz>0){loadMack({})}
}

//调用方法一

function loadMf(obj){
   loadMack({off:'on',Limg:0,Mack:0,text:'正在加载页面...'});
   obj.load(function(){
	   loadMack({off:'off'});
   });	
}

//主方法
function loadMack(obj){
	var obj = obj || {off:'on'};	
	if(obj == 'off' || obj.off=="off"){
		$(".loadMack,.loadCon").fadeOut("800",function(){
           $(this).remove();
        }); 
		return;
	}
	
	var wbod= document.documentElement.clientWidth || document.body.clientWidth;
	var hwid = document.documentElement.clientHeight || document.body.clientHeight;
	var hbod= $("body").height();
    var bjh='';
    var bjw='';
	
	if(obj.Limg==undefined){obj.Limg = '<img style="margin:10px;" src="./Public_1/cj/load/loading_wb.gif" width="48" height="48" />';}else{obj.Limg='';}
	if(obj.text==undefined){obj.text = "加载中...";}
	if(obj.set==undefined){obj.set=0}

	
	
    if(obj.Mack==undefined){obj.Mack = '<div class="loadMack" style="width:'+wbod+'px;height:'+bjh+'px;"></div>';}else{obj.Mack='';}


	if(hbod>hwid){bjh=hbod}else{bjh=hwid};
	var leg = $(".loadMack").length;
	if(leg>0){
	   $(".loadMack").css({"height":""+bjh+"px"});
	   $(".loadCon").css({"left":""+(wbod/2-45)+"px","top":""+(hwid/2-100)+"px"});
	}else{

       $("body").append(''+obj.Mack+'<div style="left:'+(wbod/2-45)+'px;top:'+(hwid/2-100)+'px;" class="loadCon">'+obj.Limg+'<p style="text-align:center;font-size:14px;">'+obj.text+'</p></div>');

	}
    
    if(obj.set!==0){
	   setTimeout(function(){
		  
		 $(".loadMack,.loadCon").fadeOut("800",function(){
           $(this).remove();
		   return;
         }); 
		     
	   },obj.set);
	   	
	}
	
}
