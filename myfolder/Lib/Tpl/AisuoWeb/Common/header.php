<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>爱锁</title>
  <link href="css/public.css?version=<?php echo AbcConfig::VERSION;?>" rel="stylesheet" type="text/css"/>
  <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="js/common.js?version=<?php  echo AbcConfig::VERSION;?>"></script>
   <script type="text/javascript">
	function ajax_submit(url,params,needTip,callbackfn){
		var url = arguments[0] || null;
		var needTip = arguments[2]==undefined ?true:arguments[2];
		var callbackfn = arguments[3] || null;
		if(!url){
			return ;
		}
		if(needTip){
			tip('show_msg', '正在处理请求,请稍后....');
		}
		
		$.post(url, params, function (result) {
			try{
		 		result = eval("(" + result + ")");
				if (result.error == 0) {
					if(typeof callbackfn == 'function'){
						callbackfn(result);
					}else{
						if(result.data.jumpUrl){
							location.href = result.data.jumpUrl;
							return;
						}else {			 		
							tip('show_msg', '操作成功');
				 		}
					}					
		 		}else{
		 			if(typeof callbackfn == 'function' && result.error<0){
		 				callbackfn(result);
		 			}else{
			 			tip('show_msg', result.msg);
			 			return false;
		 			}
		 	 	}
		 	}catch(e){
				tip('show_msg', '系统异常,请稍后提供...');
				//window.location.reload();
		 	}
		});
	}
  </script>