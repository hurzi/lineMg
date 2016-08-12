/**
 * 与微信相关js，含以下相关内容 (1)微信jsSDK基础 (2)jsAPI调起卡券,jsSDK调起卡券 (3)自定义code回收
 * (4)debug功能，可限ip做debug (5)数据中心的监测服务 (6)微信分享配置
 * 
 * @param params
 * @returns {SuiShi_WxTool}
 */
function SuiShi_WxTool(params) {
	this.param = {
		"debug" : false, // 是否调试
		"debugLimitIp" : [], // 仅限设置的IP才能进行debug输出,不填代理不限制.(目的是在正式环境下也能调试错误)119.57.165.19为北京办公外网
		"debugCurrIp" : "", // 当前IP，需要通过参数传进来，与debugLimitIP配合使用，都设置了才有效。否则不做检验
		"useJsAPI" : false, // 用jsAPI调起微信卡券
		"useJsSDK" : false, // 用jsJDK调起微信卡券
		"jsSDKConfReady" : false, // 是否注册配置jsSDK成功
		"jsCardSignUrl" : '', // 用js调起微信卡券里的签名url(如果为jsSDK，则加isJsSDK=true参数)
		"recoverCodeUrl" : '', // 回收自定义code的url
		"succCardComponectFn" : function() {// 初始加载卡券组件后完成的调 起函数
		},
		"jsSDKReadyStack" : [], // jssdk加载完成后调用的函数列表，类似jQuery.ready
		"jsSDKConf" : { // jsSDK的配置(如需要jsSDK，则必须配置以下四个参数)
			"debug" : false, // 是调试
			"appId" : '', // appid,微信公众账号appid，jsSDK权限验证必须参数之一
			"timestamp" : '', // 时间撮，jsSDK权限验证必须参数之一
			"nonceStr" : '', // 随视字符串,jsSDK权限验证必须参数之一
			"signature" : '', // 签名值 jsSDK权限验证必须参数之一
			"jsApiList" : [ 'onMenuShareTimeline', 'onMenuShareAppMessage',
					'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone',
					'hideOptionMenu', 'showOptionMenu', 'showMenuItems',
					'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem',
					'scanQRCode', 'addCard' ]
		},
		"monitorConf" : { // 数据部门监测服务的配置
			"userId" : "zequanguanggao", // [固定]在数据部门的唯一账号ID,在一个系统中是固定的不需要改变
			"jsUrl" : "http://tk.socialjia.com/ss.js", // [固定]数据中心js文件地址
			"activityId" : "", // [一次设置]从数据中心获取的活动ID,必填，如果不填则不会使用抛送测试
			"openid" : "", // [变量]当前页的openid，必填，如果不填则不会使用抛送测试
			"ename" : "", // [变量]当前页面的事件名称
			"initExecPv" : true
		// 是否初始化时就执行一次当前页面的pv监测
		},
		"shareConf" : { // 分享组件配置
			"shareStatus" : "showAll", // 右上角的状态显示("showAll":显示默认的所有,"hideAll":隐藏所有组件,"showNoBase":显示所有非基础按钮接口,"hideNoBase":隐藏所有非基础按钮接口)
			"imgUrl" : "", // 所有分享选项(朋友/朋友圈/qq/微信)通用的分享图片,当有各自的分享图片时优先使用自己的
			"title" : "", // 所有分享选项(朋友/朋友圈/qq/微信)通用的分享标题,当有各自的分享标题时优先使用自己的
			"desc" : "", // 所有分享选项(朋友/朋友圈/qq/微信)通用的分享描述,当有各自的分享描述时优先使用自己的
			"link" : "", // 所有分享选项(朋友/朋友圈/qq/微信)通用的分享链接,当有各自的分享链接时优先使用自己的
			"timelineShare" : {}, // 分享朋友圈的个性个显示项，和上面的imgUrl,title,desc,link意思一样
			"friendShare" : {}, // 分享朋友的个性个显示项，和上面的imgUrl,title,desc,link意思一样
			"qqShare" : {}, // 分享qq的个性个显示项，和上面的imgUrl,title,desc,link意思一样
			"weiboShare" : {}, // 分享微信的个性个显示项，和上面的imgUrl,title,desc,link意思一样
			"qzoneShare" : {}, // 分享Qzone的个性个显示项，和上面的imgUrl,title,desc,link意思一样
			"callback" : function() {
			}, // 分享回调 ，按微信标准的回调返回 {"errMsg":"onMenuShareAppMessage:cancel"}
		}
	};
	try {
		this._initParam(params); // 初始化参数
		//如果url中有_debug_参数且为真则自动填充debug参数
		if(getUrlParam("_debug_")){
			this._log("－－－－－－－－url参数_debug_强制设置为debug模式－－－－－－－－－－－");
			this.param['debug'] = Boolean(getUrlParam("_debug_"));
		}
		this._log("开始初始化微信jstool-----");
		// 检查微信jsAPI组件
		// document.addEventListener('WeixinJSBridgeReady', this._jsapiCallback,
		// false);
		document.addEventListener('WeixinJSBridgeReady', SuiShibind(
				this._jsapiCallback, this), false);
		// 初始倾吐微信jsSDK组件(如果有设置了分享参数，会执行分享配置)
		this._initJsSDK(); // 检查页面是否有jsSDK的调起权限

		// 是否初始执行一次页面pv监测
		if (this.param['monitorConf']['initExecPv'] == true) {
			this.monitor("pageview");
		}
		window.onerror=SuiShibind(
				this._windowError, this);
		//初始化loading
		this.loading();
		this._log("初始化结束,参数:", this.param);
	} catch (e) {
		this._log("创建SuiShiWxTool失败报错了，错误信息："+e.message,e);
	}	
};

