<!DOCTYPE html>
<html>
<head>
    <title>祝福</title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
    <meta id="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1, user-scalable=no" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    
    <link rel="stylesheet" type="text/css" href="css/common.css">
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/wish.css?version=<?=VERSION?>">
	<script type="text/javascript" src="js/common.js?version=<?=VERSION?>"></script>
	<script type="text/javascript" src="js/ylMap.js?version=<?=VERSION?>"></script>
	<script type="text/javascript" src="js/load/load.js?version=<?=VERSION?>"></script>
	<link rel="stylesheet" type="text/css" href="js/load/load.css?version=<?=VERSION?>">
	<?php tpl('Index.header');?>	
</head>
<body  style="height: 100%">
<div  style="height: 100%" class="common_bg">
    <div class="tab">
        <table class="default_table">
        	<tr>
        		<th>序号</th>
        		<th>名称</th>
        		<th>手机号</th>
        		<th>操作</th>
        	</tr>
        	<?php foreach ($list as $v){?>
        	<tr>
        		<td><?php echo $v['id'];?></td>
        		<td><?php echo $v['cname'];?></td>
        		<td><?php echo $v['cphone'];?></td>
        		<td>
        		<?php if($v["type"]==2){?>
        		<a href="<?php echo url("Index","yaoqing",array("s"=>encryptParam(array(0=>$v['id'],1=>$v['cname']))));?>">查看专属</a>
        		<?php }?>
        		</td>
        	</tr>
        	<?php }?>
        </table>
        <div class="tab_foot"><?php echo $page;?></div>
    </div>
    <input type="button" id="registBtn" onclick="jump()" value="增加专属" class="button">
    <?php tpl('Index.base_bottom');?>
</div>
<?php tpl('Index.footer');?>

<script type="text/javascript">
var mapS;
$(function(){
	mapS = new ylmap.init;
})

function jump () {
	var jumpUrl = '<?php echo url("Index","admin_add")?>';
	window.top.location.href = jumpUrl;
}
</script>
</body>
</html>