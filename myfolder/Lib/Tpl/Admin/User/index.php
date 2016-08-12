<?php tpl('Common.header_1');?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<input type="button" class="button green medium" value="转移至" id="moveGroup"></input>
			<!-- 			<select class="nav_select" id="group-combotree" name="groupId" ></select> -->
			<span id="selected_move_group"></span>
			<button id="selectMoveUserGroupBtn" onclick="moveUserGroup()">选择客户组</button>
			<form class="" style="margin-top: 5px;">
				<select name="province" id="province" class="nav_select" style="width: 120px">
					<option value="">全部省</option>
				</select>
				<select name="city" id="city" class="nav_select" style="width: 120px">
					<option value="">全部市</option>
				</select>
				<select class="nav_select" id="sex" name="sex" style="width: 80px">
					<option value="-1">性别</option>
					<?php
					foreach ($sexList as $key => $val) {
						?>
					<option value="<?php echo $key;?>" <?php if ($sex == $key) {echo'selected="selected"';}?>><?php echo $val;?></option>
				<?php }?>
				</select>
				<select class="nav_select" id="ent_subscribe" name="ent_subscribe" style="width: 80px">
					<option value="-1">订阅</option>
					<?php
					foreach ($entSubscribeList as $key => $val) {
						?>
					<option value="<?php echo $key;?>" <?php if ($ent_subscribe == $key) {echo'selected="selected"';}?>><?php echo $val;?></option>
				<?php }?>
				</select>
				<select class="nav_select" id="subscribe" name="subscribe" style="width: 80px">
					<option value="-1">关注</option>
					<?php
					foreach ($subscribeList as $key => $val) {
						?>
					<option value="<?php echo $key;?>" <?php if ($subscribe == $key) {echo'selected="selected"';}?>><?php echo $val;?></option>
				<?php }?>
				</select>
				<select class="nav_select" id="is_bind" name="is_bind" style="width: 80px">
					<?php
					foreach ($_IS_BIND as $key => $val) {
						?>
					<option value="<?php echo $key;?>" <?php if ($is_bind == $key) {echo'selected="selected"';}?>><?php echo $val;?></option>
				<?php }?>
				</select>
				<input class="inputS" type="text" placeholder="微信号" value="<?php echo $openid;?>" name="openid" id="openid">
				<p style="padding: 10px 0px;">
					<input class="inputS" type="text" placeholder="客户名称" value="<?php echo $keyword;?>" name="keyword" id="keyword"> <input id="selectUserGroupBtn" type="button" value="选择客户组" /> <input id="getSelectUserGroup" type="hidden" value="<?php echo $gid; ?>" name="gid" /> <input id="getGroupName" type="hidden" value="<?php echo $groupName; ?>" name="groupName" /> <span>已选择客户组：</span> <span id="selected_group"><?php echo $groupName;?></span>
				</p>
				<p>
					<input name="a" type="hidden" value="<?php echo $action;?>" /> <input name="m" type="hidden" value="<?php echo $method;?>" /> <input type="button" class="button blue medium" value="查询" id="search-button">
				</p>
			</form>
		</div>
	</div>
</div>
<div class="con_c_c">
	<table class="tab_con" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<td width="15"><input type="checkbox" class="qxCB" /></td>
				<td width="160">客户名称/备注</td>
				<td width="80">绑定会员ID</td>
				<td width="80">微信号</td>
				<td width="30">性别</td>
				<td width="30">关注</td>
				<td width="90">地域</td>
				<td>所属分组</td>
				<td width="40">操作</td>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($list) {
			foreach ($list as $vo) {
				?>
			<tr>
				<td height="25"><input type="checkbox" name="items" value="<?php echo $vo['user'];?>" /></td>
				<td>
					<div>
						<a href="javascript:;" title="点击跳转到当前客户的会话历史列表" class="linkClass" name="<?php echo $vo['nickname'];?>"><?php echo $vo['nickname'];?></a>
					</div>
					<div class="_sort_box" style="float: left; width: 100px;"><?php echo $vo['remark']?$vo['remark']:'---';?></div>
					<div class="czx editName" style="float: left; margin-right: 2px;">
						<a class="edit" href="javascript:;" title="编辑客户备注"></a>
					</div>
					<div class="_sort_input_box" style="display: none;">
						<input type="text" style="width: 90px;" value="<?php echo $vo['remark'];?>" /> <input name="plugin_id" type="hidden" value="<?php echo $vo['user']?>" /> <a class="_save" href="javascript:void(0);">保存</a>&nbsp; <a class="_cancel" href="javascript:void(0);">取消</a>
					</div>
				</td>
				<td><div style="width: 70px;" class="break"><?php echo empty($vo['member_id'])?'---':$vo['member_id'];?></div></td>
				<td><div style="width: 70px;" class="break"><?php echo $vo['user'];?></div></td>
				<td><?php echo $vo['sex_name'];?></td>
				<td><?php if($vo['subscribe'] == 1){echo "是";}else{echo "否";}?></td>
				<td><?php echo $vo['province'].' '.$vo['city'];?></td>
				<td <?php if($vo['groupTotal']>0){ echo "class='lv'";}?> title="<?php echo $vo['groupAllInfo'];?>"><?php echo $vo['group'];?></td>
				<td class="editAll">
					<div class="czx">
						<a name="<?php echo $vo['user'];?>" href="javascript:;" class="edit"></a>
					</div>
				</td>
			</tr>
			<?php
			}
		} else {
			?>
			<tr>
				<td colspan="9" align="center">无内容</td>
			</tr>
		<?php }?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9">
					<div class="tab_foot"><?php echo $page;?></div>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">