/**
 * 初始化jsAPI的callback
 */
SuiShi_WxTool.prototype._jsapiCallback = function() {
	var _this = this;
	_this.param['useJsAPI'] = true;
	_this._log("WeixinJSBridge初始化成功");
	_this._initGetCard();
};

/**
 * 初始化是否jsSDK的配置等权限是不是有
 */
SuiShi_WxTool.prototype._initJsSDK = function() {
	var _this = this;
	if (typeof (wx) == 'undefined') {
		_this._log("当前页面没有wx对象，不参进行jsSDK相关操作");
		return;
	}
	var appId = _this.param['jsSDKConf']['appId'];
	var timestamp = _this.param['jsSDKConf']['timestamp'];
	var nonceStr = _this.param['jsSDKConf']['nonceStr'];
	var signature = _this.param['jsSDKConf']['signature'];
	var jsApiList = _this.param['jsSDKConf']['jsApiList'];
	if (!appId || !timestamp || !nonceStr || !signature) {
		_this._log("JSSDK的参数配置不正确，不能进行jsSDK相关操作，"
				+ "appId/timestamp/nonceStr/signature:[" + appId + "/"
				+ timestamp + "/" + nonceStr + "/" + signature);
		return;
	}
	// jsSDK配置
	wx.config({
		debug : _this.param['jsSDKConf']['debug'], // debug模式
		appId : appId,
		timestamp : timestamp,
		nonceStr : nonceStr,
		signature : signature,
		jsApiList : jsApiList
	});
	// 检测失败
	wx.error(function(res) {
		_this.param['useJsSDK'] = false;
		_this.param['jsSDKConfReady'] = false;
		_this._log("JSSDK配置校验失败，失败信息：", res);
		return;
	});
	// 测试成功
	wx.ready(function() {
		_this.param['jsSDKConfReady'] = true;
		_this._log("JSSDK配置检测结束");
		wx.checkJsApi({
			jsApiList : [ 'addCard' ],
			success : function(res) {
				if (res.checkResult.addCard === true) {
					_this.param['useJsSDK'] = true;
					_this._initGetCard();
				}
			}
		});
		// 初始化分享
		_this._initShare();
		//　执行jssdkReady函数
		_this.execJsSDKReadyFn();		
	});
};

/**
 * 初始化分享相关配置
 */
