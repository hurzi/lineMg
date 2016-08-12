<?php tpl("Common.header_1")?>

	<div class="con_c_t">
		<div class="con_bzk">
			<div style="padding: 10px;">
				<div id="go_back" class="button green medium">返回</div>
			</div>
		</div>
	</div>
	<div class="con_c_t ">
		<div class="con_edit">
			<form class="addform">
				<TABLE cellpadding="0" cellspacing="0" class="have_combo">
					<THEAD>
						<tr>
							<td colspan="2">修改客户所属组</td>
						</tr>
					</THEAD>
					<TBODY>
						<tr>
							<td width="120" class="tr">
								<span>
									<label for="name">客户名称:</label>
								</span>
							</td>
							<td>
								<?php echo $user['nickname'];?>
							</td>
						</tr>
						<tr>
							<td width="120" class="tr">
								<span>
									<label for="name">所属分组:</label>
								</span>
							</td>
							<!-- <td>
								<select id="group-combotree" name="gids[]" style="width: 200px;"></select>
							</td> -->	
							<td>
								<div id="selected_group" ></div>
								<div style="display:block;clear: both;">
									<input type="button" id="selectOperateGroupBtn" onclick="selectOperateGroup()" value="选择客户组"/>
<!--  									<input type="hidden" id="group_id" name="group_id" value="[]" />  -->
								</div>
							</td>
						</tr>
						<tr>
							<td class="tr"></td>
							<td><a href="javascript:void(0);" class="button blue medium" name="Submit2" id="submit">提交</a></td>
						</tr>
					</TBODY>
				</TABLE>
				<input type="hidden" id="id" value="<?php echo $user['user'];?>"/>
			</form>
		</div>
	</div>
<script type="text/javascript">

//选择所属客户组
var operateGroupSelector = null;
var getSelOperGroupIds = [];
var getSelOperGroupNames = "";
var operateGroupUrl = {one: '/Admin/index.php?a=UserGroup&m=getFirstGroupJson',
		two: '/Admin/index.php?a=UserGroup&m=getTwoGroupJson'};
var operateGroupParam = {
	callback: getOperateCallBack,
	maxNum: -1,
	targetDiv: 'selected_group',
	defaultSelected: <?php echo $selectedGroup;?>,
	top: 186
};
operateGroupSelector = new GroupSelector(operateGroupUrl, operateGroupParam);
operateGroupSelector.renderDefault();
function selectOperateGroup ()  {
	operateGroupSelector.show();
}
//回调方法
function getOperateCallBack(_data){
	getSelOperGroupIds = operateGroupSelector.getTwoGIds();
	//$("#group_id").val(getSelOperGroupIds);
	//$('#selected_group_ids').html('<div>'+ids.join(',')+'</div>');
// 	var nameCounts = [];
// 	for (var id=0,len=_data.length; id<len; id++) {
// 		var dName = _data[id]['name'];
// 		var dd = _data[id]['child'];
// 		if (!dd) continue;
// 		for (var i=0,len2=dd.length; i<len2; i++) {
// 			if (dd[i]['name']) nameCounts.push(dName+"("+dd[i]['name']+")("+dd[i]['count']+")");
// 		}
// 	}
// 	getSelOperGroupNames = nameCounts.join('，');
// 	$('#selected_group').attr("title",getSelOperGroupNames);

// 	$('#selected_group').html(getSelOperGroupNames);
}

$(function (){
	var groupListJson = <?php echo $groupListJson;?>;
	var defaultGroupIds = <?php echo $defaultGroupIds;?>;
	getSelOperGroupIds = defaultGroupIds;
	//$('#group-combotree').combotree({multiple:true}).combotree('loadData', groupListJson).combotree('setValues', defaultGroupIds);
	var url = "<?php echo url('User', 'changeGroup');?>";
	var href = "<?php echo url('User', 'index');?>";
	$('#submit').click(function (){
		checkSubmit(url, href);
	});
	//返回首页
	$('#go_back').click(function(){
		window.location.href = href;
	});
});
function checkSubmit(url, href){
	var id = $.trim($('#id').val());
	var gids = getSelOperGroupIds;
	var params = {
		id:id,
		gids:gids
	};
	ajaxSubmit(url, 'POST', params, function(status, result){
    	if (status == false || result.error == 0) window.location.href = href;
	}, '修改成功');
}
</script>
<?php tpl('Common.footer_1');?>