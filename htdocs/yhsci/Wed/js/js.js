(function (window, undefined){
	run();
	function run(){
		var oBody	= document.getElementsByTagName('body')[0],
			height	= document.documentElement.clientHeight,
			width	= document.documentElement.clientWidth,
			num		=1,
			iStartY;
		if(oBody){
			oBody.addEventListener('touchstart',slide);
			oBody.addEventListener('touchend',slide);
			oBody.addEventListener('touchmove',slide);
		}
		var f = true;
		function slide(event){
			switch(event.type){
				case "touchstart" :
					disY	= event.changedTouches[0].clientY;
					iStartY = 0;
					break;
				case "touchend" :
					function up(n){
						$('.box_'+num).removeClass("tb").addClass("ty");
						setTimeout(function(){
							$('.box_'+num-1).hide();
							$(".home_img").hide();
							$('.box_'+num).find(".home_img").show();							
						},200)
					}
					function down(){
						$('.box_'+num).show().removeClass("ty").addClass("tb");
						setTimeout(function(){
							$('.box_'+num).find(".home_img").show();
							$(".home"+num-0+1+"_text").hide();
						},200)
					}
					//alert(iStartY);
					if(iStartY < -120){
						if(num == 15){
							$(".none").show();
						}
						if(num< $(".j_wapper").size()){
							up();
							num++;
						}
						else{
							num = 1;
							$('.j_wapper').removeClass("ty")
							down();
						}
					}
					else if(iStartY > 120){
						if(num<=0){
							num = 1;
						}
						else{
							num--;			
						}
						down();
					}
					break;
				break;
				case "touchmove" :
					iStartY = event.changedTouches[0].clientY - disY;
					event.preventDefault();
					break;
			}
		}

		setTimeout(function(){
			$(".box_0").remove();
			$('.box_1').show()
		},1000)


        $("#bgsound")[0].play();
        //背景声音开关

        $(".bgsoundsw").on('touchstart',function(e){
            if($(this).children('dd').css("display") == "none"){
                $("#bgsound")[0].pause();
            }else{
                $("#bgsound")[0].play();
            }
            $(this).children().toggle();

        })
        var audio = document.getElementById("bgsound");
        audio.addEventListener('ended', function () {
            audio.play()
            //setTimeout(function () { audio.play(); }, 500);
        }, false);

		
	}


})(window, undefined)