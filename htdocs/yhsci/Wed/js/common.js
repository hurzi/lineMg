
var Common;
(function(){
    //config    
//    Array.prototype.in_array = function(str){
//        return (this.join(',') + ',').indexOf(str+',') != -1;
//    };
    var common = function(){
        this.domain = document.domain;
        var url = location.href.split('?')[0].split('/');
        url.pop();        
        this.url = url.join('/');
        this.jsArr = [];
        this.lastError = '';
        this.submitBtn = true;
    };
    common.prototype.importjs = function(path){
        var file = this.getPath(path);
        this.load(file);
    };
    common.prototype.getPath = function(path){
        return this.url + '/js/' + path + '.js';
    };
    common.prototype.load = function(url){
        if(this.isLoad(url))return;
        $.ajax({
            url:url,            
            async:false,
            cache : true
        });
        this.jsArr.push(url);
    };
    common.prototype.isLoad = function(url){        
        var index = $.inArray(url, this.jsArr);
        return (-1 == index) ? false : true;
    }; 
    common.prototype.get = function(id){
        return document.getElementById(id);
    };
    common.prototype.alertSet = function(msg,id){
        this.get(id+'_err').innerHTML = msg;
        this.lastError = msg;
    };
    common.prototype.submitEnable = function(id,set){               
        if(set){
            return this.submitBtn = set;
        }
        var bl = this.submitBtn;
        this.submitBtn && (this.submitBtn = !this.submitBtn);        
        return bl;
    };
    common.prototype.alert = function(msg,callback){
        if('string' === typeof msg){  
        	if(window.top.$ && window.top.$.dialog){
        		window.top.$.dialog.alert(msg,callback);
        	}else if($.dialog){
        		$.dialog.alert(msg,callback);
        	}else {
        		if(!callback){
        			alert(msg);
        		}else{
        			alert("系统错误！");
        		}        		
        	}
            //window.top.$.dialog.alert(msg,callback);
        }else if('object' === typeof msg){            
            if(msg.data){
                try{
                    var fun = eval(msg.data);
                    if('function' === typeof fun){
                        fun.call(window);
                    }else{
                        this.alert(msg.msg);
                    }                                   
                }catch(e){
                    this.alert(msg.msg);
                }  
            }else{
                this.submitBtn = true;
                this.alert(msg.msg);
            }
        } 
    };
    common.prototype.goLogin = function(){
        var res = confirm('您还没有登陆，是否去登陆');
        if(res){
            location.href = './';
        }
    };
    /**
     * loadding的样式
     * @param type
     * @param loadtype
     */
    common.prototype.loading = function(type,loadtype,contentid){
    	if(loadtype == "none"){
    		return ;
    	}
    	switch (loadtype) {
		case "divtip":
			if(contentid){
				if(type == "on"){
					var html = "";
					html += '<div style="text-align:center;font-size:12px;padding:10px">';
					html += '正在加载中，请稍候...';
					html += '</div>';
					$("#"+contentid).html(html);
				}else{
					$("#"+contentid).html("");
				}				
				break;
    		}
		default:
			if(type == "on"){
	    		loadMack({off:'on'});
	    	}else{
	    		loadMack({off:'off'});
	    	}
			break;
		}    	
    };
    /**
     * 异步加载
     * @param url  url
     * @param param  参数
     * @param ok_callback  error==0时的回调函数
     * @param error_callback error!=0时的回调函数
     * @param type		post/get 默认post
     * @param loadtype  loadding样式
     */
    common.prototype.ajax = function(url,param,ok_callback,error_callback,type,loadtype,contentid){
        var self = this;
        type = type || 'post';
        //loadMack({off:'on'});    	
        self.loading("on",loadtype,contentid);
		$.ajax({
			url : url,
			type : type,
			data : param,
			dataType : 'json',
			beforeSend : function() {
			},
			complete : function() {
			},
			error : function() {
				//loadMack({off : 'off'});
				self.loading("off",loadtype);
				self.alert('系统异常,稍后再试!', function() {
					if (error_callback)
						error_callback.call(window, false, false);
					self.submitBtn = true;
				});
			},
			success : function(result) {
				loadMack({off : 'off'});
				if (result.error == '0') {
					if (ok_callback)
						ok_callback.call(window, result, true);
				} else {
					if (error_callback) {
						error_callback.call(null, result, false);
					} else {
						if(loadtype == "divtip" && contentid){
							var html = "";
							html += '<div style="text-align:center;font-size:12px;padding:10px">';
							html += result.msg;
							html += '</div>';
							$("#"+contentid).html(html);
						}else{
							self.alert(result.msg);
						}
						
					}

				}
			}
		});
    };
    common.prototype.confirm = function(msg,ok_callback,cancel_callback){
        $.dialog.confirm(msg,function(){
            if(ok_callback)ok_callback.call(window,true);
        },function(){
            if(cancel_callback)cancel_callback.call(window,false);
        });
    }
    Common = new common();  
})();
function ckplayer(id,media_url, param){
	param = param || {};
	var flashvars={
		f:media_url,
		c:0,
		b:1
	};
	if (param.p) flashvars.p = param.p;
	var w = param.w || '100%';
	var h = param.h || '100%';
	var params={bgcolor:'#000',allowFullScreen:param.allowFullScreen||false,allowScriptAccess:'always',wmode:'transparent',quality:'high'};
	var attributes={id:id,name:'ckplayer_a'+id};
	CKobject.embedSWF('./js/ckplayer/ckplayer.swf?v=2.11',id,'ckplayer_a'+id,w,h,flashvars,params,attributes);
}

