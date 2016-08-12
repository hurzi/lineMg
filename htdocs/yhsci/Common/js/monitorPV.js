var __SUISHI_MONITOR__ = {
		pv_url:['http://tracker.91adv.com/tk/wxpv.php', 'http://219.232.248.245/web/pv'],
		click_url:'http://tracker.91adv.com/tk/wxclick.php',
		url_id:'__suishi_click_id__',
		param_name:'monitor_data',
		error_log_path:'http://wx.hysci.com.cn/yhsci/DataFetcher/monitor_error.php',
		pv4_param: 'ENTID;;;POINTID;;;',
		textParamNum: 9,
		data_index:{
			//entID;campaignID;CreativeID;posID;sourceMediaID; sourcePosID;visitID;t=targetURL
			entID:0,campaignID:1,CreativeID:2,posID:3,sourceMediaID:4,sourcePosID:5,visitID:6,
			creative3_id:7,point3_id:8, creative4_id:9
		},
		//解析url获取monitor所需参数
		parseUrl:function(param_name, url){
			url = url || location.href;
			var param_str = url.substring(url.indexOf('?')+1);
			var param = [];
			if(param_str == url){
				return param_name ? '' : param;
			}
			var param_arr = param_str.split('&');
			for(var i=0;i<param_arr.length;i++){
				var tmp_arr = param_arr[i].split('=');
				param[tmp_arr[0]] = tmp_arr[1];
			}
			if (param_name) {
				return param[param_name];//string
			}
			return param;//[]
		},
		//getcookie
		getCookie:function(){
			var cookieId = null;
			var arr = document.cookie.match(new RegExp("(^| )wxVisitId=([^;]*)(;|$)"));
     			if(arr != null) {
				cookieId = unescape(arr[2]); 
			}
//			cookieId = "";
			if(cookieId == null || !cookieId){
				cookieId = Date.parse(new Date())+""+parseInt(Math.random()*(9999-1001+1)+1001);
				var Days = 9000; //此 cookie 将被保存 30 天
    				var exp  = new Date();    //new Date("December 31, 9998");
    				exp.setTime(exp.getTime() + Days*24*60*60*1000);
    				document.cookie = "wxVisitId="+ escape (cookieId) + ";expires=" + exp.toGMTString();
			}
			if(cookieId == null || cookieId == "undefined"){
				cookieId = "";
			}
			return cookieId;
		},
		getTitle:function(){
			var title=document.title;
			return title;
		},
		text:function(){//正文pv code 和原文click
			try{
				var param = this.parseUrl(this.param_name);
				if (!param) return;
				param = param.split(';');
				if(!param || param.length<this.textParamNum) return;
				var point_2_pv_str = param.slice(0,7).join(';');
				if(param[this.data_index.visitID] == ""){
					param[this.data_index.visitID] = this.getCookie();//getcookieid
				}
                                var point_2_pv_str_adsit = param.slice(0,7).join(';');
				var creative3_id = param[this.data_index.creative3_id];
				var point3_id = param[this.data_index.point3_id];
				var open_id = param[this.data_index.sourcePosID];
				var point4_data = param.slice(0,7);
				point4_data[this.data_index.CreativeID] = param[this.data_index.creative4_id];
				point4_data = point4_data.join(';')
				//pv请求
				for (var i = 0, len = this.pv_url.length; i < len; i++) {
					var img = new Image();
					//img.src = this.pv_url[i] + '?' + point_2_pv_str;
					if(i == 1){
						img.src = this.pv_url[i] + '?' + point_2_pv_str_adsit;
					}else{
						img.src = this.pv_url[i] + '?' + point_2_pv_str;
					}
					img.style.height=0;img.style.width =0;
					document.body.appendChild(img);
				}
				//全文链接处理
				var obj = document.getElementById(this.url_id);
				if(!obj || !point3_id) return;
				var href = obj.href;
				href = href + (href.indexOf('?')!=-1?'&':'?')+this.param_name+'='+point4_data;
				var point_3_data = param.slice(0,-2);
				point_3_data[this.data_index.CreativeID] = creative3_id;
				point_3_data[this.data_index.posID] = point3_id;
				var point_3_str = point_3_data.join(';');
				var param_click_url = '';
				param_click_url =this.click_url + '?' + point_3_str + ';t=' + href;
				obj.setAttribute('href',param_click_url);
			}catch(e){
				this.error_log(param, e.message);
			}
	},
	original: function () {//原文pv code
		try {
			if (!__SUISHI_MONITOR_PARAM__['point4_id'] || !__SUISHI_MONITOR_PARAM__['ent_id'])return;
			var point4_id = __SUISHI_MONITOR_PARAM__['pont4_id'];
			var ent_id = __SUISHI_MONITOR_PARAM__['ent_id'];
			var param = this.parseUrl(this.param_name);
			if (param) {
				param = param.split(';');
			}
			param = param || [];
			var point_4_data = [ent_id,
			                    param[this.data_index.campaignID] || '',
			                    param[this.data_index.CreativeID] || '',
			                    __SUISHI_MONITOR_PARAM__['point4_id'],
			                    param[this.data_index.sourceMediaID] || '',
			                    param[this.data_index.sourcePosID] || '',
			                    this.getCookie() || ''];
			var param_pv4 = point_4_data.join(';');
			//pv请求
			for (var i = 0, len = this.pv_url.length; i < len; i++) {
				var img = new Image();
				img.src = this.pv_url[i] + '?' + param_pv4;
				img.style.height=0;img.style.width =0;
				document.body.appendChild(img);
			}
		}catch(e){
			this.error_log(param, e.message);
		}
	},
	addOpenidToOriginal: function () {
		try {
			var openid = this.parseUrl('openid');
			//全文链接位置
			var obj = document.getElementById(this.url_id);
			if(!openid || !obj) return;
			var href = obj.href;
			href = href + (href.indexOf('?')!=-1?'&':'?') + 'openid=' + encodeURIComponent(openid);
			obj.setAttribute('href',href);
		}catch(e){this.error_log([openid], e.message);}
	},
	error_log: function(param, message) {
		try{
			if(!this.error_log_path) return;
			var img = new Image();
			img.src = this.error_log_path +'?param='+ encodeURIComponent(param.join(';'))+'&error='+encodeURIComponent(message);
			img.style.height=0;
			img.style.width =0;
			document.body.appendChild(img);
		}catch(e){}
	}
};
(function () {
	try {
		__SUISHI_MONITOR__.addOpenidToOriginal();
		if (typeof(__SUISHI_MONITOR_PARAM__) == 'undefined' || !__SUISHI_MONITOR_PARAM__) return;
		var fn = __SUISHI_MONITOR_PARAM__['fn'];
		if (!fn) return;
		eval(fn).call(__SUISHI_MONITOR__);
	}catch(e){}
})();
