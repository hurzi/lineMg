$(function() {
	// 编辑按钮
	$(document).on('mouseenter','.Cledit', function() {
		$(this).find('.abs').css({
			'left' : '0px'
		});
	});
	$(document).on('mouseleave','.Cledit', function() {
		$(this).find('.abs').css({
			'left' : '100%'
		});
	});
	
	

});
// ///////////////////////////////////////////////////////////////////////小林JS
if (!Abc) {
	var Abc = {};
};

Abc.MessageEditor = function(type, data, param) {
	param = param || {};
	this.data_ = data || new Array();
	if (type != 1 && type != 2) {
		this.type_ = 1;
	} else {
		this.type_ = type;
	}
	// 标题限制字数
	this.title_max = param.title_max || 128;
	// 作者限制字数
	this.author_max = param.author_max || 24;
	// 摘要限制字数
	this.desc_max = param.desc_max || 4000;
	// 当前编辑器的数据下标
	this._index = 0;
	// 上传图片地址
	this.upload_url = param.upload_url
			|| '../Common/UploadImg/Upload.php?callback=Abc.MessageEditor.uploadImg';
	this.makeDefaultData();
	
	this.Ueditor = null;
	
	this.UeditorReadyOk = false;

};
Abc.MessageEditor.EDITOR_ID = 'zw_text';

Abc.MessageEditor.prototype.makeDefaultData = function() {
	/*if (typeof (this.KindEditor) == 'undefined') {
		this.saveDefaultData = true;
		return;
	}*/
	if (! this.UeditorReadyOk) {
		this.saveDefaultData = true;
		return;
	}
	// 添加初始数组
	if (this.type_ == 2) {
		for ( var i = this.data_.length; i < 2; i++) {
			this.saveEditorData(i);
		}
	} else {
		for ( var i = this.data_.length; i < 1; i++) {
			this.saveEditorData(i);
		}
	}
};

Abc.MessageEditor.prototype.setData = function(data) {
	this.initData_ = data || null;
	return this;
};

// 上传图片
Abc.MessageEditor.uploadImg = function(url) {
	$('#img').attr('src', '');
	$('#img').attr('src', url);
	$('#imgArea').show();
};

// 删除表单图片
Abc.MessageEditor.prototype.delImg = function() {
	$('#imgArea').hide();
	$('#img').attr('src', '');
	this.saveEditorData(this._index);
	Abc.preview.changeImg(this.type_, this._index, '');
};

Abc.MessageEditor.prototype.getData = function() {
	this.saveEditorData(this._index);
	return this.data_;
};

Abc.MessageEditor.prototype.show = function(containerId) {
	this.preview = new Abc.preview(this.data_, this.type_, this.title_max,
			this.desc_max);
	var container = containerId ? $("#" + containerId) : $('body');
	// 生成view显示在container中
	this.show_(container);
	return this;
};

// 显示模板
/**
 * @param {string}
 *            container
 * @return Array
 * @private
 */
Abc.MessageEditor.prototype.show_ = function(container) {
	var frame_tpl = this.getFrameView();
	container.html(frame_tpl);
	if (this.type_ == 1) {
		var top_tpl = this.preview.getOneTopView();
		$('.TW_box').prepend(top_tpl);
	} else {
		var top_tpl = this.preview.getMoreTopView();
		var add_tpl = this.preview.getSmallView();
		var button_tpl = this.preview.getAddButtonView();
		$('.TW_box').prepend(top_tpl);
		$('.appTwb2').append(add_tpl);
		$('.appTwb2').append(button_tpl);
	}
	var form_tpl = this.getFormTpl();
	$('.mat_f3_r').append(form_tpl);
	// 初始化kindEditor
	//this.initKindEditor();
	this.initUeditor();
	this.showDataToEditor(0);
	this.initLislen();
};

// 获取框架视图
Abc.MessageEditor.prototype.getFrameView = function() {
	var tpl = '<div class="Mat_con" style="word-wrap: break-word;">'
			+ '<div class="mat_f3">' + '<div class="TW_box">'
			+ '<div class="appTwb2">' + '</div>' + '</div>' + '</div>'
			+ '<div class="mat_f3_r">' + '</div>' + '</div>';
	return tpl;
};

