<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付样例-支付</title>
	<script type="text/javascript" src="../Common/js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{	
		var openid = $("#openid").val();
		var shopId = $("#shopId").val();
		var extId = $("#extId").val();
		var price = $("#price").val();

		var param = {
				openId : openid,
				shopId : shopId,
				extId : extId,
				price : price*100.0
		};
		var url = '<?php echo url("WxPay","ajax_addUnifiedOrder"); ?>';
		tip("正在下单中...");
		$.ajax( {  
			 url:url,  
			 data:param,  
			 type:'post',   
			 dataType:'json',  
			 success:function(result) {  
				if(result.error == 0 ){ 
					tip("下单成功，正在调起微信支付...");
					var jsApiParameters = result.data.jsApiParameters; 
					WeixinJSBridge.invoke(
							'getBrandWCPayRequest',
							eval('(' + jsApiParameters + ')'),
							function(res){
//								WeixinJSBridge.log(res.err_msg);
//								alert(res.err_code+res.err_desc+res.err_msg);
								tip("支付结束");								
							}
						);
			     }else{
			    	 tip("下单失败，请重试！");
			     }
			 },  
			error : function() {   
				tip("下单异常，请重试！");
			}  
		});
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}

	function moniPay()
	{
		var openid = $("#openid").val();
		var shopId = $("#shopId").val();
		var extId = $("#extId").val();
		var price = $("#price").val();
		var ywType = $("#ywType").val();
		var isNewUser = false;

		if(confirm("是否测试新用户买单")){
			isNewUser = true;
		}

		var param = {
				openid : openid,
				shopId : shopId,
				extId : extId,
				total_fee : price*100.0,
				isNewUser : isNewUser,
				ywType : ywType
		};
		var url = '<?php echo url("WxPay","moniNotify"); ?>';
		tip("正在模拟下单中...");
		$.ajax( {  
			 url:url,  
			 data:param,  
			 type:'post',   
			 dataType:'json',  
			 success:function(result) { 
				 if(result.error==0){ 
					tip("模拟支付成功");
				 }else{
					tip(result.msg);
				}
			 },  
			error : function() {   
				tip("下单异常，请重试！");
			}  
		});
	}

	function tip (mesg) {
		 $('#show_msg').html(mesg);
	}
	</script>
</head>
<body>
    <br/>
    <input type="hidden" id="openid" name="openid" value="<?php echo $openid;?>"/>
    <input type="hidden" id="extId" name="extId" value="<?php echo $extId;?>"/>
    <input type="hidden" id="shopId" name="shopId" value="<?php echo $shopId;?>"/>
    <input type="hidden" id="ywType" name="ywType" value="<?php echo $ywType;?>"/>
    <font color="#9ACD32"><b>输入支付金额(元):<br/></b></font>
    <input type="text" id="price" name="price" value="0.01" style="color:#f00;width:100%;font-size:50px;text-align: right;border:1px #e0e0e0 solid;" /><br/><br/>
	<div id="show_msg" style="text-align: center;margin-top: 10px;color: red"></div>    
	<div align="center">
		<button style="width:98%; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;margin-top: 15px;" type="button" onclick="callpay()" >立即真实支付</button><br/>
		<button style="width:98%; height:50px; border-radius: 15px;background-color:#a0a0a0; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;margin-top: 15px;" type="button" onclick="moniPay()" >模拟支付</button>
	</div>
</body>
</html>