SuiShi_WxTool.prototype._initShare = function() {
	var _this = this;
	var baseConf = {
		"imgUrl" : this.param['shareConf']['imgUrl'],
		"title" : this.param['shareConf']['title'],
		"desc" : this.param['shareConf']['desc'],
		"link" : this.param['shareConf']['link']
	};
	var timelineShare = this._mergeParam(deepCopyObj(baseConf),
			this.param['shareConf']['timelineShare']);
	var friendShare = this._mergeParam(deepCopyObj(baseConf),
			this.param['shareConf']['friendShare']);
	var qqShare = this._mergeParam(deepCopyObj(baseConf), this.param['shareConf']['qqShare']);
	var weiboShare = this._mergeParam(deepCopyObj(baseConf),
			this.param['shareConf']['weiboShare']);
	var qzoneShare = this._mergeParam(deepCopyObj(baseConf),
			this.param['shareConf']['qzoneShare']);
	var callback = this.param['shareConf']['callback'];
	// 设置右上角
	var menuFn = function(methodName, showName) {
		// 检测权限后执行
		wx.checkJsApi({
			jsApiList : [ methodName ],
			success : function(res) {
				if (res.checkResult[methodName] === true) {
					wx[methodName]();
					_this._log("[右上角菜单]设置微信" + showName + "配置成功，配置参数", res);
				} else {
					_this._log("[右上角菜单]检测JSSDK的菜单功能[" + methodName
							+ "]函数结果没有权限,检测结果如下：", res);
				}
			},
			error : function(res) {
				_this._log("[右上角菜单]检测JSSDK的[" + methodName
						+ "]函数是否有权限出错,检测结果如下：", res);
			}
		});
	};
	// 设置分享处理
	var shareFn = function() {
		_this.setShareConf("friend", friendShare, callback);
		_this.setShareConf("timeline", timelineShare, callback);
		_this.setShareConf("qq", qqShare, callback);
		_this.setShareConf("weibo", weiboShare, callback);
		_this.setShareConf("qzone", qzoneShare, callback);
	};
	// 设置右上角分享
	switch (this.param['shareConf']['shareStatus']) {
	case "showAll":
		menuFn('showOptionMenu', "显示全部菜单");
		shareFn();
		break;
	case "hideAll":
		menuFn('hideOptionMenu', "隐藏全部菜单");
		break;
	case "showNoBase":
		menuFn('showAllNonBaseMenuItem', "显示非基础菜单");
		shareFn();
		break;
	case "hideNoBase":
		menuFn('hideAllNonBaseMenuItem', "隐藏非基础菜单");
		break;
	}
};


/**
 * 调用微信SDK的相关方法
 * @param methodName 	类型：微信jsSDK支持的方法名
 * @param params  方法配置，没有填空
 * @param	callback  方法调用的回调函数
 * 	
 */
SuiShi_WxTool.prototype.method = function(methodName, params, callBack) {	
	try {
		var _this = this;
		if (_this.param['useJsSDK'] == false) {
			_this._setError("20003", "卡券组件包jssdk还没准备好");
			_this._callbackFunction(callBack, _this._makeRsult("", "－1", "卡券组件包还没准备好"));
			return;
		}
		_this.loading("on","正在处理中...");
		params = params || [];
		params['trigger'] = params['trigger'] || function(res){
			_this._log("jsSDK触发了"+methodName+"事件,参数", params);
		};
		params['success'] = params['trigger'] || function(res){
			_this._log("jsSDK调用"+methodName+"方法成功事件,返回结果", res);
			_this._callbackFunction(callBack, _this
					._makeRsult(res,0,''));
		};
		params['cancel'] = params['cancel'] || function(res){
			_this._log("用户取消了jsSDKb的"+methodName+",参数", params);
			_this._callbackFunction(callBack, _this
					._makeRsult(res,1,'用户取消了操作'));
		};
		params['fail'] = params['fail'] || function(res){
			_this._log("jsSDKb调用"+methodName+"失败,失败原因:", res);
			_this._callbackFunction(callBack, _this
					._makeRsult(res,2,'操作失败'));
		};
		
		//缓存中是否有jssdk的权限检测
		var authorArr = _this.param["_jsSDKAuthority_"] || [];
		var mothorNameAuthor = authorArr[methodName];
		if(mothorNameAuthor){
			//调用实际的jssdk方法
			wx[methodName](params);
		}else{
			// 检测权限后再调用
			wx.checkJsApi({
				jsApiList : [ methodName ],
				success : function(res) {
					_this.loading("off");
					if (res.checkResult[methodName] === true) {
						//检测过的缓存起来，同一页面不需要再处理
						authorArr[methodName] = true;
						_this.param["_jsSDKAuthority_"] = authorArr;
						//调用实际的jssdk方法
						wx[methodName](params);	
					}else{
						_this._log("检测JSSDK的[" + methodName
								+ "]函数检查不过，没有该方法的权限", res);
						_this._callbackFunction(callBack, _this
								._makeRsult("", "20010", "js检查接口权限调用失败"));
					}
				},
				error : function(res) {
					_this.loading("off");
					_this._log("检测JSSDK的[" + methodName
							+ "]函数是否有权限出错,检测结果如下：", res);
					_this._callbackFunction(callBack, _this
							._makeRsult("", "20011", "js检查接口权限调用失败"));
				}
			});
		}	
	} catch (e) {
		this._log("调用微信SDK["+methodName+"]出现js错误："+e.message,e);
	}
};

