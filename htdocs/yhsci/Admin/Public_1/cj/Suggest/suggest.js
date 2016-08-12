/**
 * @class Suggest
 * @name  搜索联想功能JS
 * @author Zox<zox_long_hotmail@163.com>
 * @param object param 其他参数
 * @message 为空时 返回{id:0,name:''}
 */

//id集
Suggest.IDS = {
	suggest_wrapper: 'suggest_wrapper',
	suggest_list :	 'suggest_list'
};

Suggest.uuid_ = 1;

function Suggest(id,url,option){
	this.uuid_ = Suggest.uuid_++;
	this._id  = id  || '';
	this._url = url || '';
	if(!this._id || !this._url){
		alert('缺少参数:节点id 或者 获取数据的url');
	}
	if(option){
		this.default_value = option.default_value || '';
		this.keyword_name  = option.keyword_name  || 'keyword';
		this.callback      = option.callback      || '';
	}
	this.cache_ = {};
	this.data = [];
}

//获取联想区域ID
Suggest.prototype.getId_ = function (id, perfix) {
	perfix = perfix || '';
	return perfix + 's' + this.uuid_ + '_' + id;
}

//初始化
Suggest.prototype.init = function(){
	if(this.default_value!=''){
		$('#'+this._id).val(this.default_value);
	}
	this.post_id = 0;
	//生成浮动框架
	this.genFrameTpl_();
	this.input_obj = $('#'+this._id);
	this.input_obj.attr('autocomplete','off');
	var offset = $('#'+this._id).offset();
	var input_top  = offset.top;
	var input_height = $('#'+this._id).outerHeight();
	//判断浏览器是否为IE
	//alert($.browser.webkit);
	if($.browser.msie){
		this.show_left = offset.left + 1;
		this.show_width  = $('#'+this._id).outerWidth();
		this.show_top = input_top + input_height + 1;
	}else{
		this.show_left = offset.left;
		this.show_width  = $('#'+this._id).outerWidth()-2;
		this.show_top = input_top + input_height -1;
	}
	this.baseListen_();
}


//显示数据到元素下方
Suggest.prototype.show_ = function(){
	$('#'+this.getId_(Suggest.IDS.suggest_wrapper)).show();
	$('#'+this.getId_(Suggest.IDS.suggest_wrapper)).css('top',this.show_top);
	$('#'+this.getId_(Suggest.IDS.suggest_wrapper)).css('left',this.show_left);
	$('#'+this.getId_(Suggest.IDS.suggest_list)).css('width',this.show_width);
	this.genTpl_();
	this.showListen_();
}


//基础监听
Suggest.prototype.baseListen_ = function(){
	this.showListenType_ = 0;
	//取消回车自动提交
	$('#'+this._id).live('keydown',Suggest.bind(this.baseKeydownListen_,this));

	$('#'+this._id).live('keyup',Suggest.bind(this.baseKeyupListen_,this));
}

//基础监听keydown回调函数
Suggest.prototype.baseKeydownListen_ = function(e){
		e = window.event || e; // 兼容IE7
		var key_code = e.which || e.keyCode;
		if(key_code == 13 && $('#'+this.getId_(Suggest.IDS.suggest_list)+' li').length!=0){
			return false;
		}
		if(key_code==38){
			return false;
		}
		if(key_code==40){
			return false;
		}
}

//基础监听keyup回调函数
Suggest.prototype.baseKeyupListen_ = function(e){
		e = window.event || e; // 兼容IE
		var key_code = e.which || e.keyCode;
		if(key_code==13 || key_code===38 || key_code == 40){
			return;
		}
		this.keyword = $('#'+this._id).val();
		//清除
		this.clearShow_();
		this.post_id++;
		if(this.keyword.trim()==''){
			if(this.callback){
				this.callback.call(null,{id:0,name:''});
			}
			return;
		}
		this.getSuggestData_();
}

//取消显示联想信息框
Suggest.prototype.clearShow_ = function(){
	$('#'+this.getId_(Suggest.IDS.suggest_wrapper)).hide();
	$('#'+this.getId_(Suggest.IDS.suggest_list)+' li').remove();
}


//监听联想区域
Suggest.prototype.showListen_ = function(){
	if(!this.data){
		return;
	}
	if(this.showListenType_=== 1){
		return;
	}
	this.showListenType_ = 1;
	//键盘监听
	$('#'+this._id).live('keyup',Suggest.bind(this.keyboardListen_,this));
	//鼠标监听
	$('#'+this.getId_(Suggest.IDS.suggest_list)+' li').live('mousemove',Suggest.bind(this.mouseMoveListen_,this));
	$('#'+this.getId_(Suggest.IDS.suggest_list)+' li').live('click',Suggest.bind(this.mouseClickListen_,this));

	//点击其他地方隐藏联想区域
	$('body').click(Suggest.bind(this.bodySuggestHide_,this));
}

//键盘监听回调函数
Suggest.prototype.keyboardListen_ = function(e){
		e = window.event || e; // 兼容IE7
		var key_code = e.which || e.keyCode;
		switch(key_code){
			case 13:
				this.enterAction_();
				break;
			case 38:
				this.upAction_();
				break;
			case 40:
				this.downAction_();
				break;
		}
}

//联想区域鼠标移动监听回调函数
Suggest.prototype.mouseMoveListen_ = function(event){
	$(event.currentTarget).parent().find('li').removeClass('suggest_li_selected');
	$(event.currentTarget).addClass('suggest_li_selected');
}