// 监听
Abc.MessageEditor.prototype.initLislen = function() {
	this.lislenTpl();
	this.lislenPreview();
};

// 初始KindEditor
Abc.MessageEditor.prototype.initKindEditor = function() {
	var that = this;
	KindEditor
			.ready(function(K) {
				var param = {
					minWidth : '340px',
					items : [ 'bold', 'italic', 'underline', '|',
							'insertorderedlist', 'insertunorderedlist', '|',
							'image', '|', 'removeformat', 'forecolor',
							'hilitecolor', '/', 'selectall', '|',
							'justifyleft', 'justifycenter', 'justifyright',
							'justifyfull', '|', 'table', '|', 'copy', 'paste',
							'fullscreen' ],
					resizeType : 0,
					uploadJson : 'http://wx.hysci.com.cn/yhsci/Common/UploadImage.php'
				};
				var editor = K.create('#zw_text', param);
				that.KindEditor = editor;
				if (that.saveDefaultData == true) {
					that.makeDefaultData();
				}
				if (that.showData == true) {
					that.showData == false;
					that.showDataToEditor(0);
				}
			});
};

//初始Ueditor
Abc.MessageEditor.prototype.initUeditor = function() {
	var that = this;
	var editorOption = {
            //这里可以选择自己需要的工具按钮名称,此处仅选择如下五个
            toolbars:[['fullscreen', 'bold', 'italic', 'underline', '|', 
                       'insertorderedlist', 'insertunorderedlist', '|',
                       'insertimage', '|',
                       'removeformat', 'forecolor', 'backcolor','|', 'fontsize',
                       'insertvideo']],
            //初始化编辑器的内容,也可以通过textarea/script给值
            initialContent:'',
            //focus时自动清空初始化时的内容
            autoClearinitialContent:false,
            //关闭字数统计
            wordCount:false,
            //是否启用元素路径，默认是显示
            elementPathEnabled:false,
            //autoHeightEnabled
            // 是否自动长高,默认true
            autoHeightEnabled:false
            //更多其他参数，请参考editor_config.js中的配置项
        };
		this.Ueditor = UM.getEditor(Abc.MessageEditor.EDITOR_ID,editorOption);
		this.Ueditor.ready(function () {
			that.UeditorReadyOk = true;
			if (that.saveDefaultData == true) {
				that.makeDefaultData();
			}
			if (that.showData == true) {
				that.showData == false;
				that.showDataToEditor(0);
			}
		});
};

// 获取表单视图
Abc.MessageEditor.prototype.getFormTpl = function() {
	var tpl = '<div class="rel msg-editer-wrapper">'
			//+ '<div class="msg-editer">'
			+ '<form id="saveBusinessForm" class="form-horizontal msg-editer">'
			+ '<label for="" class="block">标题&nbsp;<span style="color:#B8B8B8;">(标题不能超过'
			+ (this.title_max / 2)
			+ '个字)</span></label>'
			+ '<input type="text" class="form-control" id="title" value="">'
			+ '<label for="" class="block">作者&nbsp;<span style="color:#B8B8B8;">(选填,不能超过'
			+ (this.author_max / 2)
			+ '个字)</span></label>'
			+ '<input type="text" class="form-control" id="author" value="">'
			+ '<label for="" class="block">封面图片&nbsp;<span style="color:#B8B8B8;">(效果建议尺寸：大图360*200，小图200*200)</span>'
			+ '</label>'
			+ '<div class="cover-area">'
			+ '<div class="oh z cover-hd">'
			+ ' <iframe id="imgUpload" style="filter:alpha(opacity = 0);-moz-opacity:0;-khtml-opacity:0;opacity:0;position:absolute;top:0;left:0;width:60px;height:30px;" class="uploadfile" src="'
			+ this.upload_url
			+ '"></iframe>'
			+ ' <a href="javascript:;" class="icon28C upload-btn">上传</a>'
			+ '</div>'
			+ '<p style="display: none;" id="imgArea" class="cover-bd">'
			+ '<img width=100 src="" id="img"><a href="javascript:;" class="vb cover-del" id="delImg">删除</a>'
			+ '</p>'
			+ '</div>'
			+ '<label for="" class="block">封图是否显示在正文：'
			+ '<input type="checkbox" class="" style="width:auto;display:inline-block;margin-left:5px;" id="show_cover_pic" value=""></label>'
			+ '<br/><label for="" class="block">摘要&nbsp;<span style="color:#B8B8B8;">(摘要不能超过'
			+ (this.desc_max / 2)
			+ '个字)</span></label>'
			+ '<textarea name="" id="desc" row="4" class="form-control"></textarea>'
			+ '<label for="" class="block">正文&nbsp;<span style="color:#B8B8B8;">(正文不能超过20000个字)</span></label>'
			//+ '<textarea name="" id="zw_text" class="msg-txta" style="height:300px;"></textarea>'
			+ '<div><script type="text/plain" style="width:100%" id="'+Abc.MessageEditor.EDITOR_ID+'"></script></div>'
			+ '<label for="" class="block">链接&nbsp;<span style="color:#B8B8B8;">(必须为http或https开头的链接)</span></label>'
			+ '<input type="text" class="form-control" id="url" value="">'
			+ '<div class="oh z shadow">'
			+ '<span class="left ls"></span>'
			+ '<span class="right rs"></span>'
			+ '</div> '
			+ '<span style="margin-top: 0px;" class="abs msg-arrow a-out"></span>'
			+ '<span style="margin-top: 0px;" class="abs msg-arrow a-in"></span>'
			+ '</form>';
			+ '</div>';
	return tpl;
};

