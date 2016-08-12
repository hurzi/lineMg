$(function() {
	$('.qfText').val('');
	//文本素材|图文素材 tab切换功能
	$('.nav2>.tab>li').live('click', function() {
		var index = $('.nav2>.tab>li').index(this);
		if (index == 0) {
			changeShowType(index);
		} else if (index == 1) {
			//maptss_laod('选择图文素材',getMaterialNewsUrl,700,620);
			var param = {
				ok_callback : newsOkButon,
				height : 630
			};
			materialSelecter.show(getMaterialNewsUrl, param);
		}
	});

	//备注提示		 	
	$('.nav1').TabNav('', function(index) {
		var type = (0 == index) ? 'condition' : 'users';
		$('#type').val(type);
	});
	//$('.nav2').TabNav();

	//按条件搜索客户
	$('#condition_button').click(function() {
		searchUserByCond();
	});

	//特定客户搜索客户
	$('#search_button').click(function() {
		searchUserByName();
	});

	//发送消息
	$('#send_msg').click(function() {
		sendMessage();
	});

	//选择|取消用户
	$('.u_choose li').live('click', function() {
		var openid = $(this).attr('id');
		var username = $(this).text();
		var obj = {
			openid : openid,
			username : username
		};
		chooseUser(obj);
	});

	$('.zztag a.qrtcc').live('click', function() {
		var openid = $(this).parent().attr('id');
		var obj = {
			openid : openid
		};
		cancelUser(obj);
	});

	//初始化地域
	initProvince();
	$('#province').live('change', function() {
		var provinceName = $(this).val();
		loadCity(provinceName);
	});
});

//素材确定按钮回调函数
function newsOkButon(materialId, newsHtml) {
	var materialId = materialId || 0;
	var newsHtml = newsHtml || '';
	newsHtml.find('.tw_edit').remove();

	//赋值操作 			
	$('#material_id').val(materialId);
	$('#msg_type').val('news');
	changeShowType(1);

	var con = $('.Ofs_con').html(newsHtml);
	parentSH();
}

