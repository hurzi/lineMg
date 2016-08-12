//客服分组选择插件
var grouSelector = null;
var getSelectGroupIds = [];
var getSelectGroupNames = "";

$(function() {
	
	//初始化地域
	initProvince();
	$('#province').live('change', function() {
		var provinceName = $(this).val();
		loadCity(provinceName);
	});
	
	//备注提示		 	
	$('.nav1').TabNav('', function(index) {
		var type = (0 == index) ? 'condition' : 'users';
		$('#type').val(type);
	});

	//按条件搜索客户
	$('#condition_button').click(function() {
		$('#condition_footer').html('<p>已选择<b>0</b>人&nbsp;&nbsp;');
		searchUserByCond();
	});

	//特定客户搜索客户
	$('#search_button').click(function() {
		searchUserByName();
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
	
	var url = {one: '/Admin/index.php?a=UserGroup&m=getFirstGroupJson',
			two: '/Admin/index.php?a=UserGroup&m=getTwoGroupJson'};
	var grouParam = {
		callback: getCallBack,
		maxNum: -1,
		targetDiv: 'selected_group',
		top: 120,
		all: true,
		noGroup:true
	};
	grouSelector = new GroupSelector(url, grouParam);
	grouSelector.renderDefault();
	$("#oper_group_def_id").html(grouSelector.getDefaultTwoGIds().join(','));
});

function selectOperaGroup ()  {
	grouSelector.show();
}
//回调方法
function getCallBack(_data){
	getSelectGroupIds = [];
	var nameCounts = [];
	var childNames = [];
	for (var id=0,len=_data.length; id<len; id++) {
		var dName = _data[id]['name'];
		var dd = _data[id]['child'];
		if (!dd) continue;
		childNames = [];
		for (var i=0,len2=dd.length; i<len2; i++) {
			if (-2 == dd[i]['id']) {//不限
				getSelectGroupNames = '';
				getSelectGroupIds = [-1];
				return;
			} else if (-1 == dd[i]['id']) {//未分组
				getSelectGroupIds.push('0');
				nameCounts.push(dName+"未分组");
			} else {
				getSelectGroupIds.push(dd[i]['id']);
				if (dd[i]['name']) {
					childNames.push(dd[i]['name']+"("+dd[i]['count']+")");
				}
			}
		}
		if (childNames.length > 0) {
			nameCounts.push(dName+"["+ childNames.join(',') +"]");
		}
	}
	getSelectGroupNames = nameCounts.join('，');
	parentSH();
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

//验证获取表单和回调数据
function getFormData(isSearch) {
	var type = $('#type').val();
	var userTotal = 0;
	var sex = '';
	var gid = '';
	var country = '';
	var province = '';
	var city = '';
	var users = [];
	var keyword = '';
	var sendCond = type == 'condition' ? $('#hidByCondition').html() : $('#hidByUsers').html();
	var msgType = $('#msg_type').val();
	var data = {
			type : type,
			sendCond : sendCond
	};

	switch (type) {		
		case 'condition':
			gid = getSelectGroupIds;
			sex = $('#sex').val();
			province = $('#province').val();
			city = $('#city').val();
			userTotal = parseInt($('#user_total').val());
			keyword = $('#keyword').val();
			var gidStr = gid.join(',');
			if ('template' == msgType) {
				if ((!gidStr || gidStr < 0) && sex < 0 && !province && !keyword) {
					window.top.loadMack({
						off : 'on',
						Limg : 0,
						text : '请至少选择一项条件',
						set : 1000
					});
					return false;
				}
			} else {
				if (gidStr < -1 ) {
					window.top.loadMack({
						off : 'on',
						Limg : 0,
						text : '请选择客户分组',
						set : 1000
					});
					return false;
				}
			}
			
			data.sex = sex;
			data.gid = gid;
			data.country = country;
			data.province = province;
			data.city = city;
			data.keyword = keyword;
			break;
		case 'users':
			userTotal = $('#u_choosed .zztag').length;
			$('#u_choosed .zztag').each(function() {
				users.push($(this).attr('id'));
			});
			data.users = users;
			break;
		default:
			window.top.loadMack({
				off : 'on',
				Limg : 0,
				text : '无效的发送类型',
				set : 1500
			});
			return false;
	}

	if (!isSearch && (userTotal < 0 || !userTotal)) {
		window.top.loadMack({
			off : 'on',
			Limg : 0,
			text : '请选择客户',
			set : 1000
		});
		return false;
	}
	return data;
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
	};
	$.post(
		getDataUrl,
		params,
		function(result) {
			loadMack({
				off : 'off'
			});
			result = eval("(" + result + ")");
			if (result.error != 0) {
				window.top.loadMack({
					off : 'on',
					Limg : 0,
					text : result.msg,
					set : 1500
				});
				return false;
			}
			if (result.data) {
				var list = result.data.list;
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
	var formData = getFormData(true);
	if (false == formData) {
		return false;
	}
	var msgType = $('#msg_type').val();
	
	var type = 'condition';
	var params = {
		sex : formData.sex,
		gid : formData.gid,
		province : formData.province,
		city : formData.city,
		type : type,
		keyword: formData.keyword,
		msg_type : msgType
	};
	var condHtml = [];
	if (params.gid  && params.gid.length > 0 && params.gid.join(',') != -1) {
		condHtml.push(getSelectGroupNames);
	}
	if (params.sex > -1) {
		condHtml.push($('#sex option:selected').text());
	}
	if (params.province && ! params.city) {
		condHtml.push(params.province);
	} else if (params.city && params.province) {
		condHtml.push(params.province + '-' +params.city);
	}
	if (params.keyword) {
		condHtml.push(params.keyword);
	}
	condHtml = '选择条件：' + (condHtml.length>0 ? condHtml.join('; ') : '全部');
	
	loadMack({off : 'on'});
	$.post(getDataUrl, params, function(result) {
		loadMack({off : 'off'});
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
		if (isNaN(userTotal) || userTotal < 0) {
			userTotal = 0;
		}
		$('#user_total').val(userTotal);
		var prompt = '<p>已选择<b>' + userTotal + '</b>人&nbsp;&nbsp;' + condHtml + '</p>';
		$('#condition_footer').html(prompt);
		$('#hidByCondition').html('按'+condHtml+' 发送');
		parentSH();
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
		window.top.loadMack({
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