// 监听表单
Abc.MessageEditor.prototype.lislenTpl = function() {
	var _this = this;
	$(document).off('change keyup','#title');
	$(document).on('change keyup','#title',
			function() {
				var title_content = $("#title").val();
				if (_this.len(title_content) <= _this.title_max) {
					Abc.preview.changeTitle(_this.type_, _this._index, $(
							this).val());
					_this.saveEditorData(_this._index);
				} else {
					$("#title").val(_this.data_[_this._index].news_title);
				}
			});
	//$(document).off('load','#img');
	$(document).on('load','#img', function() {
		alert("ddddddddd");
		var img_url = $("#img").attr('src');
		Abc.preview.changeImg(_this.type_, _this._index, '');
		Abc.preview.changeImg(_this.type_, _this._index, img_url);
		_this.saveEditorData(_this._index);
		parentSH(this.height);
	});
	$(document).off('change keyup','#desc');
	$(document).on('change keyup','#desc',
			function() {
				var describe_content = $("#desc").val();
				if (_this.len(describe_content) <= _this.desc_max) {
					Abc.preview.changeDescribe(_this.type_, _this._index,
							describe_content);
					_this.saveEditorData(_this._index);
				} else {
					$("#desc").val(_this.data_[_this._index].news_description);
				}
				parentSH();
			});
	$(document).off('click','#delImg');
	$(document).on('click','#delImg', function() {
		_this.delImg();
	});
};
// 验证输入字数（中英）
Abc.MessageEditor.prototype.len = function(content) {
	return content.replace(/[^\x00-\xff]/g, "**").length; // 将中文认为是两个字，返回2
};
// 监听视图
Abc.MessageEditor.prototype.lislenPreview = function() {
	var _this = this;
	if (this.type_ == 2) {
		$(document).off('click','.sub-add-btn');
		// 添加新news
		$(document).on('click','.sub-add-btn', function() {
			if (_this.preview.news_count >= 10) {
				Common.alert('无法添加，多条图文时，数据最多为10条。');
				return;
			}
			var addView = _this.preview.getAddView();
			$('.tw_li:last').after(addView);
			var top_value = $('.tw_li:last').offset().top;
			_this.preview.news_count++;
			_this.saveEditorData(_this._index);
			// 生成新数组
			var new_array = {
				news_title : '',
				news_author : '',
				news_img_url : '',
				news_show_cover_pic : '',
				news_description : '',
				news_url : '',
				news_content : ''
			};
			_this.data_.push(new_array);
			_this._index = _this.preview.news_count - 1;
			_this.showDataToEditor(_this._index);
			$('.mat_f3_r .msg-editer-wrapper').css({
				'marginTop' : top_value - 188
			}).show();
			parentSH();
		});

		// 删除news
		$(document).off('click','.iconDel');
		$(document).on('click','.iconDel', function() {
			// 保存正在操作的表单的数据
			_this.saveEditorData(_this._index);
			var Pobj = $(this).parents('.tw_li');
			var Lm = $('.appTwb2 .tw_li').length;
			if (Lm <= 1) {
				Common.alert('无法删除，多条图文至少需要2条消息。');
				return;
			}
			var li_index = Pobj.index();
			// li_index+1 为此数据的数组下标
			// 删除数据
			_this.data_.splice(li_index + 1, 1);
			_this._index = 0;
			_this.showDataToEditor(_this._index);
			$('.mat_f3_r .msg-editer-wrapper').css({
				'marginTop' : 0
			});
			Pobj.remove();
			_this.preview.news_count--;
			parentSH();
		});

		// 编辑头条图文
		$(document).off('click','.appTwb1 .iconEdit');
		$(document).on('click','.appTwb1 .iconEdit', function() {
			_this.saveEditorData(_this._index);
			_this._index = 0;
			_this.showDataToEditor(_this._index);
			$('.mat_f3_r .msg-editer-wrapper').css({
				'marginTop' : 0
			});

		});

		// 编辑后面图文
		$(document).off('click','.appTwb2 .iconEdit');
		$(document).on('click','.appTwb2 .iconEdit', function() {
			var Pobj = $(this).parents('.tw_li');
			_this.saveEditorData(_this._index);
			_this._index = (Pobj.index() * 1) + 1;
			_this.showDataToEditor(_this._index);
			var top_value = Pobj.offset().top;
			$('.mat_f3_r .msg-editer-wrapper').css({
				'marginTop' : top_value - 188
			});
			parentSH();

		});
	};
};

