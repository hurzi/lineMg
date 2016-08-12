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
 * @param option Object
 * option.position:值down或up表示表情弹出层是显示在按钮下面还是上面，不传递默认显示在上面
 * @returns {Face}
 */
function Face(sourceId, targetId, option){
	this._onlyId   = Face.__onlyId;
	this._sourceId = sourceId || '';
	this._targetId = targetId || '';
	var     option = option || {};
	this._displayPosition = (option.position || 'UP').toUpperCase(); //表情弹出层默认显示的位置
	this._isDocument = false;
	
	this._left = this._top = 0;//弹出层距离浏览器左边和顶部的距离
	this._popWidth = 468; //弹出层宽带
	this._popHight = 195; //弹出层高度
	Face.__onlyId ++;
	if (targetId) {
		$('#'+targetId).on('keydown keyup mousedown mouseup focus',
				function () {
				savePos(this);
		});
	}
}

//生成id
Face.prototype._getOnlyId = function(id, prefix){
	prefix = prefix || '';
	return prefix + 's' + this._onlyId + '_' + id;
};

//初始化
Face.prototype.init = function(){
	if($('#'+this._getOnlyId(Face.IDS.outDivId)).attr('id') == this._getOnlyId(Face.IDS.outDivId)){//判断当前弹出层是否在打开
		if (this._isDocument == false) {
			this.showPop();
		}
		return ;
	}
	//生成表情弹出层
	this.generalFrame(); 	
};

//生成表情弹出层
Face.prototype.generalFrame = function(){  	
	var offset 	 = $('#'+this._sourceId).offset();	
	this._left   = offset.left;
	if(this._displayPosition == 'UP'){
		this._top    = offset.top - this._popHight ; 
	}else{
		this._top    = offset.top + 25 ; 
	}
	var popStr ='<div id="'+this._getOnlyId(Face.IDS.outDivId)+'" '
					+'style="position:absolute !important;z-index:10000;border:1px solid gray;background-color:#fff;display:block;width:'+this._popWidth+'px;height:'+this._popHight+'px;text-align:right;left:'+this._left+'px;top:'+this._top+'px;padding:0px;">';
		popStr += this.generalInnerFrame();		 
		popStr +='</div>';
	$('body').append(popStr);	         
	this.showPop();	
};
//生成表情内部内容
Face.prototype.generalInnerFrame = function(){
	var popStr = '';
	if(typeof(window.cacheData) == 'undefined' || typeof(window.cacheData) == null || window.cacheData == '' ){	
		popStr += '<a href="javascript:;" id="'+this._getOnlyId(Face.IDS.closeId)+'">关闭</a>';
		popStr += '<div style="text-align:center;"><ul>';    
		var facePath = '<img src="http://cache.soso.com/img/img/e%s.gif">';
		var data = Face.faceList1();			
		$.each(data,function(k,v){
			var reg    = /%s/g;
			var imgSrc = facePath.replace(reg,v);
			popStr +='<li class="face_img" style="cursor:pointer;float:left;border:#CCCCCC solid 1px" title="'+k+'">'+imgSrc+'</li>';
		});			
		popStr +='</ul></div>';
		window.cacheData = popStr;
	}else{
		popStr = window.cacheData;
	}   
	return popStr;
};
//显示弹出层
Face.prototype.showPop = function(){
	this.state = 1;
	//添加监听事件
	this.bindEvent();
	this._isDocument = true;
	//弹出层外围点击时关闭弹出层
	var thisObj = this;
	setTimeout(function() {
		document.onclick=function(ev){
			/*var faceFrame = $('#'+thisObj._getOnlyId(Face.IDS.outDivId)).offset();
			var oEvent	= ev||event;
			var oLeft	= oEvent.clientX; //鼠标距离左边的距离
			var oTop	= oEvent.clientY; //鼠标距离顶部的距离*/
			//点击到表情界面以外关闭
			thisObj.closePop();
		};
	}, 10);
	return false;
};

//关闭表情弹出层
Face.prototype.closePop = function(){
	if (this._isDocument == true) {
		$('#'+this._getOnlyId(Face.IDS.outDivId)).remove();//detach比remove()好
	}
	this._isDocument = false;
	$('#'+this._getOnlyId(Face.IDS.closeId)).die();
	$('#'+this._getOnlyId(Face.IDS.outDivId)+' li').die();
	document.onclick = null;
	this.state = 0;
	return false;
};

