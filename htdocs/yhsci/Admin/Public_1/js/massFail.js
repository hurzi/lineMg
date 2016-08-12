
function viewErrorInfo(taskId){
	var wb = new jsbox({
		onlyid : "timingSend_id",
		title : '查看群发失败',		
		url : viewFailUrl+'&taskId='+taskId,
		iframe : true,
		conw : 600,
		conh : 450,		
		range : true
	}).show();
	// window.parent.$('body').height(window.parent.$('body').height());
	
}

function operaMany(url, name, href) {
	name = name || 'items';
	var ids = [];
	$("input[name='"+name+"']:checkbox:checked").each(function (){
		ids.push($(this).val());
	});
	if (ids.length <= 0) {
		loadMack({off:'on',Limg:0,text:'请选择发送项!',set:1000});
		return false;
	}
	opera(url, ids, href);
}


function opera(url, ids, href){
	jsConfirm(300, '你确定要发送吗？', function (){
		var params = {
			ids:ids
		}
		ajaxSubmit(url, 'POST', params, function(){
			//alert(window.parent.$('.failNumber').html());
			if (href) {
				window.location.href = href;
			} else {
				window.location.reload();
			}
		}, '已发送完成');
	});
}