// 保存表单数据
Abc.MessageEditor.prototype.saveEditorData = function(index) {
	var title = $('#title').val() || '';
	var author = $('#author').val() || '';
	var img_url = $("#img").attr('src') || '';
	var describe = $('#desc').val() || '';
	//var content = this.KindEditor.html() || '';
	var show_cover_pic = 0;
	if($("#show_cover_pic").prop("checked")){
	    show_cover_pic = 1;
	}
	var content = '';
	if (this.Ueditor) {
		content = this.Ueditor.getContent() || '';
	}
	var url = $('#url').val() || '';
	if (typeof (this.data_[index]) == 'undefined') {
		this.data_[index] = {};
	}
	this.data_[index].news_title = title;
	this.data_[index].news_author = author;
	this.data_[index].news_img_url = img_url;
	this.data_[index].news_show_cover_pic = show_cover_pic;
	this.data_[index].news_description = describe;
	this.data_[index].news_content = content;
	this.data_[index].news_url = url;
};

// 数据显示在表单中
Abc.MessageEditor.prototype.showDataToEditor = function(index) {
	/*if (typeof (this.KindEditor) == 'undefined') {
		this.showData = true;
		return;
	}*/
	if (! this.UeditorReadyOk) {
		this.showData = true;
		return;
	}
	var title = '';
	var author = '';
	var img_url = '';
	var show_cover_pic = '';
	var describe = '';
	var url = '';
	var content = '';
	
	if (typeof (this.data_[index]) != 'undefined') {
		title = this.data_[index].news_title;
		author = this.data_[index].news_author;
		img_url = this.data_[index].news_img_url;
		show_cover_pic = this.data_[index].news_show_cover_pic;
		describe = this.data_[index].news_description;
		url = this.data_[index].news_url;
		content = this.data_[index].news_content || '';
	}
	$('#title').val(title);
	$('#author').val(author);
	if (img_url == '') {
		$('#imgArea').hide();
	} else {
		$('#imgArea').show();
	}
	if(show_cover_pic==1){
	      $("#show_cover_pic").prop("checked",true);
	}else{
	      $("#show_cover_pic").prop("checked",false);
	}
	$('#img').attr('src', img_url);
	$('#desc').val(describe);
	$('#url').val(url);
	//this.KindEditor.html(content);
	
	this.Ueditor.ready(
			Abcbind(
				function(content){this.Ueditor.setContent(content);},
				this,
				content || ''
			)
	);
	/*UE.getEditor(Abc.MessageEditor.EDITOR_ID).ready(function(){
		UE.getEditor(Abc.MessageEditor.EDITOR_ID).setContent(content);
	});*/
};