//发送消息
function sendMessage() {
	var type = $('#type').val();
	var msgType = $('#msg_type').val();
	var content = $.trim($('#content').val());
	var material_id = $('#material_id').val();
	var is_monitor = $('#is_monitor').is(':checked');
	if(is_monitor){
		is_monitor = 1
	}else{
		is_monitor = 0;	
	}
	var userTotal = 0;
	var sex = '';
	var gid = '';
	var country = '';
	var province = '';
	var city = '';
	var users = [];
	var keyword = '';
	var templateData = {};
	var keywordRuleType = 0;
	var keywordRuleContent = '';
	var camId = $('#camId').val();
	var sendCond     = $('#hidByUsers').html();
	if(type == 'condition'){
		sendCond = $('#hidByCondition').html();
	}	
	if ('text' == msgType) {
		if ('' == content) {
			loadMack({
				off : 'on',
				Limg : 0,
				text : '文字内容不能为空',
				set : 1000
			});
			return false;
		}
	} else if ('news' == msgType) {
		if (0 == material_id) {
			loadMack({
				off : 'on',
				Limg : 0,
				text : '请选择图文素材',
				set : 1000
			});
			return false;
		}
	} else if ('template' == msgType) {
		if (0 == material_id) {
			loadMack({
				off : 'on',
				Limg : 0,
				text : '请选择模板素材',
				set : 1000
			});
			return false;
		}
		
		var fields = $('#template_edit_form_' + material_id).serializeArray();
		var emptyNum = 0;
		var fieldsNum = 0;
		$.each(fields, function (i, field){
			fieldsNum = i + 1;
			var key = field.name;
			var value = field.value;
			templateData[key] = value;
			if (! value || '' == value) {
				emptyNum ++ ;
			}
		});
		
		if (emptyNum == fieldsNum) {
			loadMack({
				off : 'on',
				Limg : 0,
				text : '请至少填写一项内容',
				set : 1000
			});
			return false;
		}
		 keywordRuleType = $('input[name="keywordOpera"]:checked').val();
		var keywordCam   = $.trim($('#keywordCam').val());
		var keywordRule  = $('#keywordRule').val();
		 keywordRuleContent = null;
		
		var text = '';
		if(keywordRuleType == 1 && keywordCam == ''){
			text = '请输入关键词';
		}else if(keywordRuleType == 2 && keywordRule == 0){
			text = '请选择关键词规则';
		}
		if(text){
			loadMack({
				off : 'on',
				Limg : 0,
				text : text,
				set : 1000
			});
			return false;
		}		
		keywordRuleContent = keywordRuleType == 1 ? keywordCam : keywordRule;
	}
	content = Face.filterFace(content);
	var params = {
		is_monitor:is_monitor,
		type : type,
		msg_type : msgType,
		content : content,
		material_id : material_id,
		template_data : templateData,
		sendCond	  : sendCond,
		keywordRuleType   : keywordRuleType,
		keywordRuleContent : keywordRuleContent,
		camId	: camId
		
	};
	
	switch (type) {
		case 'condition':
			sex = $('#sex').val();
			gid = $('#gid-combotree').combotree("getValue");
			province = $('#province').val();
			city = $('#city').val();
			userTotal = parseInt($('#user_total').val());
			keyword = $('#keyword').val();
			if ('template' == msgType) {
				if (gid < 0 && sex < 0 && '' == province && '' == keyword) {
					loadMack({
						off : 'on',
						Limg : 0,
						text : '请至少选择一项条件',
						set : 1000
					});
					return false;
				}
			}
			params.sex = sex;
			params.gid = gid;
			params.country = country;
			params.province = province;
			params.city = city;
			params.keyword = keyword;
			break;
		case 'users':
			userTotal = $('#u_choosed .zztag').length;
			$('#u_choosed .zztag').each(function() {
				users.push($(this).attr('id'));
			});
			params.users = users;
			break;
		default:
			loadMack({
				off : 'on',
				Limg : 0,
				text : '无效的发送类型',
				set : 1500
			});
			return false;
	}

	if (userTotal < 0 || !userTotal) {
		loadMack({
			off : 'on',
			Limg : 0,
			text : '请选择客户',
			set : 1000
		});
		return false;
	}
	if(window['ajaxReq'] == true) return;
	window['ajaxReq'] = true;
	
	loadMack({off:'on',text:'发送中...'});
	$.post(sendUrl, params, function(result) {
		window['ajaxReq'] = false;
		loadMack({off:'off'});
		result = eval("(" + result + ")");
		jsAlert(result.msg,function(){
			if(result.error == 0){
				window.location.href = sendSuccessUrl;
			}
		});
		/*loadMack({
			off : 'on',
			Limg : 0,
			text : result.msg,
			set : 1500
		});*/
	});
}
//根据客服名称搜索客户
function searchUserByName() {
	var nickname = $('#nickname').val();
	var msgType = $('#msg_type').val();
	var type = 'users';
	var total = $('#users_footer').find('b').text();
	loadMack({
		off : 'on'
	});
	var params = {
		type : type,
		nickname : nickname,
		msg_type : msgType
	}
	$.post(
		getDataUrl,
		params,
		function(result) {
			loadMack({
				off : 'off'
			});
			result = eval("(" + result + ")");
			if (result.error != 0) {
				loadMack({
					off : 'on',
					Limg : 0,
					text : result.msg,
					set : 1500
				});
				return false;
			}
			if (result.data) {
				var list = result.data.list;
				//var total = result.data.total;
				//var total = 0;
				var liHtml = '';
				var users = [];
				$('#u_choosed .zztag').each(function() {
					users.push($(this).attr('id'));
				});
				$.each(list,function(k, v) {
					if (in_array(v.user, users)) {
						liHtml += '<li id="'+ v.user + '" style="background-color: lightgrey;">';
						liHtml += '<a href="javascript:;">';
						if (v.remark) {
							liHtml += v.remark + '(' + v.nickname + ')';
						} else {
							liHtml += v.nickname ? v.nickname :'<span class="hong">(昵称为空)</span>';
						}
						liHtml += '</a></li>';
					} else {
						liHtml += '<li id="'+ v.user + '"><a href="javascript:;">';
						if (v.remark) {
							liHtml += v.remark + '(' + v.nickname + ')';
						} else {
							liHtml += v.nickname ? v.nickname :'<span class="hong">(昵称为空)</span>';
						}
						liHtml += '</a></li>';
					}
				});
				$('#user_list').html(liHtml);
				var prompt = '<p>已选择<b>' + total + '</b>人</p>';
				$('#users_footer').html(prompt);
				$('#hidByUsers').html('按用户发送');
			}
		});
}
//根据特定条件搜索客户
function searchUserByCond() {
	var sex = $('#sex').val();
	var gid = $('#gid-combotree').combotree("getValue");
	var province = $('#province').val();
	var city = $('#city').val();

	//var gName = $('#gid option:selected').text().replace(/\((\d+)\)$/, '');
	var gName = $('#gid-combotree').combotree("getText").replace(/\((\d+)\)$/, '');
	var sexName = $('#sex option:selected').text();
	var keyword = $('#keyword').val();
	
	var msgType = $('#msg_type').val();
	
	if ('template' == msgType) {
		if (gid < 0 && sex < 0 && '' == province && '' == keyword) {
			loadMack({
				off : 'on',
				Limg : 0,
				text : '请至少选择一项条件',
				set : 1000
			});
			return false;
		}
	}

	var type = 'condition';
	var params = {
		sex : sex,
		gid : gid,
		province : province,
		city : city,
		type : type,
		keyword: keyword,
		msg_type : msgType
	};
	loadMack({
		off : 'on'
	});
	$.post(getDataUrl, params, function(result) {
		loadMack({
			off : 'off'
		});
		result = eval("(" + result + ")");
		if (result.error != 0) {
			loadMack({
				off : 'on',
				Limg : 0,
				text : result.msg,
				set : 1500
			});
			return false;
		}
		var userTotal = parseInt(result.data);
		if (isNaN(userTotal)) {
			userTotal = 0;
		} else if (userTotal < 0) {
			userTotal = 0;
		}
		$('#user_total').val(userTotal);
		$('#type').val(type);
		var condHtml = '';
if (userTotal > 0) {
			
			if (gid > -1) {
				condHtml += '&nbsp;' + gName;
			}
			if (sex > -1) {
				condHtml += '&nbsp;' + sexName;
			}
			if ('' != province) {
				condHtml += '&nbsp;' + province;
			}
			if ('' != city && '' != province) {
				condHtml += '&nbsp;' + city;
			}
			
			if ('' != keyword) {
				condHtml += '&nbsp;' + keyword;
			}
		
			condHtml = '选择条件：' + (condHtml ? condHtml : '&nbsp;全部');
}

		var prompt = '<p>已选择<b>' + userTotal + '</b>人&nbsp;&nbsp;' + condHtml + '</p>';
		$('#condition_footer').html(prompt);
		$('#hidByCondition').html('按'+condHtml+' 发送');
		parentSH();
	});
}
//切换显示文本|图文
function changeShowType(type) {
	var _this = $('.nav2');
	_this.find('.tab li').removeClass('tab_xz').eq(type).addClass('tab_xz');
	var m = type;
	_this.find('.n_content_all').hide();
	_this.find('.n_content_all:eq(' + m + ')').show();
	if (type == 0) {
		$('.Ofs_con').html('');
		$('#msg_type').val('text');
	}
}

