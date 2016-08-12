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
    <div class="main">
        <h2>增加专属请贴人</h2>
        <ul>
            <li><span class="icon"><img src="images/icon1.png" width="20"　style="vertical-align: middle"></span><input type="text" id="cname" name="cname"  class="text_inp" placeholder="您的姓名" value="<?php echo @$info['cname'];?>"></li>
        	<li><span class="icon"><img src="images/icon2.png" width="20"　style="vertical-align: middle"></span><input type="text" id="cphone" name="cphone"  class="text_inp" placeholder="您的手机号" value="13800138000"></li>
        	
            <li>
            <input type="hidden" id="ccookieid" name="ccookieid" value="<?php echo @$info['ccookieid'];?>"/>
            
        </ul>
    </div>
    <div id="show_msg" style="text-align: center;margin-top: 10px;color: red"></div>
    <div class="footer">
        <input type="button" id="registBtn"  value="提交" class="button">
    </div>
    <?php tpl('Index.base_bottom');?>
</div>

<audio id="bgsound" src="images/music.m4a" autoplay="" loop=""></audio>
    <dl class="bgsoundsw">
        <dt></dt>
        <dd></dd>
    </dl>
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
    	var cwish = '';
    	var ctype = 1;
    	var ccount = 0;
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

		var opt_type = 'add';
		
    	var params = {
    	    	type : 2,
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
    			var jumpUrl = '<?php echo url("Index","allWish")?>';
    			window.top.location.href = jumpUrl;
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