// 验证参数
Abc.MessageEditor.checkData = function(data) {
	for ( var i = 0; i < data.length; i++) {
		if (data[i]['news_title'] == '') {
			Common.alert('第' + (i + 1) + '条图文的标题不能为空。');
			return false;
		}
		if (data[i]['news_img_url'] == '') {
			Common.alert('第' + (i + 1) + '条图文必须上传一张图片。');
			return false;
		}
		if (!data[i]['news_content'] && data[i]['news_url'] == '') {
			Common.alert('第' + (i + 1) + '条图文的正文或者链接地址不能全部为空。');
			return false;
		}
		var strRegex = "^((https|http)://(.*){3,})";
		var re = new RegExp(strRegex);
		if (data[i]['news_url'] != '' && !re.test(data[i]['news_url'])) {
			Common.alert('第' + (i + 1) + '条图文的链接地址不正确，请检查。');
			return false;
		}
	}
	return true;
};

/**
 * 素材预览类
 */
Abc.preview = function(data, type) {
	this.data = data || [];
	if (type == 1) {
		this.type = 1;
		this.news_count = 1;
	} else if (type == 2) {
		this.type = 2;
		if (this.data.length > 2) {
			this.news_count = this.data.length;
		} else {
			this.news_count = 2;
		}
	} else {
		this.type = 1;
		this.news_count = 1;
	}
	this.index_ = 0;
	this.max_count = 10;
};

// 返回视图
Abc.preview.prototype.getTpl = function() {
	var tpl = this.getTpl_();
	return tpl;
};

/**
 * @name 修改图片样式
 * @param int
 *            img_id 图片ID
 * @param string
 *            url 图片的Url
 */

Abc.preview.changeImg = function(type, img_id, url) {
	type = type || 1;
	if (type == 1) {
		if (url) {
			$('.reveal > p').hide();
			$('.reveal').addClass('news_first');
			$('.reveal').css('background-image', 'url(' + url + ')');
		} else {
			$('.reveal').removeClass('news_first');
			$('.reveal').css('background-image', 'none');
			$('.reveal > p').show();
		}
	} else {
		if (url == '') {
			if (img_id == 0) {
				$('.reveal').removeClass('news_first');
				$('.reveal').css('background-image', 'none');
			} else {
				$('.tw_li:eq(' + (img_id - 1) + ')').find('img').remove();
			}
			$('.default-tip:eq(' + (img_id) + ')').show();
		} else {
			$('.default-tip:eq(' + (img_id) + ')').hide();
			if (img_id == 0) {
				$('.reveal').addClass('news_first');
				$('.reveal').css('background-image', 'url(' + url + ')');
			} else {
				var img_str = '<img width="70" height="70" src="' + url
						+ '" />';
				$('.tw_li:eq(' + (img_id - 1) + ')').append(img_str);
			}
		}
	}
};

/**
 * @name 修改预览标题
 * @param int
 *            title_id 标题ID
 * @param string
 *            title_content 标题内容
 */
Abc.preview.changeTitle = function(type, title_id, title_content) {
	type = type || 1;
	title_content = title_content || '标题';
	if (type == 1) {
		$('.twh3 > a').html(title_content);
	} else {
		if (title_id == 0) {
			$('.z_title').html(title_content);
		} else {
			$('.tw_li:eq(' + (title_id - 1) + ') > p').html(title_content);
		}
	}
};

/**
 * @name 修改描述
 * @param int
 *            describe_id 描述ID
 * @param string
 *            describe_content 描述内容
 */
Abc.preview.changeDescribe = function(type, describe_id, describe_content) {
	type = type || 1;
	if (type == 1) {
		$('.appTwb1 >p:last').html(describe_content);
	}
};

Abc.preview.prototype.getMoreTopView = function() {
	var data = this.data[0] || [];
	return this.getMoreTopView_(data);
};

