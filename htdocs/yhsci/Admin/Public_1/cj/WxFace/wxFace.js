/**
 * 微信表情js插件
 */
Face.IDS = {
		outDivId : 'facePopId',
		closeId  : 'closeId'
};
Face.__onlyId = 1;

/**
 * 微信表情对象
 * @param sourceId
 * @param targetId
 * @param option
 * @returns {Face}
 */
function Face(sourceId, targetId, option){
	this._onlyId   = Face.__onlyId;
	this._sourceId = sourceId || '';
	this._targetId = targetId || '';
	
	this._left = this._top = 0;//弹出层距离浏览器左边和顶部的距离
	this._popWidth = 468; //弹出层宽带
	this._popHight = 190; //弹出层高度
}

//生成id
Face.prototype._getOnlyId = function(id, prefix){
	prefix = prefix || '';
	return prefix + 's' + this._onlyId + '_' + id;
}

//初始化
Face.prototype.init = function(){
	if($('#'+this._getOnlyId(Face.IDS.outDivId)).attr('id') == this._getOnlyId(Face.IDS.outDivId)){
		return ;
	}
	//生成表情弹出层
	this.generalFrame(); 	
}

//生成表情弹出层
Face.prototype.generalFrame = function(){  	
	var offset 	 = $('#'+this._sourceId).offset();	
	this._left   = offset.left;
	this._top    = offset.top+20; 
	
	var popStr ='<div id="'+this._getOnlyId(Face.IDS.outDivId)+'" '
					+'style="position:absolute !important;z-index:3;border:1px solid gray;background-color:#fff;display:block;width:'+this._popWidth+'px;height:'+this._popHight+'px;text-align:right;left:'+this._left+'px;top:'+this._top+'px;padding:0px;">';
		popStr += this.generalInnerFrame();		 
		popStr +='</div>';
	$('body').append(popStr);	         
	this.showPop();	
}
//生成表情内部内容
Face.prototype.generalInnerFrame = function(){
	var popStr = '';
	if(typeof(window.cacheData) == 'undefined' || typeof(window.cacheData) == null || window.cacheData == '' ){	
		popStr += '<a href="#" id="'+this._getOnlyId(Face.IDS.closeId)+'">关闭</a>';
		popStr += '<div style="text-align:center;"><ul>';    
		var facePath = '<img src="http://cache.soso.com/img/img/e%s.gif">';
		var data = this.faceList1();			
		$.each(data,function(k,v){
			var reg    = /%s/g;
			var imgSrc = facePath.replace(reg,v);
			popStr +='<li style="float:left;border:#CCCCCC solid 1px" title="'+k+'">'+imgSrc+'</li>';
		});			
		popStr +='</ul></div>';
		window.cacheData = popStr;
		//添加监听事件
		this.bindEvent();
	}else{
		popStr = window.cacheData;
	}   
	return popStr;
}
//显示弹出层
Face.prototype.showPop = function(){		
		
	//弹出层外围点击时关闭弹出层
	var thisObj = this;
	document.onclick=function(ev){
		var oEvent	= ev||event;
		var oLeft	= oEvent.clientX; //鼠标距离左边的距离
		var oTop	= oEvent.clientY; //鼠标距离顶部的距离		
		
		if(oLeft<thisObj._left || oLeft>(thisObj._left+thisObj._popWidth) || (oTop<thisObj._top-20) || oTop>(thisObj._top+thisObj._popHight) ){
			thisObj.closePop();
		}
	}
}

//关闭表情弹出层
Face.prototype.closePop = function(){	 
	$('#'+this._getOnlyId(Face.IDS.outDivId)).remove();//detach比remove()好
}

//绑定事件
Face.prototype.bindEvent = function(){
	$('#'+this._getOnlyId(Face.IDS.closeId)).live('click',Face.bind(this.closePop, this));
	
    var thisObj = this;
	$('#'+this._getOnlyId(Face.IDS.outDivId)+' li').live('click',function(){
		var faceContent 	= $(this).attr('title');
			faceContent     = $('#'+thisObj._targetId).val()+'['+faceContent+']';			
		$('#'+thisObj._targetId).val(faceContent);		
		thisObj.closePop();
   });  	
}