//Load 弹出层
function maptss_laod(title, url, conw, conh) {
	var initHtml = initNewsPopLayer();
	if (!conw) {
		conw = 500
	}
	;
	if (!conh) {
		conh = 500
	}
	;
	var wb = new jsbox({
		onlyid : "maptss",
		title : title,
		conw : conw,
		conh : conh,
		FixedTop : 60,
		content : initHtml,
		range : true
	}).show();

	loadNewsData();
}
//ajax加载弹出层数据
function loadNewsData() {
	var page = arguments[0] ? arguments[0] : 1;
	var data = {
		p : page,
		callback : 'loadNewsData'
	};
	$.ajax({
		url : getMaterialNewsUrl,
		type : 'get',
		data : data,
		dataType : 'json',
		beforeSend : function() {
			loadMack({
				off : 'on'
			})
		},
		complete : function() {
			loadMack({
				off : 'off'
			});
		},
		success : function(msg) {
			var data = msg.content;//内容
			var page = msg.page; //分页
			var content = createNewsTpl(data);
			$(".Mat_con").html(content);
			$('#pageId').html(page);
		}
	});
}
//选中|取消用户
function chooseUser(obj) {
	var username = $.trim(obj.username);
	var openid = obj.openid;
	var userTotal = $('#u_choosed .zztag').length;
	var children = $('#u_choosed').children('#' + openid);
	if (children.length > 0) {
		return true;
	}
	if (userTotal >= 20) {
		loadMack({
			off : 'on',
			Limg : 0,
			text : '最多只能选择20人',
			set : 1500
		});
		return false;
	} else {
		$(".u_choose li[id=" + openid + "]").css('background-color',
				'lightgrey');
		var addUserHtml = '<div class="zztag" id="' + openid + '">'
				+ '<a href="javascript:void(0);" class="a1">' + username
				+ '</a>' + '<a href="javascript:;" class="a2 qrtcc">&nbsp;</a>'
				+ '</div>';

		$('#u_choosed').append(addUserHtml);

		userTotal++;
		var prompt = '<p>已选择<b>' + userTotal + '</b>人</p>';
		$('#users_footer').html(prompt);
	}
	parentSH();
}
function cancelUser(obj) {
	var openid = obj.openid;
	var children = $('#u_choosed').children('#' + openid);
	if (children.length <= 0) {
		return false;
	}
	var userTotal = $('#u_choosed .zztag').length;
	userTotal--;
	children.remove();
	$(".u_choose li[id=" + openid + "]").removeAttr('style');
	var prompt = '<p>已选择<b>' + (userTotal >= 0 ? userTotal : 0) + '</b>人</p>';
	$('#users_footer').html(prompt);
}
/**
 * 初始化省份列表
 **/
