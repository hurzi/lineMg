<!DOCTYPE html>
<html>
<head>
    <title>送点祝福</title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
    <meta id="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1, user-scalable=no" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    
    <link rel="stylesheet" type="text/css" href="css/common.css">
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/wish.css?version=<?=VERSION?>">
	<script type="text/javascript" src="js/common.js?version=<?=VERSION?>"></script>
	<script type="text/javascript" src="js/load/load.js?version=<?=VERSION?>"></script>
	<link rel="stylesheet" type="text/css" href="js/load/load.css?version=<?=VERSION?>">
	<?php tpl('Index.header');?>	
</head>
<body>
<div id="wrap" style="height: 100%"  class="common_bg">
    
</div>
<?php tpl('Index.footer');?>

<script type="text/javascript">
$(function(){
    $('.close').on('click',function(){
        $('.sysm').hide();
    });
    $('.tex1 a').on('click',function(){
        $('.sysm').show();
    })
    

    //发送注册请求
    $("#registBtn").click(function(){
    	var url = "<?php echo url("Index","ajax_insert");?>";
    	var cname = $("#cname").val();
    	var cphone = $("#cphone").val();
    	var cwish = $("#cwish").val();
    	var ctype = $("#ctype").val();
    	var ccount = $("#ccount").val();
    	var ccookieid = $("#ccookieid").val();
    	var reg_phone = /^1(\d{10})$/;
        var reg_code = /^\d{6}$/;
        if(!cname){
			tip('show_msg', "写个名字吧！");
			return;
		}
        if(!cphone){
			tip('show_msg', "留下个电话吧！");
			return;
		}
        if(!reg_phone.test(cphone)){
			tip('show_msg', "现在都流行手机啦!");
			return;
		}

		var opt_type = '<?php echo $opt_type;?>';
		
    	var params = {
    			cname : cname,
    			cphone : cphone,
    			cwish :cwish,
    			ctype : ctype,
    			ccount : ccount,
    			opt_type : opt_type,
    			ccookieid : ccookieid
    	};

    	tip('show_msg', '正在处理请求,请稍后...');

    	Common.ajax(url, params, function (result) {
    		try{
    			tip('show_msg', "您的祝福已收到");
    			//var jumpUrl = '<?php echo url("Index","home")?>';
    			//window.top.location.href = jumpUrl;
    		}catch(e){
    			tip('show_msg', result.msg);
    	 	}
    	},null,null,'divtip','show_msg');    	
    });
})

function tip (tipid, mesg) {
	 $('#'+tipid).html(mesg);
}
</script>
</body>
</html>