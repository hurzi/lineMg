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
        <h2>送点祝福</h2>
        <ul>
            <li><span class="icon"><img src="images/icon1.png" width="20"　style="vertical-align: middle"></span><input type="text" id="cname" name="cname"  class="text_inp" placeholder="您的姓名" value="<?php echo @$info['cname'];?>"></li>
        	<li><span class="icon"><img src="images/icon2.png" width="20"　style="vertical-align: middle"></span><input type="text" id="cphone" name="cphone"  class="text_inp" placeholder="您的手机号" value="<?php echo @$info['cphone'];?>"></li>
        	<li class="fo_box" style="display: none;"><span class="icon"><img src="images/icon1.png" width="20"　style="vertical-align: middle"></span>是否到现场:<br>
                <select class="default_select mar_b" name="ctype" id="ctype" class="nav_select">
                    <option value="1" <?php if(@$info['ctype']==1){echo 'selected="selected"';};?> >来不了了</option>
                    <option value="2" <?php if(@$info['ctype']==2){echo 'selected="selected"';};?>>还是要来的</option>
                </select> 
                <select name="ccount" id="ccount" class="default_select mar_b" style="width: 120px">
                    <option value="1" <?php if(@$info['ccount']==1){echo 'selected="selected"';};?>>1</option>
                    <option value="2" <?php if(@$info['ccount']==2){echo 'selected="selected"';};?>>2</option>
                    <option value="3" <?php if(@$info['ccount']==3){echo 'selected="selected"';};?>>3</option>
                    <option value="4" <?php if(@$info['ccount']==4){echo 'selected="selected"';};?>>3个不够，我们全家出动</option>
                </select>
            </li>
            <li>
            <input type="hidden" id="ccookieid" name="ccookieid" value="<?php echo @$info['ccookieid'];?>"/>
            <textarea id="cwish" name="cwish" rows="4"  style="border: none;padding:2px;  width: 100%;  font-size: 14px;  vertical-align: middle;  color: #505050;  height: 60px;" placeholder="说点什么吧"><?php echo @$info['cwish'];?></textarea></li>
        </ul>
    </div>
    <div id="show_msg" style="text-align: center;margin-top: 10px;color: red"></div>
    <div class="footer">
        <input type="button" id="registBtn"  value="提交" class="button">
    </div>
    <?php tpl('Index.base_bottom');?>
</div>

<audio id="bgsound" src="images/music.mp3" autoplay="" loop=""></audio>
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