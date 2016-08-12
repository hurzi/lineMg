
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

/**
 * 加载js文件
 * @param url  js文件路径
 * @param callback  加载成功后的callback
 */
function loadScript(url, callback) {
	var script = document.createElement("script");
	script.type = "text/javascript";
	if (script.readyState) { // IE
		script.onreadystatechange = function() {
			if (script.readyState == "loaded"
					|| script.readyState == "complete") {
				script.onreadystatechange = null;
				if(typeof(callback) == "function"){
					callback.call(null,null);
				}
			}
		};
	} else { // Others
		script.onload = function() {
			if(typeof(callback) == "function"){
				callback.call(null,null);
			}
		};
	}
	script.src = url;
	document.getElementsByTagName("head")[0].appendChild(script);
}

/**
 * 是否是数组
 * @param obj
 * @returns
 */
function isArray(obj){
	return Object.prototype.toString.call(obj) === '[object Array]';
}

/**
 * 深度复制对象
 */
function deepCopyObj(source) { 
	var result={};
	for (var key in source) {
		if(source[key] == null){
			result[key] = null;
		}else{
			result[key] = (typeof source[key]==='object')? deepCopyObj(source[key]): source[key];
		}
	} 
	return result; 
}

/**
 * 获取url中的参数
 * @param name
 * @returns
 */
function getUrlParam(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}


/**
 * 是否在数组内
 * @param needle
 * @param haystack
 * @param argStrict
 * @returns {Boolean}
 */
function in_array(needle, haystack, argStrict) {
    var key = '', strict = !! argStrict;
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }
    return false;
}


/**
 * 向父级上报高度
 */
function parentSH(h){
	return;  //不用iframe结构，不用上报主框高度
	  h = parseInt(h) || 0; 
	  var hh = $('body').height()+h+10;
	  window.top.conIfH(hh);
}

/**
 * 批量删除
 * @param url
 * @param name  ids name名称
 * @param href
 * @returns
 */
function removeMany(url, name, href,title) {
	name = name || 'items';
	title = title || '';
	var ids = [];
	$("input[name='"+name+"']:checkbox:checked").each(function (){
		ids.push($(this).val());
	});
	if (ids.length <= 0) {
		Common.alert('请选择删除项!');
		return false;
	}
	remove(url, ids, href,title);
}

function removeOne(url, id, href){
	remove(url, id, href);
}

function remove(url, ids, href,title){
	title = title || '';
	Common.confirm('你确定要删除吗？'+title, function (){
		var params = {
			ids:ids
		};
		Common.request(url,  params, function(){
			Common.alert("删除成功",function(){
				if (href) {
					window.location.href = href;
				} else {
					window.location.reload();
				}
			});			
		});
	});
}

//音频处理
function JPlayer(id, url) {
	$("#jquery_jplayer_" + id).jPlayer({
		ready : function() {
			$(this).jPlayer("setMedia", {
				mp3 : url
			}).jPlayer("play");
		},
		cssSelectorAncestor : "#jp_container_" + id,
		swfPath : "./Admin/Public_1/js",
		supplied : "mp3",
		wmode : "window"
	});
}
//坐标地图
function logAndLatMap () {
	var content = '<div class="hui"></div><iframe src="http://api.map.baidu.com/lbsapi/getpoint/index.html" '
		+ 'style="width:975px;height:600px;" frameborder="0"></iframe><div style="height:5px;"></div>';
	new window.top.jsbox({
		onlyid: 'logAndLatMap',	
		title:'获取经纬度坐标',
		content:content,
		conw:960,
		mack:true
	}).show();
}

/** *****************************js 通用方法********************************** */
//把字符串首字母转为大写并返回
function wordFirstUpper(str) {
	var len = str.length;
	var tmp = '';
	for ( var i = 0; i < len; i++) {
		if (i == 0) {
			tmp += str[i].toUpperCase();
		} else {
			tmp += str[i];
		}
	}
	return tmp;
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

function showMessage(msg_type, data, div_id) {
	if (div_id) {
		new ShowSendData().setData(msg_type, data).render(div_id);
	} else {
		new window.top.ShowSendData().setData(msg_type, data).show();
	}
}

function parseUrl(url){
	url = url || location.href;
	var param_str = url.substring(url.indexOf('?')+1);
	var param_arr = param_str.split('&');
	var param = [];
	for(var i=0;i<param_arr.length;i++){
		var param_arr2 = param_arr[i].split('=');
		param[param_arr2[0]] = decodeURIComponent(param_arr2[1]);

	}
	return param;
}

function getMonitorPVCode(ent_id,point_id){
	var tpl =	'&lt;script&gt;' +
					'var __ABC_MONITOR_PARAM__ = {' +
					'fn:\' __ABC_MONITOR__.original\',' +
					'ent_id: '+ent_id+',' +
					'point4_id:' + point_id +
					'}' +
				'&lt;/script&gt;' +
				'&lt;script src="http://wx.hysci.com.cn/yhsci/Common/js/monitorPV.js"&gt;&lt;/script&gt;';
	return tpl;
}


Abcbind = function(fn, selfObj, var_args) {
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
};