//转移至分组选择
var moveGroupSelector = null;
var getSelMoveGroupIds = null;
var getSelMoveGroupNames = "";
var moveGroupUrl = {one: '/Admin/index.php?a=UserGroup&m=getFirstGroupJson&type=1',
		two: '/Admin/index.php?a=UserGroup&m=getTwoGroupJson'};
var moveGroupParam = {
	callback: getMoveCallBack,
	maxNum: 1,
	targetDiv: '',
	top: 186
};
moveGroupSelector = new GroupSelector(moveGroupUrl, moveGroupParam);
moveGroupSelector.renderDefault();
function moveUserGroup ()  {
	moveGroupSelector.show();
}
//回调方法
function getMoveCallBack(_data){
	getSelMoveGroupIds = moveGroupSelector.getTwoGIds();
	//$('#selected_group_ids').html('<div>'+ids.join(',')+'</div>');
	var nameCounts = [];
	for (var id=0,len=_data.length; id<len; id++) {
		var dName = _data[id]['name'];
		var dd = _data[id]['child'];
		if (!dd) continue;
		for (var i=0,len2=dd.length; i<len2; i++) {
			if (dd[i]['name']) nameCounts.push(dName+"："+dd[i]['name']+"("+dd[i]['count']+")");
		}
	}
	getSelMoveGroupNames = nameCounts.join('，');
	$('#selected_move_group').html(getSelMoveGroupNames);
}


//查询选择客户分组
var grouSelector = null;
var getSelectGroupIds = null;
var getSelectGroupNames = "";
var selectGroupUrl = {one: '/Admin/index.php?a=UserGroup&m=getFirstGroupJson',
		two: '/Admin/index.php?a=UserGroup&m=getTwoGroupJson'};
var selectGroupParam = {
	callback: getSelectCallBack,
	maxNum: 1,
	targetDiv: '',
	top: 186
};
grouSelector = new GroupSelector(selectGroupUrl, selectGroupParam);
grouSelector.renderDefault();

$('#selectUserGroupBtn').click(function(){
	grouSelector.show();
});
//回调方法
function getSelectCallBack(_data){
	getSelectGroupIds = grouSelector.getTwoGIds();
	var nameCounts = [];
	for (var id=0,len=_data.length; id<len; id++) {
		var dName = _data[id]['name'];
		var dd = _data[id]['child'];
		if (!dd) continue;
		for (var i=0,len2=dd.length; i<len2; i++) {
			if (dd[i]['name']) nameCounts.push(dName+"："+dd[i]['name']+"("+dd[i]['count']+")");
		}
	}

	if(getSelectGroupIds!=null){
		var group_id = getSelectGroupIds.join(',');
		$('#getSelectUserGroup').val(group_id);
	}

	getSelectGroupNames = nameCounts.join('，');
	$('#getGroupName').val(getSelectGroupNames);
	$('#selected_group').html(getSelectGroupNames);
}


