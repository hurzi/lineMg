<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>爱情故事</title>
<meta name="description" content="WeBank"/>
<meta name="keywords" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<link rel="stylesheet" type="text/css" href="css/common.css">
<link rel="stylesheet" type="text/css" href="css/love2.css">
<?php tpl('Index.header');?>
</head>
<body>
	<div id="wrap" class="wrap">
			
			<ul>
                <li>
					<div class="bg1">
						<div class="uptip"></div>
					</div>
				</li>
				<li>
					<div class="bg2">
						
						<div class="uptip"></div>
					</div>
				</li>
				<li>
					<div class="bg3">
						<div class="uptip"></div>
					</div>
				</li>				
				<li>
					<div class="bg4">
						<div class="uptip"></div>
					</div>
				</li>
				<li>
					<div class="bg5">
						<div class="uptip"></div>
					</div>
				</li>
				<li>
					<div class="bg6">
						<div class="uptip"></div>
					</div>
				</li>
				<li>
					<div class="bg7">
						<div class="uptip"></div>
					</div>
				</li>
				<li>
					<div class="bg8">
						<div class="uptip"></div>
					</div>
				</li>
				<li>
					<div class="bg9">
						<?php tpl('Index.base_bottom');?>
					</div>
				</li>
			</ul>
		</div>
    <audio id="bgsound" src="images/music.mp3" autoplay="" loop=""></audio>
    <dl class="bgsoundsw">
        <dt></dt>
        <dd></dd>
    </dl>
	<script type="text/javascript" src="js/zepto.min.js"></script>
  	<script type="text/javascript" src="js/touch.js"></script>
  	<script type="text/javascript" src="js/activity.js?230506"></script>
    <script type="text/javascript">
        $(".receive_btn").on('click',function(){
            window.location.href="<?php echo url("Index","showPic");?>";
        })
    </script>
    <?php tpl('Index.footer');?>
</body>
</html>