/**
 * 设置分享配置(可对外)
 * @param type 	类型：friend/timeline/qq/weibo/qzone
 * @param conf  分享参数配置
 * 		{
 * 			"imgUrl" : "", // 分享图片
 * 			"title" : "", // 分享标题
 * 			"desc" : "", // 分享描述
 * 			"link" : "", // 分享链接
 * 		}			
 * @param	callback  分享的回调函数
 * 	
 */
SuiShi_WxTool.prototype.setShareConf = function(type, conf, callback) {
	var _this = this;
	if (_this.param['jsSDKConfReady'] == false) {
		this._log("设置分享配置出错，jsSDK检测失败了");
		return;
	}
	if (type != "friend" && type != "timeline" && type != "qq"
			&& type != "weibo" && type != "qzone") {
		this._log("设置分享配置出错，配置参数不合法，类型[" + type
				+ "]必须为[friend/timeline/qq/weibo/qzone]之有", conf);
		return;
	}
	// if(!conf || !conf.imgUrl || !conf.title ||!conf.desc ||!conf.link){
	// this._log("设置分享配置出错，配置参数不合法，类型["+type+"],参数：",conf);
	// return;
	// }
	var imgUrl = conf.imgUrl;
	var title = conf.title;
	var desc = conf.desc;
	var link = conf.link;
	// 事件处理
	var fn = function(methodName, monitorType, showName) {
		// 检测权限
		wx.checkJsApi({
			jsApiList : [ methodName ],
			success : function(res) {
				if (res.checkResult[methodName] === true) {
					wx[methodName]({
						title : title,
						desc : desc,
						link : link,
						imgUrl : imgUrl,
						trigger : function(res) {
							_this._log("用户点击了分享到" + showName, res);
							//_this.monitor(monitorType);
							_this._callbackFunction(callback, res);
						},
						success : function(res) {
							_this._log("用户分享到" + showName + "成功,类型："+ monitorType, res);
							_this.monitor(monitorType);
							_this._callbackFunction(callback, res);
						},
						cancel : function(res) {
							_this._log("用户取消了分享到" + showName, res);
							_this._callbackFunction(callback, res);
						},
						fail : function(res) {
							_this._log("用户分享到" + showName + "失败");
							_this._callbackFunction(callback, res);
						}
					});
					_this._log("设置微信分享给" + showName + "配置成功，配置参数", conf);
				} else {
					_this._log("检测JSSDK的分享[" + methodName
							+ "]函数是否有权限不通过,检测结果如下：", res);
				}
			},
			error : function(res) {
				_this._log("检测JSSDK的[" + methodName
						+ "]函数是否有权限出错,检测结果如下：", res);
			}
		});

	};
	// 设置朋友分享
	if (type == "all" || type == "friend") {
		// 2.1 监听“分享给朋友”，按钮点击、自定义分享内容及分享结果接口
		fn("onMenuShareAppMessage", "friend", "朋友");
	}
	// 设置朋友圈分享
	if (type == "all" || type == "timeline") {
		fn("onMenuShareTimeline", "timeline", "朋友圈");
	}
	// 设置qq分享
	if (type == "all" || type == "qq") {
		fn("onMenuShareQQ", "qq", "qq");
	}
	// 设置微博分享
	if (type == "all" || type == "weibo") {
		fn("onMenuShareWeibo", "qqweibo", "腾讯微信");
	}
	// 设置qQzone
	if (type == "all" || type == "qzone") {
		fn("onMenuShareWeibo", "qq", "QQ空间");
	}
};

/**
 * 默认页面加载就调起卡券
 */
SuiShi_WxTool.prototype._initGetCard = function() {
	if (this.isInitGetCard == true) {
		this._log("已经有另外的组件正在调用card组件准备结束事件，不需要再调 用了");
		return;
	}
	this.isInitGetCard = true;
	this._callbackFunction(this.param['succCardComponectFn'], this.param);
};

/**
 * 初始化参数
 * 
 * @param param
 * @returns {Boolean}
 */
SuiShi_WxTool.prototype._initParam = function(param) {
	if (typeof (param) != "object") {
		return;
	}
	for ( var p in param) { // 循环设置初始化值
		// this.param[p] = param[p];
		if (typeof (param[p]) == "object") {
			this.param[p] = this._mergeParam(this.param[p], param[p]);
		} else {
			this.param[p] = param[p];
		}
	}
	return true;
};

/**
 * 全并参数值，把新值覆盖旧值
 * 
 * @param oldParam
 * @param newParam
 * @returns
 */
SuiShi_WxTool.prototype._mergeParam = function(oldParam, newParam) {
	if (typeof (newParam) != "object") {
		return oldParam;
	}
	for ( var p in newParam) { // 循环设置初始化值
		if (typeof (newParam[p]) == "object") {
			oldParam[p] = this._mergeParam(oldParam[p], newParam[p]);
		} else {		
			oldParam[p] = newParam[p];
		}
	}
	return oldParam;
};

