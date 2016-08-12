/**
 * h5摇一摇的js类
 */
function SuiShi_Shake(params) {
	this.param = {
			"power":false,
			"debug" : false, // 是否调试
			"debugLimitIp" : [], // 仅限设置的IP才能进行debug输出,不填代理不限制.(目的是在正式环境下也能调试错误)119.57.165.19为北京办公外网
			"debugCurrIp" : "", // 当前IP，需要通过参数传进来，与debugLimitIP配合使用，都设置了才有效。否则不做检验
			"callback":function(){},
			"speed":500,  // 定义摇的速度，越少越容易触发事件
	};	
	try {
		//初始化参数
		this._initParam(params);
		//如果url中有_debug_参数且为真则自动填充debug参数
		if(getUrlParam("_debug_")){
			this._log("－－－－－－－－url参数_debug_强制设置为debug模式－－－－－－－－－－－");
			this.param['debug'] = Boolean(getUrlParam("_debug_"));
		}
		
		//设置监听
		if(window.DeviceMotionEvent){
	        //移动浏览器支持运动传感事件
	        window.addEventListener('devicemotion', SuiShibind(
					this._shakeCallback, this), false);
	    }else{
	        this._log("非移动设备，不能用页面摇一摇事件");
	    }
	} catch (e) {
		this._log("创建SuiShiShake失败报错了，错误信息：",e);
	}
};

/**
 * 摇一摇的回调 
 * 
 * @param param
 * @returns {Boolean}
 */
SuiShi_Shake.prototype.setPower = function(power) {
	this.param['power'] = power;
	this._log("已设置当前页面是否允许用摇一摇事件："+power);
};

/**
 * 摇一摇的回调 
 * 
 * @param param
 * @returns {Boolean}
 */
SuiShi_Shake.prototype._shakeCallback = function(eventData) {
	var _this = this;
	if(_this.param['power'] == false){
		return;
	}
    var SHAKE_THRESHOLD = 500;
 // 定义一个变量保存上次更新的时间
    var last_update = _this.LAST_UPDATE_TIME || 0;
    // 紧接着定义x、y、z记录三个轴的数据以及上一次出发的时间
    var last_x = 0;
    var last_y = 0;
    var last_z = 0;
    try {
    	// 获取含重力的加速度
		var acceleration = eventData.accelerationIncludingGravity;
		// 获取当前时间
		var curTime = new Date().getTime();
		var diffTime = curTime - last_update;
		//_this._log("----------------diffTime：" + diffTime);
		// 固定时间段
		if (diffTime > 200) {

			_this.LAST_UPDATE_TIME = curTime;
			var x = acceleration.x;
			var y = acceleration.y;
			var z = acceleration.z;
			var speed = Math.abs(x + y + z - last_x - last_y - last_z)
					/ diffTime * 3000;
			// 自定义代码段
			if (speed > SHAKE_THRESHOLD) {
				_this._log("已经触发了摇一摇的事件");
				_this._callbackFunction(_this.param['callback']);
			}
			last_x = x;
			last_y = y;
			last_z = z;
		}
	} catch (e) {
		_this._log("处理监听事件失败:"+e.message,e);
	}
};

/**
 * 初始化参数
 * 
 * @param param
 * @returns {Boolean}
 */
SuiShi_Shake.prototype._initParam = function(param) {
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
SuiShi_Shake.prototype._mergeParam = function(oldParam, newParam) {
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
 * debug日志
 * 
 * @param msg
 *            第一个参数为文本类型，
 * @param obj
 *            第二个参数为任意属性
 */
SuiShi_Shake.prototype._log = function(msg, obj) {
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
		var divID = this.param["debugShakeDivId"]
				|| ("__" + Math.round(Math.random() * 1000) + "logShakeDIV");
		var divObj = document.getElementById(divID);
		//自动把日志加载到页面的最底端
		if (divObj == null || typeof (divObj) != "object") {
			var logDiv = document.createElement("div");
			logDiv.id = divID;
			document.getElementsByTagName("body")[0].appendChild(logDiv);
			this.param["debugShakeDivId"] = divID;
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
SuiShi_Shake.prototype._makeRsult = function(data, error, msg) {
	var result = {
		"data" : data,
		"error" : error,
		"msg" : msg
	};
	return result;
};
/**
 * 设置错误
 * 
 * @param errorCode
 * @param errorMsg
 */
SuiShi_Shake.prototype._setError = function(errorCode, errorMsg) {
	this.errorCode = errorCode;
	this.errorMsg = errorMsg;
	if (this.param['debug'] == true) {
		this._log("[errorLog][" + this.errorCode + "]" + this.errorMsg);
	}
};
/**
 * 回调函数
 * 
 * @param fn
 * @param param
 */
SuiShi_Shake.prototype._callbackFunction = function(fn, param) {
	if (!fn || typeof (fn) != "function") {
		this._setError("10003", fn + "不是一个函数");
		return;
	}
	fn.call(null, param);
};
