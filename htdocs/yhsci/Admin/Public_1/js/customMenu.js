var LIMIT_PARENT_MENU_LENGTH = 16;
var LIMIT_CHILD_MENU_LENGTH = 40;

var doAjax = false;

$(function() {
	//返回首页
	$('#go_back').click(function(){
		window.location.href = href;
	});
	//类型选择
	$('input[name="menuType"]').click(function() {
		if (1== $(this).val()) {
			$("#system_preinstall").show();
			$("#dynamic_acquire").hide();
			$("#visit_webpage").hide();

		} else if (2== $(this).val()) {
			$("#system_preinstall").hide();
			$("#dynamic_acquire").show();
			$("#visit_webpage").hide();
		} else {
			$("#system_preinstall").hide();
			$("#dynamic_acquire").hide();
			$("#visit_webpage").show();
		}
	});
	
	//确认修改
	$('#submit_dynamic,#submit_webpage').click(function() {
		if(doAjax == true){
			return;
		}
		var params = getFormData();
		if (! params) {
			return;
		}
		doAjax == true;
		ajaxSubmit(setUrl, 'POST', params, function(status, result){
			doAjax = false;
			if (status == false || result.error == 0) window.location.href = href;
		}, '修改成功');
	});
});
//修改菜单获取表单数据
function getFormData(msgData) {
	msgData = msgData || {};
	var menuId = $('#menuId').val();
	var menuName = $.trim($('#menuName').val());
	var parentId = $('select[name="parentId"] option:selected').val();
	var menuType = $('input[name="menuType"]:checked').val();
	var menuOrder = parseInt($('#menuOrder').val());
	
	var materialId = msgData['material_id'] || '';
	var msgType = msgData['msg_type'] || '';
	var textContent = msgData['content'] || '';
	
	var url = '';

	if (menuName == '') {
		var tip = ('请输入菜单名称');
		loadMack({off:'on', Limg:0, text:tip, set:2000});
		return false;
	} else if (parentId == 0 && getStrLength(menuName) > LIMIT_PARENT_MENU_LENGTH) {
		var tip = ('菜单名称不能超过' + LIMIT_PARENT_MENU_LENGTH + '个字节');
		loadMack({off : 'on',Limg : 0,text : tip,set : 2000});
		return false;
	} else if (parentId > 0 && getStrLength(menuName) > LIMIT_CHILD_MENU_LENGTH) {
		var tip = ('子菜单名称不能超过' + LIMIT_CHILD_MENU_LENGTH + '个字节');
		loadMack({off : 'on',Limg : 0,text : tip,set : 2000});
		return false;
	} else if (isNaN(menuOrder)) {
		var tip = ('菜单排序值必须是一个整数');
		loadMack({off : 'on',Limg : 0,text : tip,set : 2000});
		return false;
	} else if (menuType == 1) {
		if ('text' == msgType) {
			if (isEmpty(textContent)) {
				loadMack({off:'on',Limg:0,text:'请输入文本消息',set:1000});
				return false;
			}
		} else {
			if (isEmpty(materialId) || materialId == 0) {
				loadMack({off:'on',Limg:0,text:'请选择素材',set:1000});
				return false;
			}
		}
	} else if (menuType == 2){
		url = $('#dynamic_url').val();
		if (url == '') {
			var tip = ('请输入url地址');
			loadMack({off : 'on',Limg : 0,text : tip,set : 2000});
			return false;
		} else if (! isUrl(url)) {
			loadMack({off:'on',Limg:0,text:'请填写正确的URL',set:2000});
			return false;
		}
	} else if (menuType == 3) {
		url = $('#visit_url').val();
		if (url == '') {
			var tip = ('请输入url地址');
			loadMack({off : 'on',Limg : 0,text : tip,set : 2000});
			return false;
		} else if (! isUrl(url)) {
			loadMack({off:'on',Limg:0,text:'请填写正确的URL',set:2000});
			return false;
		}
	}

	var data = {
		menuId : menuId,
		menuName : menuName,
		menuOrder : menuOrder,
		parentId : parentId,
		menuType : menuType,
		msgType : msgType,
		content : textContent,
		materialId : materialId,
		url : url,
		is_oauth : msgData.is_oauth
	};
	return data;
}

//messageSelector组件回调方法
function msgCallback(data) {
	if(doAjax == true){
		return;
	}
	var params = getFormData(data);
	if (! params) {
		return;
	}
	doAjax == true;
	ajaxSubmit(setUrl, 'POST', params, function(status, result){
		doAjax = false;
		if (status == false || result.error == 0) window.location.href = href;
	}, '修改成功');
}

//同步菜单
function synchronousMenu() {
	jsConfirm(300, '你确定要同步微信菜单么？', function () {
		var url = synchronousWxUrl;
		var options = {
			url : url,
			callbackFn : callbackSynch,
			loadText : '正在同步微信菜单...'
		};
		commonReq(options);
	});
}

function clearWxMenu() {
	jsConfirm(300, '你确定要清除微信端菜单么？', function () {
		var url = clearWxUrl;
		var options = {
			url : url,
			callbackFn : callbackClear,
			loadText : '正在清除微信菜单...'
		};
		commonReq(options);
	});
}
//通用请求入口
function commonReq(options) {
	var url = options.url || '';
	var type = options.type || 'get';
	var data = options.data || {};
	var dataType = options.dataType || 'json';
	var callbackFn = options.callbackFn || '';
	var loadText = options.loadText;
	$.ajax({
		url : url,
		type : type,
		data : data,
		dataType : dataType,
		beforeSend : function() {
			loadMack({
				text : loadText
			});
		},
		complete : function() {
			loadMack({
				off : 'off'
			});
		},
		success : function(msg) {
			if (callbackFn) {
				callbackFn.call(null, msg);
			}
		}
	});
}

function callbackClear() {
	var msg = arguments[0] || '';
	if (!msg)
		return;
	var tip = (msg.msg);
	jsAlert(tip);

}
function callbackSynch() {
	var msg = arguments[0] || '';
	if (!msg)
		return;
	var tip = (msg.msg);
	jsAlert(tip,function(){
		window.location.reload();
	});	
}