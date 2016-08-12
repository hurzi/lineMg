<?php tpl('Common.header')?>
<body>
<div id="wrap">
    <div class="header margin0">
        <img src="" width="80px" class="radiu_pic">
    </div>
    <div class="information-box white_bj orange-color-border-top">
        <div class="burst-main">
            <div class="bj_wra">
            <div class="but-co">
                <span class="i_text_min orange-color-size">手机号：</span>
                <span class="inp_box gra1_color_size">
                    <input type="text" id="user_phone" name="user_phone" class="gra1_color_size inp_box23" placeholder="请输入手机号">
                </span>
                <div class="clear"></div>
            </div>
            <div class="but-co">
                <span class="i_text_min orange-color-size">验证码：</span>
                <span class="inp_box gra1_color_size">
                    <input type="text" id="valide_code" name="valide_code" class="gra1_color_size inp_box23_min gra1_color_size" placeholder="请输入验证码">
                </span>
                <span class="btn_bx"><input type="button" value="获取验证码" class="hq_btn" onclick="sendCode(this)" ></span>
                <div class="clear"></div>
            </div>
            <div class="but-co">
                <span class="i_text_min orange-color-size">学号：</span>
                <span class="inp_box gra1_color_size">
                    <input type="text" id="user_number" name="user_number" class="gra1_color_size inp_box23" placeholder="请输入学号">
                </span>
                <div class="clear"></div>
            </div>
            <div class="but-co">
                <span class="i_text_min orange-color-size">真实姓名：</span>
                <span class="inp_box gra1_color_size">
                    <input type="text" id="user_name" name="user_name" class="gra1_color_size inp_box23" placeholder="请输入真实姓名">
                </span>
                <div class="clear"></div>
            </div>
            </div>
        </div>		
    </div>
    <div class="burst-main">
        <div class="button_wrap mar_top20">
            <button class="submit_btn orange_bj pad50 borde-rad white_color_size" id="register" name="register">提交</button>	
        </div>
    </div>	
	
</div>
</body>

<script type="text/javascript">
$(function(){
    $("#register").click(function(){
        var user_phone = $("#user_phone").val();
        var valide_code = $("#valide_code").val();
        var user_number = $("#user_number").val();
        var user_name = $("#user_name").val();
        var reg_phone = /^1(\d{10})$/;
        var reg_code = /^\d{6}$/;
        if (!user_phone || !reg_phone.test(user_phone)){
            alert('手机号码不能为空');
            return false;
        }
        if(!valide_code || !reg_code.test(valide_code)){
            alert("请输入正确的验证码");
            return false;
        }
        var params = {
        		user_phone:user_phone,
        		valide_code:valide_code,
        		user_number:user_number,
        		user_name:user_name,
                };
        
        var url = "<?=url('Index','ajax_addUser')?>";
        Common.request(url,params,function(data){
            window.location.href="<?=url('User','index')?>";
        });
    });
});	
var countdown=60; 

function settime(obj) { 
  	$(".hq_btn").addClass("cheng_bj");
	if (countdown == 0) { 
  	$(".hq_btn").removeClass("cheng_bj");
        obj.removeAttribute("disabled");
		obj.value="获取验证码"; 
		countdown = 60; 
		return;
	} else { 
		obj.setAttribute("disabled", true); 
		obj.value="重新发送(" + countdown + ")"; 
		countdown--; 
	} 
    setTimeout(function() { settime(obj); },1000);
}
function sendCode(obj){
    var url = "<?php echo url("User","ajax_sendVolideCode");?>";
    var user_phone = $("#user_phone").val();
    var reg_phone = /^1(\d{10})$/;
    if(!user_phone || !reg_phone.test(user_phone)){
        Common.alert("请输入正确的手机号.");
        return;
    }
    var params = {
    		user_phone :user_phone
    };
    Common.request(url,params,function(data){
        Common.alert("发送验证码成功，请查询短信.");
        settime(obj);
    },function(){
    	settime(obj);
        });
}
</script>
</html>