$(function(){

	var ua = navigator.userAgent,
		isWx = ua.indexOf("MicroMessenger")>=0,
		isAyb = location.href.split("?")[1] == "f=app",
		isAdr = ua.indexOf("Android")>=0,
		isIph = ua.indexOf("iPhone")>=0;
		
		
		
	var alw = 70,
		cuurmtop = 0,
		tStart = 0,
		tEnd = 0,
		start2end = 0,
		currScreen = 1,
		currHeight = $("#wrap").height();

	$(".wrap ul").height(currHeight*6);
	$(".wrap ul li").height(currHeight);
	var playAni = function(){
		if(currScreen === 1){
			$("#wrap li").eq(0).addClass("page01");
		}else{
			$("#wrap li").eq(0).removeClass("page01");
		}
		if(currScreen === 2){
			$("#wrap li").eq(1).addClass("page02")
		}else{
			$("#wrap li").eq(1).removeClass("page02")
		}
		if(currScreen === 3){
			$("#wrap li").eq(2).addClass("page03")
		}else{
			$("#wrap li").eq(2).removeClass("page03")
		}
		if(currScreen === 4){
			$("#wrap li").eq(3).addClass("page04")
		}else{
			$("#wrap li").eq(3).removeClass("page04")
		}
		if(currScreen === 5){
			$("#wrap li").eq(4).addClass("page05")
		}else{
			$("#wrap li").eq(4).removeClass("page05")
		}
		if(currScreen === 6){
			$("#wrap li").eq(5).addClass("page06")
		}else{
			$("#wrap li").eq(5).removeClass("page06")
		}
		if(currScreen === 7){
			$("#wrap li").eq(6).addClass("page07")
		}else{
			$("#wrap li").eq(6).removeClass("page07")
		}
		if(currScreen === 8){
			$("#wrap li").eq(7).addClass("page08");
		}else{
			$("#wrap li").eq(7).removeClass("page08");
		}
		if(currScreen === 9){
			$("#wrap li").eq(8).addClass("page09");
		}else{
			$("#wrap li").eq(8).removeClass("page09");
		}
	}
	
	var slide = function(){
		$("#wrap ul").css({"margin-top":-currHeight*(currScreen-1)});
		var callback = arguments[0];
		if($.isFunction(callback)) setTimeout(callback,500);
	}
	

	$("#wrap").bind("touchstart",function(e){
		tStart = e.targetTouches[0].clientY;
	}).bind("touchmove",function(e){
		e.preventDefault();
		tEnd = e.targetTouches[0].clientY;
		start2end = tEnd - tStart;
		if((currScreen==1 && start2end>=0) || (currScreen==9 && start2end<=0)) return;
		$("#wrap ul").removeClass("ani").css({"margin-top":Number(cuurmtop)+start2end});		
	}).bind("touchend",function(e){
		if(start2end){
			$("#wrap ul").addClass("ani");
			var isChange = true;
			if(start2end+alw<0){
				isChange = currScreen++>=9;
				currScreen = isChange ? currScreen-1:currScreen;
			}
			if(start2end-alw>0){
				isChange = currScreen--<=1;
				currScreen = isChange ? currScreen+1:currScreen;
			}
			slide(!isChange?playAni:undefined);
			cuurmtop = -currHeight*(currScreen-1);
			tStart = tEnd =	start2end = 0;
		}

	})
	
	var init = function(){
		$("#wrap ul li").each(function(index) {
            if(!$(this).hasClass("s"+(index+1))){
				$(this).addClass("s"+(index+1))
			}
        });
	
		slide(playAni);
	}
	
	
	var docSt = setInterval(function(){
		if(document.readyState=="complete"){
			clearInterval(docSt);
			$(window).resize(init).triggerHandler("resize");
		}
	},10);



})
