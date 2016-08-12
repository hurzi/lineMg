<!DOCTYPE html>
<html>
<head>
    <title>邀请函</title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
    <meta id="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1, user-scalable=no" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    
    <link rel="stylesheet" type="text/css" href="css/common.css">
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/wish.css?version=<?=VERSION?>">
	<link rel="stylesheet" type="text/css" href="js/load/load.css?version=<?=VERSION?>">
	<?php tpl('Index.header');?>	
</head>
<body  style="height: 100%">
<div  style="height: 100%" class="yaoqing_bg">
    <div class="yaoqing_people">
        <div>
        <span class="to_span">TO:</span>
        <span class="name_span"><?php echo $name?></span>
        </div>
    </div>
    
    <audio id="bgsound" src="images/music.mp3" autoplay="" loop=""></audio>
    <dl class="bgsoundsw">
        <dt></dt>
        <dd></dd>
    </dl>
    <?php tpl('Index.base_bottom');?>
</div>
<?php tpl('Index.footer');?>

<script type="text/javascript">

function tip (tipid, mesg) {
	 $('#'+tipid).html(mesg);
}
</script>
</body>
</html>