/**
 * jsSDK加载完成自动执行函数配置
 * 
 * @param fn jsSDK加载完成自动执行的函数
 */
SuiShi_WxTool.prototype.jsSDKReady = function(fn) {
	if (Object.prototype.toString.call(fn) === '[object Function]') {
		this.param['jsSDKReadyStack'].push(fn);
    }
	this.execJsSDKReadyFn();
};

/**
 * jsSDK加载完成自动执行函数
 * 
 * @param fn jsSDK加载完成自动执行的函数
 */
SuiShi_WxTool.prototype.execJsSDKReadyFn = function() {
	if(this.param['jsSDKConfReady']){
		for (var i = 0; i < this.param['jsSDKReadyStack'].length; i++) {
			this.param['jsSDKReadyStack'][i]();
	    }
	}
};

/**
 * 调起微信卡券
 * 
 * @param account_id
 * @param card_id
 * @param outer_id
 * @param callBack
 */
SuiShi_WxTool.prototype.addCard = function(account_id, card_id, outer_id,ext_param,
		callBack) {
	try{
		this._addCard(account_id, "",card_id, outer_id, ext_param,callBack);
	} catch (e) {
		this._log("调用微信调 起卡券出现js错误："+e.message,e);
	}
};


/**
 * 调起微信卡券
 * 
 * @param account_id
 * @param card_id
 * @param outer_id
 * @param callBack
 */
SuiShi_WxTool.prototype.addCardByAppId = function(app_id, card_id, outer_id,ext_param,
		callBack) {
	try{
		this._addCard('', app_id,card_id, outer_id, ext_param,callBack);
	} catch (e) {
		this._log("调用微信调 起卡券出现js错误："+e.message,e);
	}
};

/**
 * 调起微信卡券
 * 
 * @param account_id
 * @param card_id
 * @param outer_id
 * @param callBack
 */
SuiShi_WxTool.prototype._addCard = function(account_id,app_id, card_id, outer_id,ext_param,
		callBack) {
	var _this = this;
	if (_this.param['useJsSDK'] == false && _this.param['useJsAPI'] == false) {
		_this._setError("20003", "卡券组件包还没准备好");
		_this._callbackFunction(callBack, _this
				._makeRsult("", "－1", "卡券组件包还没准备好"));
		return;
	}
	var useJsSDK = this.param['useJsSDK'];
	var dataUrl = this.param['jsCardSignUrl'];
	var param = {
			"account_id" : account_id,
			"app_id" : app_id,
			"wx_card_ids":card_id,
			"outer_id":outer_id
	};
	param = _this._mergeParam(param, ext_param);
	if (useJsSDK == true) {
		param['isJsSDK'] = true;
	}
	_this._log("开始调起卡，ajax加载签名数据中,url:"+dataUrl+",参数：",param);
	_this.loading("on", "加载数据中...");
	try {
		$.ajax({
			type : 'post',
			url : dataUrl,
			data : param,
			dataType : "json",
			error : function() {
				_this.loading("off");
				_this._setError("20001", "获取签名数据失败");
				_this._callbackFunction(callBack, _this._makeRsult("", "20001",
						"获取签名数据失败"));
			},
			success : function(result) {
				_this.loading("off");
				if (result.error != 0) {
					_this._setError(result.error, "获取签名数据失败",result);
					_this._callbackFunction(callBack, _this._makeRsult(null, result.error,
							"获取签名数据失败"));
					return;
				}
				_this._log("获取签名数据成功(jsSDK:" + useJsSDK + ",)，数据结果：", result);
				if (useJsSDK == true) {
					_this._addCard_bySDK(result.data, callBack);
				} else {
					_this._addCard_byAPI(result.data, callBack);
				}
			}
		});
	} catch (e) {
		_this.loading("off");
		_this._log("获取签名数据出现异常(jsSDK:" + useJsSDK + "),出错原因："+e.message, e);
	}

};
/**
 * 网页jsJDK方式调起卡券
 * 
 * @param data
 * @param callBack
 */
