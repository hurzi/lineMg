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
	
});
//修改菜单获取表单数据
function getFormData(msgSelector) {
	msgSelector = msgSelector || {};
	var menuId = $('#menuId').val();
	var menuName = $.trim($('#menuName').val());
	var parentId = $('#parentId').val();
	var menuType = $('#menuType').val();
	var menuOrder = parseInt($('#menuOrder').val());
	
	var materialId = null;
	var msgType = null;
	var textContent = null;
	var is_oauth = null;
	
	var url = '';

	if (menuName == '') {
		var tip = ('请输入菜单名称');
		Common.alert(tip);
		return false;
	} else if (parentId == 0 && getStrLength(menuName) > LIMIT_PARENT_MENU_LENGTH) {
		var tip = ('菜单名称不能超过' + LIMIT_PARENT_MENU_LENGTH + '个字节');
		Common.alert(tip);
		return false;
	} else if (parentId > 0 && getStrLength(menuName) > LIMIT_CHILD_MENU_LENGTH) {
		var tip = ('子菜单名称不能超过' + LIMIT_CHILD_MENU_LENGTH + '个字节');
		Common.alert(tip);
		return false;
	} else if (isNaN(menuOrder)) {
		var tip = ('菜单排序值必须是一个整数');
		Common.alert(tip);
		return false;
	} else if (menuType == 1) {
		var msgData = msgSelector.getData();
		if (!MessageSelector.check(msgData)){
			return;
		}
		materialId = msgData['material_id'] || '';
		msgType = msgData['msg_type'] || '';
		textContent = msgData['content'] || '';
		is_oauth = msgData['content'] || '';
		
		if ('text' == msgType) {
			if (isEmpty(textContent)) {
				loadMack({off:'on',Limg:0,text:'请输入文本消息',set:1000});
				Common.alert('请输入文本消息');
				return false;
			}
		} else {
			if (isEmpty(materialId) || materialId == 0) {
				Common.alert('请选择素材');
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
		is_oauth : is_oauth
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
	Common.confirm('你确定要同步微信菜单么？', function () {
		Common.request(synchronousWxUrl,null,function(){Common.alert("同步成功")},null,null,{"tip":'正在同步微信菜单...'});
	});
}

function clearWxMenu() {
	Common.confirm( '你确定要清除微信端菜单么？', function () {
		Common.request(clearWxUrl,null,function(){Common.alert("删除成功")},null,null,{"tip":'正在清除微信菜单...'});
	});
}


/**
 * 选择类型
 */
function changeMenuReturnType(){
	var type = $("#menuType").val();
	if(type == 1){
		$(".preinstall_div").show();
		$(".dynamic_div").hide();
		$(".visit_div").hide();
	}else if(type == 2){
		$(".preinstall_div").hide();
		$(".dynamic_div").show();
		$(".visit_div").hide();
	}else if(type == 3){
		$(".preinstall_div").hide();
		$(".dynamic_div").hide();
		$(".visit_div").show();
		
	}
}
