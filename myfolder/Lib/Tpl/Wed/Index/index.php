<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title></title>
<meta name="description" content="WeBank"/>
<meta name="keywords" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<link rel="stylesheet" type="text/css" href="css/common.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div class="j_wapper box_0">
		<div class="box_wrap home_0">
            <!--<h2>欢迎趣摇</h2>-->
            <p class="img_lod"><img src="images/loading.gif" width="100%"></p>
            <p class="text_de">请稍等</p>
		</div>
	</div>
	<div class="j_wapper box_1" style="display:none">
		<div class="box_wrap home_1">
			<div class="home_img home0_bg home1_text">
			</div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
	<div class="j_wapper box_2" >
		<div class="box_wrap home_2">	
			<div class="home_img home1_bg home2_text">
			</div>

			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
	<div class="j_wapper box_3" >
		<div class="box_wrap home_3">
            <div class="home_img home2_bg">
            </div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
	<div class="j_wapper box_4" >
		<div class="box_wrap home_4">
            <div class="home_img home3_bg">
            </div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
	<div class="j_wapper box_5" >
		<div class="box_wrap home_5">
            <div class="home_img home4_bg">
            </div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
	<div class="j_wapper box_6" >
		<div class="box_wrap home_6">
            <div class="home_img home5_bg">
            </div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
	<div class="j_wapper box_7" >
		<div class="box_wrap home_7">
            <div class="home_img home6_bg">
            </div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
	<div class="j_wapper box_8" >
		<div class="box_wrap home_8">
            <div class="home_img home7_bg">
            </div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>	
	<div class="j_wapper box_9" >
		<div class="box_wrap home_9">
            <div class="home_img home8_bg">
            </div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
	<div class="j_wapper box_10" >
		<div class="box_wrap home_10">
            <div class="home_img home6_text home0_bg">
                <button class="receive_btn">查看相册</button>
            </div>
			<a href="javascript:;" class="b_arr_btn"><img src="images/home-go.png"></a>
		</div>
	</div>
    <audio id="bgsound" src="images/13.mp3" autoplay="" loop=""></audio>
    <dl class="bgsoundsw">
        <dt></dt>
        <dd></dd>
    </dl>
	<script type="text/javascript" src="js/zepto.min.js"></script>
  	<script type="text/javascript" src="js/touch.js"></script>
  	<script type="text/javascript" src="js/js.js?230506"></script>
    <script type="text/javascript">
        $(".receive_btn").on('click',function(){
            window.location.href="<?php echo url("Index","showPic");?>";
        })
    </script>
</body>
</html>