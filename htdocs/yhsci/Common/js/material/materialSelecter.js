/**
 * 素材显示js
 */

var materialSelecter = {
	/**
	 * @param string url ajax传递地址
	 * @param function() ok_callback 确定按钮后置事件
	 * @param function() cancel_callback 取消按钮后置事件
	 */
	show : function(url, param) {
		this.showNews(url, param);
	},
	showNews : function(url, param) {
		if (!url) {
			throw 'url不能为空';
		} else {
			this.news_url = url;
		}
		var news_param_ = {
			ok_callback : param.ok_callback || null,
			cancel_callback : param.cancel_callback || null,
			conw : 700,
			conh : 610,
			newOneNewsUrl : param.newOneNewsUrl || '/Admin/index.php?a=MaterialNews&m=add&news_type=1',
			newManyNewsUrl : param.newManyNewsUrl || '/Admin/index.php?a=MaterialNews&m=add&news_type=2'
		};
		//alert(news_param_['newManyNewsUrl']);
		this.type = 'news';
		this.maptss_load('选择图文素材', news_param_, 'news');
		this.loadNewsData();
		var _this = this;
		this.listen(news_param_);
		$('#rehref').click(function() {
			_this.loadNewsData();
		});
	},
	// ajax加载图文数据
	loadNewsData : function() {
		var _this = this;
		var page = arguments[0] ? arguments[0] : 1;
		var data = {
			p : page,
			callback : 'materialSelecter.loadNewsData'
		};
		Common.request(_this.news_url,data,
				function(msg) {
			var data = msg.data.content;// 内容
			var page = msg.data.page; // 分页
			var content = _this.createNewsTpl(data);
			$(".Mat_con").html(content);
			$('#pageId').html(page);
		});
	},
	// 根据数据生成图文内容
	createNewsTpl : function(data) {
		var contentLeft = '<div class="mat_l">';
		var contentRight = '<div class="mat_r">';
		if (!data) return;
		$.each(data,function(i, obj) {
			var articles = obj.articles;
			var len = articles.length;
			if (len < 1) {
				return;
			}
			// alert(len);
			var tmpStr = '';
			var materialId = obj.id;
			var date = obj.create_time;
			var title = articles[0].title;
			var picurl = articles[0].picurl;
			var desc = articles[0].description;
			//var url = articles[0].url;
			if (len == 1) { // 单条图文
				tmpStr += '<div class="TW_box materialSelecter">';
				tmpStr += '<div class="tw_edit">';
				tmpStr += '<a class="optFor" href="javascript:;" name="' + materialId + '"></a>';
				tmpStr += '</div>';
				tmpStr += '<div class="appTwb1">';
				tmpStr += '<h3 class="twh3 mal_title"><a href="javascript:void(0);">' + title + '</a></h3>';
				tmpStr += '<p class="twp create_time" >' + date + '</p>';
				tmpStr += '<div class="reveal news_first" style="background-image:url(\'' + picurl + '\')">';
				tmpStr += '</div>';
				tmpStr += '</div>';
				tmpStr += '<div class="appTwb2">';
				tmpStr += '<div class="tw_text">';
				tmpStr += '<p>' + desc + '</p>';
				tmpStr += '</div>';
				tmpStr += '</div>';
				tmpStr += '</div>';
			} else {// 多条图文
				tmpStr += '<div class="TW_box materialSelecter">';
				tmpStr += '<div class="tw_edit">';
				tmpStr += '<a class="optFor" href="javascript:;" name="' + materialId + '"></a>';
				tmpStr += '</div>';
				tmpStr += '<div class="appTwb1">';
				tmpStr += '<p class="twp create_time">' + date + '</p>';
				tmpStr += '<div class="reveal news_first" style="background-image:url(\'' + picurl + '\')">';
				tmpStr += '<h5 class="tw_z">';
				tmpStr += '<a class="z_title" href="javascript:void(0);">' + title + '</a>';
				tmpStr += '</h5>';
				tmpStr += '</div>';
				tmpStr += '</div>';
				tmpStr += '<div class="appTwb2">';
				for ( var j = 1; j < len; j++) {
					var title = articles[j].title;
					var picurl = articles[j].picurl;
					tmpStr += '<div class="tw_li">';
					tmpStr += '<a class="atext" href="javascript:void(0);">' + title + '</a>';
					tmpStr += '<img width="70" height="70" src="' + picurl + '" />';
					tmpStr += '</div>';
				}
				tmpStr += '</div>';
				tmpStr += '</div>';
			}
			if (i % 2 == 0) {// 左边内容
				contentLeft += tmpStr;
			} else {// 右边内容
				contentRight += tmpStr;
			}
		});
		contentLeft += '</div>';
		contentRight += '</div>';
		return (contentLeft + contentRight);
	},
	//----------------------------------------------------
	//显示音乐素材
	showMusic : function(url, param) {
		if (!url) {
			throw 'url不能为空';
		} else {
			this.music_url = url;
		}
		var music_param_ = {
			ok_callback : param.ok_callback || null,
			cancel_callback : param.cancel_callback || null,
			conw : 700,
			conh : 610,
			newOneNewsUrl : param.newOneNewsUrl || '/Admin/index.php?a=MaterialMusic&m=add',
			newManyNewsUrl : param.newManyNewsUrl || null
		};
		this.type = 'music';
		this.maptss_load('选择音乐素材', music_param_, 'music');
		this.loadMusicData();
		var _this = this;
		this.listen(music_param_);
		$('#rehref').click(function() {
			_this.loadMusicData();
		});
	},
	// ajax加载音乐数据
	loadMusicData : function() {
		var _this = this;
		var page = arguments[0] ? arguments[0] : 1;
		var data = {
			p : page,
			callback : 'materialSelecter.loadMusicData'
		};
		Common.request(_this.music_url,data,
			function(msg) {
				var data = msg.data.content;// 内容
				var page = msg.data.page; // 分页
				var content = _this.createMusicTpl(data);
				$(".Mat_con").html(content);
				$('#pageId').html(page);
			}
		);
	},
	// 创建音乐皮肤
	createMusicTpl : function(data) {
		var contentLeft = '<div class="mat_l">';
		var contentRight = '<div class="mat_r">';
		if (!data)
			return;
		$.each(data, function(i, obj) {
			var materialId = obj.id;
			var title = obj.title;
			var create_time = obj.create_time;
			var music_url = obj.articles.music_url;
			if (obj.articles.hq_music_url) {
				music_url = obj.articles.hq_music_url;
			}
			var thumb_url = obj.articles.thumb_url;
			var description = obj.articles.description;
			var tmpStr = '';
			tmpStr += '<div class="TW_box materialSelecter">';
			tmpStr += '<div class="tw_edit">';
			tmpStr += '<a class="optFor" href="javascript:;" name="' + materialId + '"></a>';
			tmpStr += '</div>';
			tmpStr += '<div class="appTwb1">';
			tmpStr += '<p class="twp create_time">' + create_time + '</p>';
			tmpStr += '<div style="overflow:hidden;" class="con_Ivredit">';
			tmpStr += '<div style="height: 100px;width: 100px;float:left;overflow:hidden;" class="twp">';
			tmpStr += '<img width="100" height="100" src="' + thumb_url + '" style="position:absolute;" id="img">';
			tmpStr += '<div style="position:absolute;left:10px;top:35px;">';
			tmpStr += '<div id="jquery_jplayer_' + materialId + '" class="jp-jplayer"></div>';
			tmpStr += '<div onclick="JPlayer(\'' + materialId + '\',\'' + music_url + '\');" id="jp_container_' + materialId + '" class="jp-audio">';
			tmpStr += '<a href="javascript:;" class="jp-play audioImgBarBtn audioPlayBtn" tabindex="1"></a>';
			tmpStr += '<a href="javascript:;" class="jp-pause audioImgBarBtn audioStopBtn" style="display:none;" tabindex="1"></a>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '<div style="float:left;width:180px; max-width:none;" class="twp">';
			tmpStr += '<h3>';
			tmpStr += '<a href="javascript:void(0);">' + title + '</a>';
			tmpStr += '</h3>';
			tmpStr += '<p>' + description + '</p>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			if (i % 2 == 0) {// 左边内容
				contentLeft += tmpStr;
			} else {// 右边内容
				contentRight += tmpStr;
			}
		});
		contentLeft += '</div>';
		contentRight += '</div>';
		return (contentLeft + contentRight);
	},
	// ---------------------------------------------------
	// 显示图片
	showImage : function(url, param) {
		if (!url) {
			throw 'url不能为空';
		} else {
			this.image_url = url;
		}
		var image_param_ = {
			ok_callback : param.ok_callback || null,
			cancel_callback : param.cancel_callback || null,
			conw : 700,
			conh : 610,
			newOneNewsUrl : param.newOneNewsUrl || '/Admin/index.php?a=MaterialImage&m=add',
			newManyNewsUrl : param.newManyNewsUrl || null
		};
		this.type = 'image';
		this.maptss_load('选择图片素材', image_param_, 'image');
		this.loadImageData();
		var _this = this;
		this.listen(image_param_);
		$('#rehref').click(function() {
			_this.loadImageData();
		});
	},
	// ajax加载图片数据
	loadImageData : function() {
		var _this = this;
		var page = arguments[0] ? arguments[0] : 1;
		var data = {
			p : page,
			callback : 'materialSelecter.loadImagesData'
		};
		Common.request(_this.image_url,data,
			function(msg) {
				var data = msg.data.content;// 内容
				var page = msg.data.page; // 分页
				var content = _this.createImageTpl(data);
				$(".Mat_con").html(content);
				$('#pageId').html(page);
			}
		);
	},
	// 初始化模板
	initNewsPopLayer : function(type, param) {
		var html = '';
		html += '<div class="Tccm">';
		html += '<div class="con_c_t">';
		html += '<div class="con_bzk" style=" border:none; background:#fff; margin-bottom:0px;">';
		html += '<div>';
		switch (type) {
			case 'news':
				//html += '<a href="' + param.newOneNewsUrl + '" target="_blank" class="button green medium">新建单图文</a>';
				//html += '<a href="' + param.newManyNewsUrl + '" target="_blank" class="button green medium">新建多图文</a>';
				break;
			case 'image':
				html += '<a href="' + param.newOneNewsUrl + '" target="_blank" class="button green medium">新建图片素材</a>';
				break;
			case 'music':
				html += '<a href="' + param.newOneNewsUrl + '" target="_blank" class="button green medium">新建音乐素材</a>';
				break;
			case 'video':
				html += '<a href="' + param.newOneNewsUrl + '" target="_blank" class="button green medium">新建视频素材</a>';
				break;
			case 'voice':
				html += '<a href="' + param.newOneNewsUrl + '" target="_blank" class="button green medium">新建音频素材</a>';
				break;
			default:
				alert('error type');
				return;
			}
		html += '<div style="float:right">';
		html += '<a href="javascript:;" id="rehref" title="刷新" class="refresh" style="float:right"></a>';
		html += '<div id="pageId" style="display:inline-block;"></div>';
		html += '</div>';
		html += '</div> ';
		html += '</div> ';
		html += '</div> ';
		html += '<div class="Mat_con" style=" border:none; margin-top:5px; height:455px; overflow-x:hidden; overflow-y:auto;">';
		html += '</div>';
//		html += '<div class="mat_f">';
//		html += '<a href="javascript:;" class="button green medium AddOFs" >确定</a>';
//		html += '<a href="javascript:;" class="button green medium ML_close">取消</a>';
//		html += '</div>';
		html += '</div>';
		return html;
	},
	createImageTpl : function(data) {
		var contentLeft = '<div class="mat_l">';
		var contentRight = '<div class="mat_r">';
		if (!data)
			return;
		$.each(data, function(i, obj) {
			var materialId = obj.id;
			var title = obj.title;
			var create_time = obj.create_time;
			var img_url = obj.media_url;
			var tmpStr = '';
			tmpStr += '<div class="TW_box materialSelecter">';
			tmpStr += '<div class="tw_edit">';
			tmpStr += '<a class="optFor" href="javascript:;" name="' + materialId + '"></a>';
			tmpStr += '</div>';
			tmpStr += '<div class="appTwb1">';
			tmpStr += '<h3 class="twh3">';
			tmpStr += '<a href="javascript:void(0);">' + title + '</a>';
			tmpStr += '</h3>';
			tmpStr += '<p class="twp create_time">' + create_time + '</p>';
			tmpStr += '<div class="reveal news_first" style="height:auto;">';
			tmpStr += '<img src=\"' + img_url + '" >';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			if (i % 2 == 0) {// 左边内容
				contentLeft += tmpStr;
			} else {// 右边内容
				contentRight += tmpStr;
			}
		});
		contentLeft += ' </div>';
		contentRight += ' </div>';
		return (contentLeft + contentRight);
	},
	// -----------------------------------------------------------------------------------------------
	// 显示语音素材
	showVoice : function(url, param) {
		if (!url) {
			throw 'url不能为空';
		} else {
			this.voice_url = url;
		}
		var voice_param_ = {
			ok_callback : param.ok_callback || null,
			cancel_callback : param.cancel_callback || null,
			conw : 700,
			conh : 610,
			newOneNewsUrl : param.newOneNewsUrl
					|| '/Admin/index.php?a=MaterialVoice&m=add',
			newManyNewsUrl : param.newManyNewsUrl || null
		};
		this.type = 'voice';
		this.maptss_load('选择语音素材', voice_param_, 'voice');
		this.loadVoiceData();
		var _this = this;
		this.listen(voice_param_);
		$('#rehref').click(function() {
			_this.loadVoiceData();
		});
	},
	// ajax加载语音数据
	loadVoiceData : function() {
		var _this = this;
		var page = arguments[0] ? arguments[0] : 1;
		var data = {
			p : page,
			callback : 'materialSelecter.loadVoiceData'
		};
		Common.request(_this.voice_url,data,
			function(msg) {
				var data = msg.data.content;// 内容
				var page = msg.data.page; // 分页
				var content = _this.createVoiceTpl(data);
				$(".Mat_con").html(content);
				$('#pageId').html(page);
			}
		);
	},
	// 创建语音皮肤
	createVoiceTpl : function(data) {
		var contentLeft = '<div class="mat_l">';
		var contentRight = '<div class="mat_r">';
		if (!data) return;
		$.each(data, function(i, obj) {
			var materialId = obj.id;
			var title = obj.title;
			var create_time = obj.create_time;
			var video_url = obj.media_url;
			var tmpStr = '';
			tmpStr += '<div class="TW_box materialSelecter">';
			tmpStr += '<div class="tw_edit">';
			tmpStr += '<a class="optFor" href="javascript:;" name="' + materialId + '"></a>';
			tmpStr += '</div>';
			tmpStr += '<div class="appTwb1">';
			tmpStr += '<h3 class="twh3">';
			tmpStr += '<a href="javascript:void(0);">' + title + '</a>';
			tmpStr += '</h3>';
			tmpStr += '<p class="twp create_time">' + create_time + '</p>';
			tmpStr += '<div class="dhLb he">';
			tmpStr += '<div class="cloud cloudText">';
			tmpStr += '<div class="cloudPannel">';
			tmpStr += '<div style="width:290px;" class="cloudBody">';
			tmpStr += '<div class="cloudContent">';
			tmpStr += '<div id="jquery_jplayer_' + materialId + '" class="jp-jplayer"></div>';
			tmpStr += "<div onclick='JPlayer(\"" + materialId + "\",\"" + video_url + "\")' id='jp_container_"
					+ materialId + "' class='jp-audio' style='background-color: #B2CF73;width:270px;'>";
			tmpStr += '<a href="javascript:;" class="jp-play" tabindex="1"></a>';
			tmpStr += '<a href="javascript:;" class="jp-pause" style="display:none;" tabindex="1"></a>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '<div class="cloudArrow"></div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			if (i % 2 == 0) {//左边内容	
				contentLeft += tmpStr;
			} else {//右边内容
				contentRight += tmpStr;
			}
		});
		contentLeft += ' </div>';
		contentRight += ' </div>';
		return (contentLeft + contentRight);
	},
	// -----------------------------------------------------------
	// 显示视频素材
	showVideo : function(url, param) {
		if (!url) {
			throw 'url不能为空';
		} else {
			this.video_url = url;
		}
		var video_param_ = {
			ok_callback : param.ok_callback || null,
			cancel_callback : param.cancel_callback || null,
			conw : 700,
			conh : 610,
			newOneNewsUrl : param.newOneNewsUrl
					|| '/Admin/index.php?a=MaterialVideo&m=add',
			newManyNewsUrl : param.newManyNewsUrl || null
		};
		this.type = 'video';
		this.maptss_load('选择视频素材', video_param_, 'video');
		this.loadVideoData();
		var _this = this;
		this.listen(video_param_);
		$('#rehref').click(function() {
			_this.loadVideoData();
		});
	},
	// ajax加载视频数据
	loadVideoData : function() {
		var _this = this;
		var page = arguments[0] ? arguments[0] : 1;
		var data = {
			p : page,
			callback : 'materialSelecter.loadVideoData'
		};
		Common.request(_this.video_url,data,
			function(msg) {
				var data = msg.data.content;// 内容
				var page = msg.data.page; // 分页
				var rand_num = Math.ceil(Math.random() * 1000);
				var content = _this.createVideoTpl(data, rand_num);
				$(".Mat_con").html(content);
				$('#pageId').html(page);
				$.each(data, function(i, obj) {
					var materialId = obj.id;
					var video_url = obj.media_url;
					_this.ckplayer(materialId, video_url, rand_num);
				});
			}
		);
	},
	// 创建视频皮肤
	createVideoTpl : function(data, rand_num) {
		var contentLeft = '<div class="mat_l">';
		var contentRight = '<div class="mat_r">';
		if (!data)
			return;
		$.each(data, function(i, obj) {
			var materialId = obj.id;
			var title = obj.title;
			var create_time = obj.create_time;
			var description = obj.description ? obj.description : '';
			//var video_url = obj.media_url;
			var tmpStr = '';
			tmpStr += '<div class="TW_box materialSelecter">';
			tmpStr += '<div class="tw_edit">';
			tmpStr += '<a class="optFor" href="javascript:;" name="' + materialId + '"></a>';
			tmpStr += '</div>';
			tmpStr += '<div style="margin-left:8px;margin-bottom:10px;" class="appTwb1">';
			tmpStr += '<h3 style="margin-left:0px;" class="twh3">';
			tmpStr += '<a href="javascript:void(0);">' + title + '</a>';
			tmpStr += '</h3>';
			tmpStr += '<p style="margin-left:0px;" class="twp create_time">' + create_time + '</p>';
			tmpStr += '<div id="video_' + rand_num + '_' + materialId + '" style="position:relative;z-index: 100;">';
			tmpStr += '<div id="a' + rand_num + '_' + materialId + '"></div></div>';
			tmpStr += '</div>';
			tmpStr += '<div class="appTwb2">';
			tmpStr += '<div style="padding-left:10px;min-height: 50px;">';
			tmpStr += '<p>' + description + '</p>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			tmpStr += '</div>';
			if (i % 2 == 0) {// 左边内容
				contentLeft += tmpStr;
			} else {// 右边内容
				contentRight += tmpStr;
			}
		});
		contentLeft += ' </div>';
		contentRight += ' </div>';
		return (contentLeft + contentRight);
	},
	ckplayer : function(id, media_url, rand_num) {
		var flashvars = {
			f : media_url,
			c : 0,
			b : 1
		};
		var params = {
			bgcolor : '#000',
			allowFullScreen : false,
			allowScriptAccess : 'always'
		};
		var attributes = {
			id : 'a' + id,
			name : 'ckplayer_a' + id
		};
		CKobject.embedSWF('./Admin/Public_1/cj/ckplayer/ckplayer.swf?v=2.1', 'a'
				+ rand_num + '_' + id, 'ckplayer_a' + rand_num + '_' + id,
				'300', '250', flashvars, params, attributes);
	},
	// Load 弹出层
	maptss_load : function(title, param, type) {
		var _this = this;
		var initHtml = this.initNewsPopLayer(type, param);
		if (!param.conw) {
			param.conw = 520;
		}

		if (!param.conh) {
			param.conh = 500;
		}
		var scrollT= $(window.top.document).scrollTop();
		var wheight = $('body').height() - scrollT-60;
		if ( wheight < param.conh) {
			parentSH(param.conh - wheight + 40);
		}
		bootbox.dialog({
			"message":initHtml,  //内容
			"title":title,		//标题
			"onEscape":function(){},  //退出时事件
			"show":true,  //是否显示此dialog,
			"closeButton" : true, //是否显示关闭按钮，默认true
			"animate":true,//是否动画弹出dialog，IE10以下版本不支持
			"className":"materialSelecter_dialog",  //dialog的类名通过此可改变高宽
			"buttons":{
				"success" :{
					label: "确定",
					className: "btn-success AddOFs",
					callback: Abcbind(_this.chooseOk,_this,param)
				},
				"cancel" :{
					label: "取消",
					className: "btn-cancel ML_close",
					callback: param.cancel_callback
				}
			}			
		});
		$(".materialSelecter_dialog").draggable({handle: ".modal-header"});
//		new jsbox({
//			onlyid : "materialSelecter",
//			title : title,
//			conw : param.conw,
//			conh : param.conh,
//			FixedTop : scrollT + 60,
//			content : initHtml,
//			range : true,
//			mack:true
//		}).show();
	},
	chooseOk : function(param){
		var param_ = param;
		var _this = this;
		var choosedId = $('.OFs').attr('name');
		if (!choosedId) {
			Common.alert('请选择素材');
			return false;
		}
		var obj = $('.OFs').parents('.TW_box');
		$('#materialSelecter .jsbox_close').click();
		if (param_.ok_callback) {
			var obj_clone = obj.clone();
			var date = new Date();
			date = date.Format("yyyy-MM-dd");
			obj_clone.removeClass('materialSelecter');
			obj_clone.css({
				'background-color' : '',
				'width' : '320px'
			});
			obj_clone.find('.tw_edit').remove();
			switch (_this.type) {
				case 'news':
					if (obj_clone.find('.appTwb1 .twh3').length) {
						obj_clone.find('.create_time').html(date);
					} else {
						obj_clone.find('.create_time').remove();
					}
					break;
				case 'music':
					obj_clone.find('.create_time').remove();
					obj_clone.find('.appTwb1 .twh3').remove();
					obj_clone.find('.jp-audio .jp-play').css({"margin": "2px 0 0 30px"});
					obj_clone.find('.jp-audio .jp-pause').css({"margin": "2px 0 0 30px"});
					break;
				case 'image':
					obj_clone.find('.create_time').remove();
					//obj_clone.find('.appTwb1 .twh3').remove();
					break;
				case 'voice':
					obj_clone.find('.create_time').remove();
					//obj_clone.find('.appTwb1 .twh3').remove();
					break;
				case 'video':
					//obj_clone.find('.create_time').remove();
					//obj_clone.find('.appTwb1 .twh3').remove();
					if (obj_clone.find('.appTwb1 .twh3').length) {
						obj_clone.find('.create_time').html(date);
					} else {
						obj_clone.find('.create_time').remove();
					}
					obj_clone.find('.appTwb1').css({"margin": "10px"});
					break;
			}
			param_.ok_callback.call(null, choosedId, obj_clone, _this.type);
		}
	},
	listen : function(param) {
		$(document).off('click','.materialSelecter');
		// 点击DIV选择素材
		$(document).on('click','.materialSelecter', function() {
			$(".TW_box").css({
				'background-color' : ''
			});
			var m = $(this).find('a').eq(0).attr('class');
			if (m == "optFor") {
				$('.optFor').removeClass('OFs');
				$(this).find('a').eq(0).addClass('OFs');
				$(this).css({
					'background-color' : '#BBBBBB'
				});
			} else {
				$(this).find('a').eq(0).attr('class', 'optFor');
			}
		});
		
	}
};