<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
        <title>大转盘</title>
        <meta http-equiv="Content-Language" content="zh-CN" />
	    <meta id="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1, user-scalable=no" name="viewport" />
	    <meta content="yes" name="apple-mobile-web-app-capable">
	    <meta content="black" name="apple-mobile-web-app-status-bar-style">
	    <meta content="telephone=no" name="format-detection">
	    <meta name="keywords" content="jQuery大转盘,jQuery抽奖" />
        <meta name="description" content="" />
        <style type="text/css">
            .demo{ position:relative;width:350px;margin:auto;}
            #disk{width:350px; height:350px; background:url(<?php echo URL?>dazhuanpan/images/disk.jpg) no-repeat;}
            #start{width:140px; height:272px; position:absolute; top:38px; left:107px;}
            #start img{cursor:pointer}
        </style>
    </head>
    <body>
        <div class="head">
            <div class="head_inner" style="margin-bottom: 20px;">
                	<center><h1>幸运大转盘</h1></center>
            </div>
        </div>
        <div class="container" style="text-align: center;">
            <div class="demo">
                <div id="disk"></div>
                <div id="start"><img src="<?php echo URL?>dazhuanpan/images/start.png" id="startbtn" alt="转盘开启"/></div>
            </div>  
        </div>
        <div>
        	
        <div>
        <script type="text/javascript" src="<?php echo CDN_PATH?>Common/js/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="<?php echo CDN_PATH?>Common/js/jQueryRotate.2.2.js"></script>
        <script type="text/javascript" src="<?php echo CDN_PATH?>Common/js/jquery.easing.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $("#startbtn").click(function() {
                    lottery();
                });
            });
            function lottery() {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo url("H5Activity","ajax_getAward")?>',
                    dataType: 'json',
                    cache: false,
                    error: function() {
                        alert('Sorry，出错了！');
                        return false;
                    },
                    success: function(json) {
                        $("#startbtn").unbind('click').css("cursor", "default");
                        var angle = json.angle; //指针角度 
                        var prize = json.prize; //中奖奖项标题 
                        $("#startbtn").rotate({
                            duration: 3000, //转动时间 ms
                            angle: 0, //从0度开始
                            animateTo: 3600 + angle, //转动角度 
                            easing: $.easing.easeOutSine, //easing扩展动画效果
                            callback: function() {
                                alert('恭喜您中得' + prize + '');
                            }
                        });
                    }
                });
            }
        </script>

    </body>
</html>