function initProvince() {
	var provinceArr = getProvince();
	var optionHtml = '';
	$.each(provinceArr, function(i, name) {
		optionHtml += "<option value='" + name + "'>" + name + "</option>";
	});
	$('#province').append(optionHtml);
}
function loadCity(provinceName) {
	var cityArr = getCityByProvinceName(provinceName);
	var optionHtml = '<option value="">全部</option>	';
	$.each(cityArr, function(i, name) {
		optionHtml += "<option value='" + name + "'>" + name + "</option>";
	});
	$('#city').html(optionHtml);
}
//初始化弹出模板
function initNewsPopLayer() {
	var html = '';
	html += ' <div class="Tccm">';
	html += '	<div class="con_c_t">';
	html += ' 		<div class="con_bzk" style=" border:none; background:#fff; margin-bottom:0px;">';
	html += '			 <div style="padding:10px;">';
	html += ' 				<a href="' + newOneNewsUrl
			+ '" target="mainFrame" class="button green medium">新建单图文</a>';
	html += '				<a href="' + newManyNewsUrl
			+ '" target="mainFrame" class="button green medium">新建多图文</a>';
	html += '				<div id="pageId"  style="float:right">';
	html += ' 				</div>';
	html += ' 			</div> ';
	html += ' 		</div> ';
	html += ' 	</div> ';
	html += ' <div class="Mat_con" style=" border:none; margin-top:5px; height:475px; overflow-x:hidden; overflow-y:auto;">';
	html += '</div>';
	html += ' <div class="mat_f">';
	html += ' 		<a href="javascript:;" class="button green medium AddOFs">确定</a>';
	html += '		<a href="javascript:;" class="button green medium ML_close">取消</a>';
	html += '</div>';
	html += ' </div>';
	return html;
}

//根据数据生成图文内容
function createNewsTpl(data) {
	var contentLeft = '<div class="mat_l">';
	var contentRight = '<div class="mat_r">';
	if (!data)
		return;
	$.each(data, function(i, obj) {
		var articles = obj.articles;
		var len = articles.length;
		//alert(len);
		var tmpStr = '';
		var materialId = obj.id;
		var date = obj.create_time;
		var title = articles[0].title;
		var picurl = articles[0].picurl;
		var desc = articles[0].description;
		var url = articles[0].url;
		if (len == 1) { //单条图文					
			tmpStr += ' <div class="TW_box">';
			tmpStr += ' 	<div class="tw_edit">';
			tmpStr += ' 		<a class="optFor" href="javascript:;" name="'
					+ materialId + '"></a>';
			tmpStr += '		</div>';
			tmpStr += ' 	<div class="appTwb1">';
			tmpStr += ' 		<h3 class="twh3"><a href="#">' + title + '</a></h3>';
			tmpStr += ' 		<p class="twp">' + date + '</p>';
			tmpStr += '			<div class="reveal">';
			tmpStr += '				<img src="' + picurl + '"  />';
			tmpStr += '		 	</div>';
			tmpStr += '		</div>';
			tmpStr += '	<div class="appTwb2">';
			tmpStr += ' 	<div class="tw_text">';
			tmpStr += ' 		<p>' + desc + '</p>';
			tmpStr += ' 	</div>';
			tmpStr += '	</div>';
			tmpStr += ' </div>';
		} else {//多条图文
			tmpStr += '<div class="TW_box">';
			tmpStr += ' <div class="tw_edit">';
			tmpStr += ' 		<a class="optFor" href="javascript:;" name="'
					+ materialId + '"></a>';
			tmpStr += '</div>';
			tmpStr += ' <div class="appTwb1">';
			tmpStr += '		<p class="twp">' + date + '</p>';
			tmpStr += '		<div class="reveal">';
			tmpStr += '			<h5 class="tw_z">';
			tmpStr += '  			<a class="z_title" href="#">' + title + '</a>';
			tmpStr += '			 </h5>';
			tmpStr += '			<img src="' + picurl + '"  />';
			tmpStr += '  	</div>';
			tmpStr += ' </div>';
			tmpStr += ' <div class="appTwb2">';
			for ( var j = 1; j < len; j++) {
				var title = articles[j].title;
				var picurl = articles[j].picurl;
				tmpStr += '<div class="tw_li">';
				tmpStr += '		<a class="atext" href="javascript:;">' + title + '</a>';
				tmpStr += ' 	<img width="70" height="70" src="' + picurl
						+ '" />';
				tmpStr += ' </div>';
			}
			tmpStr += ' </div>';
			tmpStr += ' </div>';
		}
		if (i % 2 == 0) {//左边内容					
			contentLeft += tmpStr;
		} else {//右边内容
			contentRight += tmpStr;
		}
	});
	contentLeft += ' </div>';
	contentRight += ' </div>';
	return (contentLeft + contentRight);
}