//绑定事件
Face.prototype.bindEvent = function(){
	$('#'+this._getOnlyId(Face.IDS.closeId)).die().click(Face.bind(this.closePop, this));
	
    var thisObj = this;
    $('#'+this._getOnlyId(Face.IDS.outDivId)+' li').die().click(function(){
    	try {
		var faceContent 	= $(this).attr('title');
		addContent(thisObj._targetId, '['+faceContent+']');
		thisObj.closePop();
	}catch(e){
		alert(e.message);
	}
   }); 	
};

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
	var showContent = content;
	var faceList1 = Face.faceList1();
	$.each(Face.faceList2(), function(k,v){		
		var find = new RegExp("\\["+v+"\\]", 'gi');
		content = content.replace(find,k);
		showContent = showContent.replace(find,"<img src='" + 'http://cache.soso.com/img/img/e%s.gif'.replace(/%s/g, faceList1[v]) + "' />");
	});
	return [content, showContent];
};
//将消息内容中表情转换成编辑模式下的标签符号[微笑]
Face.toEditMode = function(content) {
	if (!content) return '';
	var patten = '';
	$.each(Face.faceList2(), function(k,v){
		var find = new RegExp(k.replace(/([\*\.\?\+\$\^\[\]\(\)\{\}\|\\\/])/g,"\\$1"), 'gi');
		content = content.replace(find,'['+v+']');
	});
	return content;
}

//定义全局变量
CursorPosition.cacheIds 	= [];     //定义光标对象IDS集合
CursorPosition.cachePosObjs = []; 	  //定义光标对象集合

/*添加内容调用(即赋值调用)
*   textBoxId   内容显示的载体(text或textArea输入框的id)
*   pushContent 要追加的内容
*/
function addContent(textBoxId, pushContent) {
	if(!textBoxId || !pushContent){
		alert('参数丢失');
		return ;
	}
	var posObj = CursorPosition.cachePosObjs[textBoxId];
	if (!CursorPosition.isExists(textBoxId) || !posObj) {
		savePos(null, textBoxId);
		posObj = CursorPosition.cachePosObjs[textBoxId];
	}
	var textBox = document.getElementById(textBoxId);
	var pre = textBox.value.substr(0, posObj.start);
	var post = textBox.value.substr(posObj.end);
	textBox.value = pre + pushContent + post;
}



/**供触发文本框调用
 * textBox||textBoxId  内容显示的载体(text或textArea输入框的对象或id),两个参数至少需要填写一个
 */
function savePos(textBox, textBoxId) {
	if (!textBox && !textBoxId) {
		alert('参数丢失,文本区域对象或文本区域对象ID两个参数至少填写一个');
		return;
	}
	if (!textBoxId) {
		 textBoxId = textBox.id || textBox.getAttribute('id');
	}
	if (!textBox) {
		 textBox = document.getElementById(textBoxId);
	}
	
	if (!CursorPosition.isExists(textBoxId)) {
			var index = CursorPosition.getID(textBoxId);
			CursorPosition.cacheIds[index]	= textBoxId;;
			CursorPosition.cachePosObjs[textBoxId] = new CursorPosition();
	}
	CursorPosition.cachePosObjs[textBoxId].setPos(textBox);
}

//创建光标位置对象
function CursorPosition() {	
	this.start = 0;
	this.end = 0;
}
//得到光标选择区域开始位置和结束位置
CursorPosition.prototype.getSE_notUsed = function(textBoxId) {
	var start_end = [];
	start_end['start'] = CursorPosition.cachePosObjs[textBoxId].start;
	start_end['end'] = CursorPosition.cachePosObjs[textBoxId].end;
	return start_end;
};
//判断光标位置对象是否存在
CursorPosition.isExists = function(id) {
	var index = this.getID(id);
	var cacheId = CursorPosition.cacheIds[index];
	if (cacheId) {
		return true;
	} else {		
		return false;
	}
};
CursorPosition.getID = function(id){
	var index = 'prefix_' + id;
	return index;
};
//CursorPosition.
//设置开始光标位置和结束位置
CursorPosition.prototype.setPos = function(textBox) {
	//如果是Firefox(1.5)的话，方法很简单
	if (typeof (textBox.selectionStart) == "number") {
		this.start = textBox.selectionStart;
		this.end = textBox.selectionEnd;
	}
	//下面是IE(6.0)的方法，麻烦得很，还要计算上'\n'
	else if (document.selection) {
		var range = document.selection.createRange();
		if (range.parentElement().id == textBox.id) {
			// create a selection of the whole textarea
			var range_all = document.body.createTextRange();
			range_all.moveToElementText(textBox);
			//两个range，一个是已经选择的text(range)，一个是整个textarea(range_all)
			for (this.start = 0; range_all.compareEndPoints("StartToStart",
					range) < 0; this.start++) {
				range_all.moveStart('character', 1);
			}
			// 计算一下\n
			for ( var i = 0; i <= this.start; i++) {
				if (textBox.value.charAt(i) == '\n')
					this.start++;
			}
			// create a selection of the whole textarea
			var range_all = document.body.createTextRange();
			range_all.moveToElementText(textBox);
			// calculate selection end point by moving beginning of range_all to end of range
			for (this.end = 0; range_all.compareEndPoints('StartToEnd', range) < 0; this.end++)
				range_all.moveStart('character', 1);
			// get number of line breaks from textarea start to selection end and add them to end
			for ( var i = 0; i <= this.end; i++) {
				if (textBox.value.charAt(i) == '\n')
					this.end++;
			}
		}
	}	
};

Face.faceList1  = function(){
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
			'右太极'   : 204     
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
};