SuiShi_WxTool.prototype._addCard_bySDK = function(data, callBack) {
	if (typeof (wx) == 'undefined') {
		this._setError("30001", "用JSSDK调起卡券失败,wx对象不存在");
		this._callbackFunction(callBack, this._makeRsult(data, "30002", "领取失败"));
		return;
	}
	var cardList = data.card_list;
	var app_id = data.app_id;
	var _this = this;
	_this._log("开始用JSSDK调起卡券，参数内容：", cardList);
	_this.loading("on", "弹出卡券中...");
	wx.addCard({
		cardList : cardList, // 需要添加的卡券列表
		success : function(res) {
			_this.loading("off");
			_this._log("调起成功返回结果：", res);
			_this._callbackFunction(callBack, _this._makeRsult(res, 0, ""));
		},
		cancel : function(res) {
			_this.loading("off");
			_this._log("调起取消返回结果：", res);
			// 回收code
			_this._recoverCodeNumber(cardList, true, app_id);
			_this._callbackFunction(callBack, _this
					._makeRsult(res, 1, "您取消的卡券领取"));
		},
		fail : function(res) {
			_this.loading("off");
			_this._log("调起失败返回结果：", res);
			// 回收code
			_this._recoverCodeNumber(cardList, true, app_id);
			_this._callbackFunction(callBack, _this._makeRsult(res, 2, "领取失败"));
		}
	});
};
/**
 * jsAPI方式调起卡券
 * 
 * @param data
 * @param callBack
 */
SuiShi_WxTool.prototype._addCard_byAPI = function(data, callBack) {
	if (typeof (WeixinJSBridge) == 'undefined') {
		this._setError("30002", "用JSAPI调起卡券失败，WeixinJSBridge对象不存在");
		this._callbackFunction(callBack, this._makeRsult(data, "30002", "领取失败"));
		return;
	}

	var _this = this;
	var app_id = data.app_id;
	var cardList = data.card_list;
	var apiParam = {
		"card_list" : cardList
	};
	_this._log("开始用JSAPI调起卡券，参数内容：", apiParam);
	_this.loading("on", "弹出卡券中...");
	WeixinJSBridge.invoke('batchAddCard', apiParam,
			function(res) {
				_this._log("调起返回结果：", res);
				_this.loading("off");
				if (res.err_msg == 'batch_add_card:ok') {
					_this._callbackFunction(callBack, _this
							._makeRsult(res, 0, ""));
				} else if (res.err_msg == 'batch_add_card:fail') {
					// 回收code
					_this._recoverCodeNumber(cardList, false, app_id);
					// 领取失败
					_this._callbackFunction(callBack, _this._makeRsult(res, 2,
							"领取失败"));
				} else if (res.err_msg == 'batch_add_card:cancel') {
					// 回收code
					_this._recoverCodeNumber(cardList, false, app_id);
					// 取消领取
					_this._callbackFunction(callBack, _this._makeRsult(res, 1,
							"您取消的卡券领取"));
				} else {
					// 回收code
					_this._recoverCodeNumber(cardList, false, app_id);
					// 拉取没反应
					_this._callbackFunction(callBack, _this._makeRsult(res, 3,
							"领取未知异常"));
				}
			});
};
/**
 * 回调函数
 * 
 * @param fn
 * @param param
 */
SuiShi_WxTool.prototype._callbackFunction = function(fn, param) {
	if (!fn || typeof (fn) != "function") {
		this._setError("10003", fn + "方式不是一个函数");
		return;
	}
	fn.call(null, param);
};
/**
 * 设置错误
 * 
 * @param errorCode
 * @param errorMsg
 */
SuiShi_WxTool.prototype._setError = function(errorCode, errorMsg , msgObj) {
	this.errorCode = errorCode;
	this.errorMsg = errorMsg;
	if (this.param['debug'] == true) {
		this._log("[errorLog][" + this.errorCode + "]" + this.errorMsg,msgObj);
	}
};

/**
 * 系统错误
 * 
 * @param msg
 *            第一个参数为文本类型，
 * @param obj
 *            第二个参数为任意属性
 */
SuiShi_WxTool.prototype._windowError = function(sMsg,sUrl,sLine) {
	this._log("系统js出错，出错消息："+sMsg+",文件："+sUrl+",行数:"+sLine);
};

/**
 * debug日志
 * 
 * @param msg
 *            第一个参数为文本类型，
 * @param obj
 *            第二个参数为任意属性
 */
