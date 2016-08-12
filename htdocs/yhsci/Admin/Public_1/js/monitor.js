//setTimeout("refreshPage()",5000); 

/**
 * 显示客服信息
 * @param operatorData
 */
function showOperatorData(operatorData){
	var operatorPart = '';
	var title = '';
	var colorClass = '';
	if (! isEmpty(operatorData)) {
		$('#onlineNum').html(operatorData.length);
		for (var i in operatorData ) {
			var vo = operatorData[i];
			var onlineStatus = vo.onlineStatus;
			
			operatorPart += '<tr>';
			operatorPart += '<td>';
			switch (onlineStatus) {
				case 1:
					title = '正常服务';
					colorClass = 'lx';
					break;
				case 2:
					title = '暂停服务';
					colorClass = 'hong';
					break;
				case 3:
					title = '断线';
					colorClass = '';
					break;
				case 4:
					title = '离线';
					colorClass = '';
					break;
			}
			operatorPart += '<div class="u_state '+colorClass+'" title="'+title+'"></div>';
			
			operatorPart += '</td>';
			operatorPart += '<td>'+ vo.operator_nickname + '<br>' + vo.operator_username + '</td>';
			operatorPart += '<td>' + vo.session_num + '/' + vo.session_max + '</td>'; 
			operatorPart += '<td>';
			if (vo.sessionUser) {
				operatorPart += vo.sessionUser.join('<br>'); 
			} else {
				operatorPart += '---';
			}
			operatorPart += '</td>';
			operatorPart += '</tr>';
		}
	} else {
		$('#onlineNum').html(0);
		operatorPart = '<tr><td colspan="5" align="center">无内容</td>';
	}
	$('#operatorData').html(operatorPart);
}

/**
 * 显示已分配客户
 * @param assignedUser
 */
function showAssignedData(assignedData){
	var assignedPart = '';
	if (! isEmpty(assignedData)) {
		$('#assignedNum').html(assignedData.length);
		for (var i in assignedData ) {
			var vo = assignedData[i];
			
			assignedPart += '<tr>';
			if (vo.remark) {
				assignedPart += '<td>' + vo.remark + '(' +vo.nickname + ')</td>';
			} else {
				assignedPart += '<td>' + vo.nickname + '</td>';
			}
			
			assignedPart += '<td>' + vo.operator_nickname + '</td>';
			assignedPart += '<td>' + vo.group_name + '</td>';
			assignedPart += '<td>';
			if (vo.province) {
				assignedPart += vo.province + '&nbsp;';
			}
			assignedPart += vo.city;
			assignedPart += '</td>';
			assignedPart += '<td>' + vo.session_time + '</td>';
			assignedPart += '</tr>';
		}
	} else {
		$('#assignedNum').html(0);
		assignedPart = '<tr><td colspan="5" align="center">无内容</td>';
	}
	$('#assignedData').html(assignedPart);
}
/**
 * 显示未分配客户
 * @param unassignedUser
 */
function showUnassignedData(unassignedData){
	var unassignedPart = '';
	if (! isEmpty(unassignedData)) {
		$('#unassignedNum').html(unassignedData.length);
		for (var i in unassignedData ) {
			var vo = unassignedData[i];
			
			unassignedPart += '<tr>';
			if (vo.remark) {
				unassignedPart += '<td>' + vo.remark + '(' +vo.nickname + ')</td>';
			} else {
				unassignedPart += '<td>' + vo.nickname + '</td>';
			}
			
			unassignedPart += '<td>' + vo.group_name + '</td>';
			unassignedPart += '<td>';
			if (vo.province) {
				unassignedPart += vo.province + '&nbsp;';
			}
			unassignedPart += vo.city;
			unassignedPart += '</td>';
			unassignedPart += '<td>' + vo.wait_time + '</td>';
			unassignedPart += '</tr>';
		}
	} else {
		$('#unassignedNum').html(0);
		unassignedPart = '<tr><td colspan="5" align="center">无内容</td>';
	}
	$('#unassignedData').html(unassignedPart);
}

/**
 * 加载数据
 * @param url
 */
function refreshPage(url){
	var t = new Date();
	jQuery.ajax({
		type:"GET",
		url : url + '&t=' +t,
		dataType:"json",
		success:function(msg){
			loadMack({off:'off'});
			var assignedData = msg.assignedData;
			var unassignedData = msg.unassignedData;
			var operatorData = msg.operatorData;
			
			showOperatorData(operatorData);
			
			showAssignedData(assignedData);
			
			showUnassignedData(unassignedData);
			
			parentSH();
			setTimeout("refreshPage('"+url+"')", 5000);  
		}
	});
}