function SuiShibind(fn, selfObj, var_args) {
	if (!fn) {
		throw new Error();
	}
	if (arguments.length > 2) {
		var boundArgs = Array.prototype.slice.call(arguments, 2);
		return function() {
			var newArgs = Array.prototype.slice.call(arguments);
			Array.prototype.unshift.apply(newArgs, boundArgs);
			return fn.apply(selfObj, newArgs);
		};
	} else {
		return function() {
			return fn.apply(selfObj, arguments);
		};
	}
}

Date.prototype.Format = function(fmt) {
		var o = {
			"M+" : this.getMonth() + 1, // 月份
			"d+" : this.getDate(), // 日
			"h+" : this.getHours(), // 小时
			"m+" : this.getMinutes(), // 分
			"s+" : this.getSeconds(), // 秒
			"q+" : Math.floor((this.getMonth() + 3) / 3), // 季度
			"S" : this.getMilliseconds()
		// 毫秒
		};
		if (/(y+)/.test(fmt))
			fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "")
					.substr(4 - RegExp.$1.length));
		for ( var k in o)
			if (new RegExp("(" + k + ")").test(fmt))
				fmt = fmt.replace(RegExp.$1, 
						(RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;
};
	
//音频处理
function JPlayer(id, url) {
	$("#jquery_jplayer_" + id).jPlayer({
	ready : function() {
	$(this).jPlayer("setMedia", {
	mp3 : url
	}).jPlayer("play");
	},
	cssSelectorAncestor : "#jp_container_" + id,
	swfPath : "./js/jplayer/",
	supplied : "mp3",
	wmode : "window"
	});
}
/**
* 取得字符串的长度，中文字符：若是UTF-8表示三个字节，GBK或GB2312表示2个字节
* @param str 传入的字符串
* @param charset 默认 UTF-8
* @returns
*/
function getStrLength(str, charset) {
	charset = charset || 'UTF-8';
	var cnWordLen = charset.toUpperCase() == 'UTF-8' ? 3 : 2;
	var i, sum;
	sum = 0;
	for (i = 0; i < str.length; i++) {
		if ((str.charCodeAt(i) >= 0) && (str.charCodeAt(i) <= 255)) {
			sum = sum + 1;
		} else {
			sum = sum + cnWordLen;
		}
	}
	return sum;
}
/**
 * 检测是否为空
 * @param string
 * @returns {Boolean}
 */
function isEmpty(string) {
	if (null == string || '' == string || false == string) {
		return true;
	}
	return false;
}
/**
 * 检测是否为url
 * @param string
 * @returns {Boolean}
 */
function isUrl(str_url){
	var strRegex = "^[A-Za-z]+://[A-Za-z0-9-_.]+";
	var re=new RegExp(strRegex);
	if (re.test(str_url)){
		return true;
	}else{
		return false;
	}
}