//闭包处理
Face.bind = function(fn, selfObj, var_args) {
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

//提交值时表情过滤(由用户识别的表情符转换到微信识别的表情符)
Face.filterFace = function(content){
	$.each(Face.faceList2(), function(k,v){		
		var find = '['+v+']';
		content = content.replace(find,k);
	});
	return content;
}
Face.prototype.faceList1  = function(){
	var faces = {
			'微笑'   : 100,   
			'撇嘴 '   : 101,   
			'色'   	 : 102,   
			'发呆'   : 103,   
			'得意'   : 104,  
			'流泪'   : 105,   
			'害羞'   : 106,   
			'闭嘴'   : 107,   
			'睡'     : 108,   
			'大哭'   : 109,  
			'尴尬'   : 110,  
			'发怒'   : 111,   
			'调皮'   : 112,   
			'呲牙'   : 113,   
			'惊讶'   : 114,   
			'难过'   : 115,  
			'酷'     : 116,   
			'冷汗'   : 117,  
			'抓狂'   : 118,   
			'吐'     : 119,   
			'偷笑'   : 120,  
			'愉快'   : 121, 
			'白眼'   : 122,   
			'傲慢'   : 123,  
			'饥饿'   : 124,   
			'困'     : 125,  
			'惊恐'   : 126,   
			'流汗'   : 127,   
			'憨笑'   : 128,   
			'悠闲'   : 129,  
			'奋斗'   : 130,  
			'咒骂'   : 131,  
			'疑问'   : 132,    
			'嘘'     : 133,  
			'晕'     : 134,  
			'疯了'   : 135,   
			'衰'     : 136,  
			'骷髅'   : 137,  
			'敲打'   : 138,   
			'再见'   : 139,  
			'擦汗'   : 140, 
			'抠鼻'   : 141,  
			'鼓掌'   : 142, 
			'糗大了' : 143,      
			'坏笑'   : 144,      
			'左哼哼' : 145,       
			
			'右哼哼' : 146,   
			'哈欠'   : 147,  
			'鄙视'   : 148,  
			'委屈'   : 149,  
			'快哭了' : 150,  
			'阴险'   : 151,  
			'亲亲'   : 152,   
			'吓'     : 153,   
			'可怜'   : 154,   
			'菜刀'   : 155,   
			'西瓜'   : 156,  
			'啤酒'   : 157, 
			'篮球'   : 158, 
			'乒乓'   : 159,      
			'咖啡'   : 160,  
			'饭'     : 161,     
			'猪头'   : 162,     
			'玫瑰'   : 163,    
			'凋谢'   : 164,    
			'凋谢'   : 164,    
			'嘴唇'   : 165,  
			'爱心'   : 166,   
			'心碎'   : 167,   
			'蛋糕'   : 168,    
			
			'闪电'   : 169,      
			'炸弹' : 170,    
			'刀'   : 171,      
			'足球'   : 172,   
			'瓢虫' : 173, 
			'便便' : 174,    
			'月亮' : 175,    
			'太阳'  : 176,     
			'礼物' : 177,    
			'拥抱'  : 178,     
			'强' : 179,  
			'弱'   : 180,    
			'握手'  : 181,   
			'胜利'   : 182,       
			'抱拳'  : 183,      
			'勾引'  : 184,      
			'拳头'  : 185,      
			'差劲' : 186,     
			'爱你' : 187,     
			'NO'  : 188,      
			'OK'  : 189,      
			'爱情' : 190,    
			'飞吻' : 191,     
			
			'跳跳'  : 192,    
			'发抖' : 193,   
			'怄火'    : 194,     
			'转圈' : 195,  
			'磕头'  : 196,   
			'回头' : 197,    
			'跳绳' : 198,    
			'投降'   : 199,      
			'激动'  : 200,     
			'乱舞' : 201,  
			'献吻' : 202,    
			'左太极'   : 203,      
			'右太极'   : 204,      
			};
	return faces;
}
Face.faceList2  = function(){
	var faces = {
			'/::)'   : '微笑',
			'/::~'   : '撇嘴 ',
			'/::B'   : '色',
			'/::|'   : '发呆',
			'/:8-)'  : '得意',
			'/::<'   : '流泪',
			'/::$'   : '害羞',
			'/::X'   : '闭嘴',
			'/::Z'   : '睡',
			'/::\'('  : '大哭',
			'/::-|'  : '尴尬',
			'/::@'   : '发怒',
			'/::P'   : '调皮',
			'/::D'   : '呲牙',
			'/::O'   : '惊讶',
			' /::('  : '难过',
			'/::+'   : '酷',
			'/:--b'  : '冷汗',
			'/::Q'   : '抓狂',
			'/::T'   : '吐',
			'/:,@P'  : '偷笑',
			'/:,@-D' : '愉快',
			'/::d'   : '白眼',
			'/:,@o'  : '傲慢',
			'/::g'   : '饥饿',
			'/:|-)'  : '困',
			'/::!'   : '惊恐',
			'/::L'   : '流汗',
			'/::>'   : '憨笑',
			'/::,@'  : '悠闲',
			'/:,@f'  : '奋斗',
			'/::-S'  : '咒骂',
			'/:?'    : '疑问',
			'/:,@x'  : '嘘',
			'/:,@@'  : '晕',
			'/::8'   : '疯了',
			'/:,@!'  : '衰',
			'/:!!!'  : '骷髅',
			'/:xx'   : '敲打',
			'/:bye'  : '再见',
			'/:wipe' : '擦汗',
			'/:dig'  : '抠鼻',
			'/:handclap' : '鼓掌',
			'/:&-('  : '糗大了',
			'/:B-)'  : '坏笑',
			'/:<@'   : '左哼哼',
			
			'/:@>'   : '右哼哼',
			'/::-O'  : '哈欠',
			'/:>-|'  : '鄙视',
			'/:P-('  : '委屈',
			'/::\'|'  : '快哭了',
			'/:X-)'  : '阴险',
			'/::*'   : '亲亲',
			'/:@x'   : '吓',
			'/:8*'   : '可怜',
			'/:pd'   : '菜刀',
			'/:<W>'  : '西瓜',
			'/:beer' : '啤酒',
			'/:basketb' : '篮球',
			'/:oo'   : '乒乓',
			'/:coffee'  : '咖啡',
			'/:eat'  : '饭',
			'/:pig'  : '猪头',
			'/:rose' : '玫瑰',
			'/:fade' : '凋谢',
			'/:fade' : '凋谢',
			'/:showlove' : '嘴唇',
			'/:heart' : '爱心',
			'/:break' : '心碎',
			'/:cake'  : '蛋糕',
			
			'/:li'   : '闪电',
			'/:bome' : '炸弹',
			'/:kn'   : '刀',
			'/:footb'   : '足球',
			'/:ladybug' : '瓢虫',
			'/:shit' : '便便',
			'/:moon' : '月亮',
			'/:sun'  : '太阳',
			'/:gift' : '礼物',
			'/:hug'  : '拥抱',
			'/:strong' : '强',
			'/:weak'   : '弱',
			'/:share'  : '握手',
			'/:v'   : '胜利',
			'/:@)'  : '抱拳',
			'/:jj'  : '勾引',
			'/:@@'  : '拳头',
			'/:bad' : '差劲',
			'/:lvu' : '爱你',
			'/:no'  : 'NO',
			'/:ok'  : 'OK',
			'/:love' : '爱情',
			'/:<L>' : '飞吻',
			
			'/:jump'  : '跳跳',
			'/:shake' : '发抖',
			'/:<O>'    : '怄火',
			'/:circle' : '转圈',
			'/:kotow'  : '磕头',
			'/:turn' : '回头',
			'/:skip' : '跳绳',
			'/:oY'   : '投降',
			'/:#-0'  : '激动',
			'/:hiphot' : '乱舞',
			'/:kiss' : '献吻',
			'/:<&'   : '左太极',
			'/:&>'   : '右太极'
		};
	return faces;
}