SuiShi_WxTool.prototype._log = function(msg, obj) {
	var _this = this;
	var str2 = "";
	console.log(msg);
	if (arguments.length == 2) {
		var obj = arguments[1];
		console.log(obj);
		str2 = obj;
		if (Object.prototype.toString.call(obj) === '[object Array]') {
			str2 = obj.join(",");
		}
		if (typeof (obj) == "object") {
			str2 = JSON.stringify(obj);
		}
	}
	if(!_this.param){
		console.log("本对象里没有param这个变量");
		return;
	}
	var ipLogCheckFn = function() { // 检查日志是否需要做ip限制
		if (!(typeof (_this.__ipLogCheckResult) == "undefined")) {
			return _this.__ipLogCheckResult;
		}
		var result = false;
		var currIp = _this.param['debugCurrIp'];
		var limitIp = _this.param['debugLimitIp'];
		if (!currIp || !limitIp
				|| Object.prototype.toString.call(limitIp) !== '[object Array]'
				|| limitIp.length == 0) {
			_this.__ipLogCheckResult = true;
			return true;
		}
		//本机和内网IP不做限制
		if (currIp == "127.0.0.1" || currIp.substring(0, 7) == "192.168") {
			_this.__ipLogCheckResult = true;
			return true;
		}
		for ( var i = 0; i < limitIp.length; i++) {
			if (currIp == limitIp[i]) {
				result = true;
				break;
			}
		}
		_this.__ipLogCheckResult = result;
		return result;
	};
	var ipLimitOk = ipLogCheckFn();
	if (this.param['debug'] == true && ipLimitOk == true) {
		var divID = this.param["debugDivId"]
				|| ("__" + Math.round(Math.random() * 1000) + "logDIV");
		var divObj = document.getElementById(divID);
		//自动把日志加载到页面的最底端
		if (divObj == null || typeof (divObj) != "object") {
			var logDiv = document.createElement("div");
			logDiv.id = divID;
			document.getElementsByTagName("body")[0].appendChild(logDiv);
			this.param["debugDivId"] = divID;
			divObj = document.getElementById(divID);
		}
		var html = divObj.innerHTML;
		var d = (new Date()).Format("[M-d h:m:s]");
		html = d + msg + str2 + "</br>" + html;
		divObj.innerHTML = html;
	}
};
/**
 * 组装返回结构
 * 
 * @param data
 * @param error
 * @param msg
 * @returns {___anonymous10406_10463}
 */
SuiShi_WxTool.prototype._makeRsult = function(data, error, msg) {
	var result = {
		"data" : data,
		"error" : error,
		"msg" : msg
	};
	return result;
};

/**
 * 抛送监测
 * 
 * @param type
 *            监测的类型:(friend/timeline/qqweibo/qq/pageview)
 * @param setename
 *            事件名
 */
SuiShi_WxTool.prototype.monitor = function(type, setename) {
	try{
		var _this = this;
		var userId = this.param['monitorConf']['userId'];
		var openid = this.param['monitorConf']['openid'];
		var activityId = this.param['monitorConf']['activityId'];
		var ename = this.param['monitorConf']['ename'] || setename || "page";
		var jsUrl = this.param['monitorConf']['jsUrl'];
		_this._log("开始处理监测数据,监测类型："+type+",事件名："+ename);
		if (!userId || !openid || !activityId || !jsUrl) {
			_this._setError("40100",
					"需要抛送检测部门数据失败，但没有userId/openid/activityId/jsUrl:[" + userId
							+ "/" + openid + "/" + activityId + "/" + jsUrl + "]");
			return;
		}
		if (type != 'friend' && type != 'timeline' && type != 'qqweibo'
				&& type != 'qq' && type != 'qzone' && type != 'click'
				&& type != 'pageview') {
			_this._setError("40101", "需要抛送检测部门数据失败，type类型[" + type
					+ "]不是[friend/timeline/qqweibo/qq/qzone/click/pageview]之一");
			return;
		}
		if (typeof (_sstk) == 'undefined') {
			loadScript(jsUrl, function() {
				_this._log("第一次加载监测js文件成功，开始抛送数据");
				_this._sendMonitorData(type, openid, ename, userId, activityId);
			});
		} else {
			_this._sendMonitorData(type, openid, ename, userId, activityId);
		}
	} catch (e) {
		this._log("增加监测数据出现js错误："+e.message,e);
	}
};
/**
 * 数据部门的检测代码
 * 
 * @param type
 *            类型 (friend/timeline/qq/qqweibo/pageview)
 * @param openid
 * @param ename
 * @param activityId
 */