//联想区域鼠标点击监听回调函数
Suggest.prototype.mouseClickListen_ = function(event){
		var data_id = $(event.currentTarget).find('a').attr('data-value');
		this.submitAction_(data_id);
}
//body点击隐藏联想区域
Suggest.prototype.bodySuggestHide_ = function(e){
		e = window.event || e; // 兼容IE7
		var obj = $(e.srcElement || e.target);
		//判断是不是在目标区域
		if(obj.attr('id')!=this._id && obj.parent().parent().attr('id')!==this.getId_(Suggest.IDS.suggest_list)){
			this.clearShow_();
		}
}

//点击或者回车动作
Suggest.prototype.submitAction_ = function(data_id){
		//maybe error
		var news_keyword = this.data[data_id].name;
		if(this.data[data_id].id==0){
			news_keyword = '';
		}
		$('#'+this._id).val(news_keyword);
		$('.'+'suggest_li_selected').removeClass('suggest_li_selected');
		$('#'+this.getId_(Suggest.IDS.suggest_wrapper)).hide();
		$('#'+this.getId_(Suggest.IDS.suggest_list)+' li').remove();
		//调用回调函数
		if(this.callback){
			this.callback.call(null,this.data[data_id]);
		}
}


//键盘上键动作
Suggest.prototype.upAction_ = function(){
	if(this.keyword==''){
		return;
	}
	var li_doc = $('#'+this.getId_(Suggest.IDS.suggest_list)+' li');
	if(li_doc.length == 0 ){
		return;
	}
	if($('.suggest_li_selected').length === 0){
		 li_doc.last().addClass('suggest_li_selected');
		 return;
	}
	for(var i=0;i<li_doc.length;i++){
		if(li_doc.eq(i).attr('class')=='suggest_li_selected'){
			$('.suggest_li_selected').removeClass('suggest_li_selected');
			if(i<1){
				li_doc.last().addClass('suggest_li_selected');
			}else{
				li_doc.eq(i-1).addClass('suggest_li_selected');
			}
			break;
		}
	}
}


//键盘下键动作
Suggest.prototype.downAction_ = function(){
	if(this.keyword==''){
		return;
	}
	var li_doc = $('#'+this.getId_(Suggest.IDS.suggest_list)+' li');
	if(li_doc.length == 0 ){
		return;
	}
	if($('.suggest_li_selected').length === 0){
		 li_doc.first().addClass('suggest_li_selected');
		 return;
	}
	for(var i=0;i<li_doc.length;i++){
		if(li_doc.eq(i).attr('class')=='suggest_li_selected'){
			$('.suggest_li_selected').removeClass('suggest_li_selected');
			if(i==li_doc.length-1){
				 li_doc.first().addClass('suggest_li_selected');
			}else{
				li_doc.eq(i+1).addClass('suggest_li_selected');
			}
			break;
		}
	}
}


//键盘回车动作
Suggest.prototype.enterAction_ = function(){
	if($('#'+this.getId_(Suggest.IDS.suggest_list)+' li').length === 0 || $('.suggest_li_selected').length === 0){
		return;
	}
	var data_id = $('.suggest_li_selected a').attr('data-value');
	this.submitAction_(data_id);
}


//获取初始模版
Suggest.prototype.genFrameTpl_ = function(){
	if($('#'+this.getId_(Suggest.IDS.suggest_wrapper)).attr('id')===this.getId_(Suggest.IDS.suggest_wrapper)){
		return;
	}
	var str = '<div id="'+this.getId_(Suggest.IDS.suggest_wrapper)+'" style="display:none" class="suggest_wrapper" >' +
					'<ul id="'+this.getId_(Suggest.IDS.suggest_list)+'" class="suggest_list" >' +
					'</ul>' +
			  '</div>';
	$('body').append(str);
}


//获取模版
Suggest.prototype.genTpl_ = function(){
	if(this.data.length==0){
		this.data = [{id:0, name:'无匹配结果&nbsp;&nbsp;清空'}];		
	}
	var str = '';
	for(var i=0;i<this.data.length;i++){
		str +=	'<li>' +
					'<a data-value="'+i+'" href="javascript:void(0)">' +
					this.data[i].name.replace(this.keyword,'<b>'+this.keyword+'</b>') +
					'</a>' +
				'</li>';
	}
	$('#'+this.getId_(Suggest.IDS.suggest_wrapper)+' > ul').html(str);
}


//获取联想信息
Suggest.prototype.getSuggestData_ = function(){
	if(this.cache_[this.keyword.trim()] != undefined){
		this.data = this.cache_[this.keyword.trim()];
		this.show_();
		return;
	}
	$.post(this._url,this.keyword_name+'='+encodeURIComponent(this.keyword.trim()),Suggest.bind(this.parsePostData_,this,this.post_id));
}


//获取联想信息post处理函数
Suggest.prototype.parsePostData_ = function(post_id,msg){
		var keyword = this.keyword;
		try{
			var return_data = eval("("+msg+")");
			if(return_data.error === 0){
				this.data = return_data.data;
				//添加缓存
				this.cache_[keyword.trim()] = return_data.data;
			}else{
				this.data = [];
			}
		}catch(e){
			this.data = [];
		}
		//显示
		if(this.post_id === post_id ){
			this.show_();
		}
}

//闭包处理
Suggest.bind = function(fn, selfObj, var_args) {
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