// 增加多个图文时的顶部视图
Abc.preview.prototype.getMoreTopView_ = function(data) {
	data = data || [];
	var tpl = '<div class="appTwb1">' + '<p class="twp">' + this.getDate()
			+ '</p>';
	if (!data['img_url']) {
		tpl += '<div class="reveal Cledit">';
	} else {
		tpl += '<div class="reveal Cledit news_first" style="background-image:url('
				+ (data.news_img_url || '') + ')">';
	}
	tpl += '<h5 class="tw_z">' + '<div class="z_title">'
			+ (data.news_title || '标题') + '</div>' + '</h5>';
	if (!data['img_url']) {
		tpl += '<p class="default-tip" style="">封面图片</p>';
	} else {
		tpl += '<p class="default-tip" style="display:none;">封面图片</p>';
	}
	tpl += '<ul class="abs tc sub-msg-opr" style="left: 100%;">'
			+ '<li style="  margin-top: 70px;" class="b-dib sub-msg-opr-item">'
			+ '<a href="javascript:;" class="th icon18 iconEdit" data-rid="2">编辑</a>'
			+ '</li>' + '</ul>' + '</div>' + ' </div>';
	return tpl;
};

Abc.preview.prototype.getOneTopView = function() {
	var data = this.data[0] || [];
	return this.getOneTopView_(data);
};

// 获取单条图文模板
Abc.preview.prototype.getOneTopView_ = function(data) {
	data = data || [];
	var tpl = '<div class="appTwb1">'
			+ '<h3 class="twh3"  style="overflow:hidden;max-height:40px;" >'
			+ '<a href="#">' + (data.news_title || '标题') + '</a>' + '</h3>'
			+ '<p class="twp">' + this.getDate() + '</p>';
	if (!data.news_img_url) {
		tpl += '<div class="reveal Cledit">';
		tpl += '<p class="default-tip" style="">封面图片</p>';
	} else {
		tpl += '<div class="reveal Cledit news_first" style="background-image:url('
				+ data.news_img_url + ')">';
		tpl += '<p class="default-tip" style="display:none;">封面图片</p>';
	}
	;
	tpl += '</div>' + '<p class="twp" >' + (data.news_description || '')
			+ '</p>' + '</div>';
	return tpl;
};

Abc.preview.prototype.getSmallView = function() {
	var tpl = '';
	var length = this.data.length;
	if (length < 2) {
		return this.getAddView();
	}
	for ( var i = 1; i < length; i++) {
		tpl += this.getAddView(this.data[i]);
	}
	return tpl;
};

// 获取预览中间增加的视图
Abc.preview.prototype.getAddView = function(data) {
	data = data || {};
	var AddLi = '<div class="tw_li Cledit">'
			+ '<p style="overflow:hidden;max-height:75px;" >'
			+ (data.news_title || '标题') + '</p>';
	if (!data.news_img_url) {
		AddLi += '<span style="" class="default-tip">缩略图</span>';
	} else {
		AddLi += '<span style="display:none" class="default-tip">缩略图</span>';
		AddLi += '<img src="' + data.news_img_url + '" width="70" height="70">';
	}
	AddLi += '<ul class="abs tc sub-msg-opr">'
			+ '<li class="b-dib sub-msg-opr-item">'
			+ '<a data-rid="2" class="th icon18 iconEdit" href="javascript:;">编辑</a>'
			+ '</li>'
			+ '<li class="b-dib sub-msg-opr-item">'
			+ '<a data-rid="2" class="th icon18 iconDel" href="javascript:;">删除</a>'
			+ '</li>' + '</ul>' + '</div>';
	return AddLi;
};

// 获取底部增加按钮视图
Abc.preview.prototype.getAddButtonView = function() {
	var tpl = '<div class="sub-add">'
			+ '<a href="javascript:;" class="sub-add-btn">'
			+ '<span class="vm dib sub-add-icon"></span> 增加一条' + '</a>'
			+ '</div>';
	return tpl;
};

//获取当日前日期
Abc.preview.prototype.getDate = function() {
	var mydate = new Date();
	var str = "" + mydate.getFullYear() + "-";
	str += (mydate.getMonth() + 1) + "-";
	str += mydate.getDate();
	return str;
};