$(function () {
	$().checkboxqx('.tab_con .qxCB');

	var selectGroupList = <?php echo $selectGroupList;?>;
	var defaultGid = <?php echo $gid;?>;
	//$('#gid-combotree').combotree().combotree('loadData', selectGroupList).combotree('setValue', defaultGid);

	var province = "<?php echo $province;?>";
	var city = "<?php echo $city;?>";

	//初始化地域
	initProvince(province);
	loadCity(province, city);
	$('#province').live('change', function() {
		var provinceName = $(this).val();
		loadCity(provinceName);
	});

	/**
	 * 初始化省份列表
	 **/
	function initProvince(province) {
		var provinceArr = getProvince();
		var optionHtml = '';
		$.each(provinceArr, function(i, name) {
			if (province == name) {
				optionHtml += '<option value="' + name + '" selected="selected">' + name + '</option>';
			} else {
				optionHtml += '<option value="' + name + '">' + name + '</option>';
			}

		});
		$('#province').append(optionHtml);
	}

	function loadCity(provinceName, city) {
		var cityArr = getCityByProvinceName(provinceName);
		var optionHtml = '<option value="">全部</option>	';
		$.each(cityArr, function(i, name) {
			if (city == name) {
				optionHtml += '<option value="' + name + '" selected="selected">' + name + '</option>';
			} else {
				optionHtml += "<option value='" + name + "'>" + name + "</option>";
			}
		});
		$('#city').html(optionHtml);
	}

	//搜索
	$('#search-button').click(function (){
		$(this).closest('form').submit();
	});
	$('.editAll .edit').click(function(){
		var id = $(this).attr('name');
		window.location.href = "<?php echo url('User','edit')?>"+"&id="+id;
	});
	$('.linkClass').click(function(){
		var name = encodeURIComponent($(this).attr('name'));
		window.open("<?php echo url('SessionHistory','index')?>&nickname="+name);
	});

	//编辑
	$('.editName .edit').click(function(){
		var tr = $(this).parents('tr');
		tr.find('._sort_box').hide();
		var val = $.trim(tr.find('._sort_box').html());
		if (val == '---') {
			val = '';
		}
		tr.find('._sort_input_box').show().find('input').val();;
		$(this).hide();
		//tr.find('.editName').hide();
		tr.find('._update_box').show();
	});
	//保存
	$('._save').click(function(){
		if(window.flagReq == true)return;
		var plugin_id = $(this).prev('input[name=plugin_id]').val();
		var tr 		  = $(this).parents('tr');
		var sort 	  =  tr.find('._sort_input_box').find('input').val();
		var url		  = "<?php echo url('User', 'updateRemark');?>";
		window.flagReq = true;
		$.post(url, {openId:plugin_id,remark:sort}, function(result) {
			result = eval('('+result+')');
			jsAlert(result.msg,function(){
				if(result.error == 0){
					tr.find('._sort_box').show().html(sort||'---');
					tr.find('._sort_input_box').hide();
					tr.find('.editName .edit').show();
					tr.find('._update_box').hide();
				}
				window.flagReq = false;
			});
		});
	});
	//取消
	$('._cancel').click(function(){
		var tr = $(this).parents('tr');
		tr.find('._sort_box').show();
		tr.find('._sort_input_box').hide()
		tr.find('.editName .edit').show();
		tr.find('._update_box').hide();
	});

	//批量转组
	$('#moveGroup').click(function () {
		var group_id = 0;
		if(getSelMoveGroupIds!=null){
			group_id = getSelMoveGroupIds.join(',');
		}

		if (group_id == 0) {
			var prompt = "请选择要转移到哪个分组！";
			loadMack({off:'on',Limg:0,text:prompt,set:1000});
			return ;
		}

		var url	= "<?php echo url('User','moveGroup');?>";
		var ids = [];
		$("input[name='items']:checkbox:checked").each(function (){
			ids.push($(this).val());
		});
		if (ids.length == 0) {
			var prompt = "请选择要操作的客户";
			loadMack({off:'on',Limg:0,text:prompt,set:1000});
		 	return false;
	 	}
		var params = {
				ids:ids,
				group_id:group_id
			};

		jsConfirm(300, '你确定要转移分组吗？',function() {
			ajaxSubmit(url, 'post', params, function(status,result){
				if (status == false || result.error == 0) window.location.reload();
			})
		});
	});
	$('.lv_test').on('click',function(){
		var groups = $(this).attr('title');
		var content ="<div style ='overflow-y:scroll;'>"+groups+"</div>";
		var wb = new jsbox({
			onlyid:"show_user_group_id",
			content:content,
			title:'显示客户的所有组',
			conw:480,
			conh:150,
			FixedTop:50,
			//iframe:false, //是使用iframe方式弹出层
			//loads: true, //是使用DIV URL方式弹出层
			range:true,
			mack:true
			}).show();
	});
	//$('.lv').on('click',function(){
		//new PopupLayer({trigger:'.lv_',popupBlk:".blk",closeBtn:".closeBtn"});
	//});

});
</script>
<!--
	<link href="./Public_1/cj/popDiv/styles/core.css" type="text/css" rel="stylesheet"/>
    <script src="./Public_1/cj/popDiv/scripts/popup_layer.js" type="text/javascript" language="javascript"></script>
    -->
<?php tpl('Common.footer_1');?>