SuiShi_WxTool.prototype._sendMonitorData = function(type, openid, ename,
		userId, activityId) {
	var objCommon = {
		"opid" : openid,
		"ename" : ename,
	// 事件名称（必填）
	};
	var eType = 'share';
	switch (type) {
	case 'friend':
		objCommon['ename'] = "[分享给朋友]"+objCommon['ename'];
		// 分享类型（0—朋友圈；1—朋友；2—qq；3—腾讯微博）（必填）
		objCommon['stype'] = 1;
		break;
	case 'timeline':
		objCommon['ename'] = "[分享到朋友圈]"+objCommon['ename'];
		objCommon['stype'] = 0;
		break;
	case 'qq':
		objCommon['ename'] = "[分享到qq]"+objCommon['ename'];
		objCommon['stype'] = 2;
		break;
	case 'qqweibo':
		objCommon['ename'] = "[分享到腾讯微博]"+objCommon['ename'];
		objCommon['stype'] = 3;
		break;
	case 'qzone':
		objCommon['ename'] = "[分享到qq空间]"+objCommon['ename'];
		objCommon['stype'] = 4;
		break;
	case 'pageview':
		eType = 'pageview';
		break;
	case 'click':
		eType = 'click';
		break;
	default:
		return;
	}
	_sstk.create(userId, activityId);
	_sstk.send(eType, objCommon);
	this._log("抛送监测代理结束，类型:"+eType+",参数：", objCommon);
};

/**
 * loading的样式
 * @param status
 *            on:开始加载
 *            off:结束加载
 *            init:初始化
 * @param msg
 *            样式类型：默认为底黑loading白
 * @param type
 *            样式类型：默认为底黑loading白
 * @returns {Boolean}
 */
SuiShi_WxTool.prototype.loading = function(status,msg,type){	
	var divID =  this.param["_logDivId"]
				|| "__loading_div_"+Math.round(Math.random() * 1000);
	msg = msg || "努力处理中...";
	var divObj = document.getElementById(divID);
	//自动把日志加载到页面的最底端
	if (divObj == null || typeof (divObj) != "object") {
		var htmlDiv = document.createElement("div");
		htmlDiv.id = divID;
		htmlDiv.style.display = "none";
		htmlDiv.style.width = "100%";
		htmlDiv.style.height = "100%";
		htmlDiv.style.background = "rgba(0,0,0,0.8)";
		htmlDiv.style.position = "fixed";
		htmlDiv.style.left = "0";
		htmlDiv.style.top = "0";
		htmlDiv.style.textAlign = "center";
//		htmlDiv.style = "display:none; width: 100%;height: 100%;background: rgba(0,0,0,0.8);"
//				+"	position: fixed;left: 0;top: 0;text-align: center;";
		htmlDiv.innerHTML = '<div style="margin-top: 40%;width:100px;height: 100px;background-color: #f4f5f6;'
						+'		border-radius: 5px;overflow: hidden;text-align: center;margin-left: auto;margin-right: auto;">'
						+'        <p style="margin-top: 10px"><img src="images/loading.gif" width="50%"></p>'
						+'        <h4 style="margin-top: 10px;font-size:10px;overflow: hidden;">'+msg+'</h4>';
						+'    </div>';				
		document.getElementsByTagName("body")[0].appendChild(htmlDiv);
		this.param["_logDivId"] = divID;
		divObj = document.getElementById(divID);
	}
	status = status || "off";
	if(status == "on"){
		divObj.style.display = "block";
	}else{
		divObj.style.display = "none";
	}	
};

/**
 * 回收自定义code(只发回收请求，不用管返回)
 * 
 * @param card_list
 *            调起卡券的json对象
 * @param isJsSDK
 *            是否用jsSDK调起
 * @param app_id
 *            调起卡券的appid
 * @returns {Boolean}
 */
SuiShi_WxTool.prototype._recoverCodeNumber = function(card_list, isJsSDK,
		app_id) {
	var _this = this;
	if (!card_list) {
		return false;
	}
	var url = this.param['recoverCodeUrl'];

	for ( var i = 0; i < card_list.length; i++) {
		var wx_card_id = "";
		var code_ext = "";
		var code = "";
		if (isJsSDK) {
			wx_card_id = card_list[i].cardId;
			code_ext = $.parseJSON(card_list[i].cardExt);
		} else {
			wx_card_id = card_list[i].card_id;
			code_ext = $.parseJSON(card_list[i].card_ext);
		}
		if (code_ext) {
			code = code_ext.code;
		}
		if (!wx_card_id || !code) {
			_this._log("参数不全，不需要回收自定义code,code/wx_card_id/app_id:[" + code + "/"
					+ wx_card_id + "/" + app_id + "]");
			continue;
		}

		$.ajax({
			type : 'POST',
			url : url,
			data : {
				wx_card_id : wx_card_id,
				codeNumber : code,
				app_id : app_id
			},
			dataType : "json",
			success : function(data) {
				_this._log("回收自定义code结束，回收结果：", data);
			},
			error : function(data) {
				_this._setError("20003", "回收自定义code失败");
			}
		});
	};
};
