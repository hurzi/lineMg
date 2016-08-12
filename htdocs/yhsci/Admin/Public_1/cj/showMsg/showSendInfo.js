
function ShowSendData(){
		
}

//设置获取数据Url
ShowSendData.prototype.setUrl= function (url){
	if(!url){
		loadMack({off:'on',Limg:0,text:'url不存在',set:1000});
		return;
	}else{
		this._url = url;
	}
	return this;
}

//设置数据
ShowSendData.prototype.setData= function (type,data){
    this._data = data || [];
    if(!type){
    	loadMack({off:'on',Limg:0,text:'数据类型不能为空',set:1000});
    	return;
    }else{
    	this._type = type;
    }
    return this;
}

//验证Url
ShowSendData.prototype.checkUrl= function (){
	
}

//验证数据
ShowSendData.prototype.checkData= function(){
	
}

//显示信息
ShowSendData.prototype.show= function (){
	if(this._data){
		this.do_jsbox(this.getView());
	}else{
		var url_data = this._getUrlData();
		if(!url_data){
			return;
		}
		this.do_jsbox(this.getView(url_data));
	}
}

//显示到指定位置
ShowSendData.prototype.render = function(div_id){
	if(!div_id){
		return;
	}
	if(this._data){
		$('#'+div_id).html(this.getView());
	}else{
		var url_data = this._getUrlData();
		if(!url_data){
			return;
		}
		$('#'+div_id).html(this.getView(url_data));
	}
}

//弹出jsbox
ShowSendData.prototype.do_jsbox = function (content){
	var wb = new jsbox({
		 content:content,
		 onlyid:"maptss",
		 title:'信息内容',
		 footer:false,
		 conw:300,
		 Ok_button:false,
		 FixedTop:170,
		 iframe:false,
		 range:true,
		 mack:true
		}).show();
}

//Url获取数据
ShowSendData.prototype._getUrlData = function(){
	url = this._url;
	$.get(url,{},function(msg){
		var re_msg = eval('('+msg+')');
		if(re_msg['error']==0){
			return re_msg;
		}else{
			loadMack({off:'on',Limg:0,text:'数据获取失败',set:1000});
			return false;
		}
	})
}

//获取视图
ShowSendData.prototype.getView = function (type,data){
	data = data || this._data;
	type = type || this._type;
	switch (type){
		case 'text':
			return this._textView(data);
			break;
		case 'news':
			return this._newsView(data);
			break;
	}
}

//获取文本视图
ShowSendData.prototype._textView = function (data){
	/*var html = '<div class="dhLb he">' +
					'<div class="cloud cloudText">' +
						'<div class="cloudPannel">' +
							'<div class="cloudBody">' +
								'<div class="cloudContentShow">'+data+'</div>' +
								'<div class="cloudArrow"></div>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>';*/
	var html = '<div class="TW_box">' +
					'<div class="p10">' + data+  '</div>' +
				'</div>';
	return html;
}

//获取图文视图
ShowSendData.prototype._newsView = function (data){
	var html = '';
	var news_data = data;
	if(news_data.length>1){
		html += '<div class="TW_box">' +
					'<div class="appTwb1">' +
						'<p class="twp">2013-05-15</p>' +
						'<div class="reveal news_first" style="background-image:url(\''+news_data[0]['picurl']+'\')">' +
							'<h5 class="tw_z">' +
								'<a class="z_title" href="javascript:;">'+news_data[0]['title']+'</a>' +
							'</h5>' +
						'</div>' +
					'</div>' +
					'<div class="appTwb2">';
		for(var i=1;i<news_data.length;i++){
			html += 	'<div class="tw_li">' +
							'<a class="atext" href="javascript:;">'+news_data[i]['title']+'</a>' +
							'<img width="70" height="70" src="'+news_data[i]['picurl']+'">' +
						'</div>';
		}
		html +=     '</div>' +
				'</div>';
	}else{
		html += '<div class="TW_box">' +
					'<div class="appTwb1">' +
						'<h3 class="twh3">' +
							'<a href="javascript:;">'+news_data[0]['title']+'</a>' +
						'</h3>' +
						'<p class="twp">2013-05-14</p>' +
						'<div class="reveal news_first" style="background-image:url('+news_data[0]['picurl']+')"></div>' +
					'</div>' +
					'<div class="appTwb2">' +
						'<div class="tw_text">' +
							'<p>'+news_data[0]['description']+'</p>' +
						'</div>' +
					'</div>' +
				'</div>';
	}
	return html;
}