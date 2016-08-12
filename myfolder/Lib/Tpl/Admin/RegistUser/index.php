<?php tpl('Common.header_1');?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<!-- 			<select class="nav_select" id="group-combotree" name="groupId" ></select> -->
			<form class="" style="margin-top: 5px;">
				<!-- 
				<select name="province" id="province" class="nav_select" style="width: 120px">
					<option value="">全部省</option>
				</select>
				<select name="city" id="city" class="nav_select" style="width: 120px">
					<option value="">全部市</option>
				</select>
				 -->
				<select class="nav_select" id="sex" name="sex" style="width: 80px">
					<option value="-1">性别</option>
					<?php
					foreach ($sexList as $key => $val) {
						?>
					<option value="<?php echo $key;?>" <?php if ($sex == $key) {echo'selected="selected"';}?>><?php echo $val;?></option>
				<?php }?>
				</select>
				<select class="nav_select" id="is_majia" name="is_majia" style="width: 80px">
					<option value="-1">用户类型</option>
					<option value="0">注册用户</option>
					<option value="1">马甲</option>
				</select>
				<select class="nav_select" id="as_status" name="as_status" style="width: 80px">
					<option value="-1">爱锁状态</option>
					<option value="0">注册未绑定</option>
					<option value="1">绑定</option>
					<option value="2">取消绑定</option>					
				</select>				
				<input class="inputS" type="text" placeholder="微信号" value="<?php echo $openid;?>" name="openid" id="openid">
				<p style="padding: 10px 0px;">
					<input class="inputS" type="text" placeholder="妮称" value="<?php echo $truthname;?>" name="truthname" id="truthname"> 
					<input class="inputS" type="text" placeholder="手机号" value="<?php echo $mobile;?>" name="mobile" id="mobile"> 
				</p>
				<p>
					<input name="a" type="hidden" value="<?php echo $action;?>" /> <input name="m" type="hidden" value="<?php echo $method;?>" /> 
					<input type="button" class="button blue medium" value="查询" id="search-button">
					<input type="button" class="button blue medium" value="增加马甲" id="add-button">
				</p>
				<p><span>总注册人数/注册未绑定/绑定/取消绑定:[<?php echo $statusCount['sum_count']?>/<?php echo $statusCount['nobind_count']?>/<?php echo $statusCount['bind_count']?>/<?php echo $statusCount['unbind_count']?>]</span>
				</p>
				<p>
					<span>短信余额:<?php echo $balance;?></span>
				</p>
			</form>
		</div>
	</div>
</div>
<div class="con_c_c">
	<table class="tab_con" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<!-- <td width="15"><input type="checkbox" class="qxCB" /></td> -->
				<td width="80">妮称</td>
				<!--<td width="80">openid</td>-->
				<td width="80">手机号</td>
				<td width="30">性别</td>
				<td width="30">爱锁邮编</td>
				<td width="30">爱锁状态</td>
				<td width="30">创建时间</td>
				<!-- <td width="90">地域</td> -->
				<td width="40">操作</td>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($list) {
			foreach ($list as $vo) {
				?>
			<tr>
				<!-- <td height="25"><input type="checkbox" name="items" value="<?php echo $vo['openid'];?>" /></td> -->
				<td>
					<div>
						<?php echo $vo['truthname'];?><?php if($vo['is_majia'] ){echo "<font color='red'>[马甲]</font>";}?>
					</div>					
				</td>
				<!--<td><div style="width: 70px;" class="break"><?php echo $vo['openid'];?></div></td>-->
				<td><div style="width: 80px;" class="break"><?php echo $vo['mobile'];?></div></td>
				<td><?php echo $vo['sex_name'];?></td>
				<td><?php echo $vo['as_code'];?></td>
				<td><?php switch ($vo['as_status']){
					case 0 : echo "未锁定";break;
					case 1 : echo "已锁定";break;
					case 2 : echo "已解锁";break;
					default: echo "单身";
					}?>
				</td>
				<td><?php echo $vo['create_time'];?></td>
				
				<!-- <td><?php echo $vo['province'].' '.$vo['city'];?></td>
				-->
				<td class="editAll">
					<div class="czx">
						<?php if($vo['is_majia'] && $vo['is_majia']==1){?>
						<a name="<?php echo $vo['uid'];?>" href="javascript:;" class="edit" target="majia"></a>
						<?php }else{?>
						<a name="<?php echo $vo['uid'];?>" href="javascript:;" class="edit" target="registUser"></a>
						<?php } ?>
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


$(function () {
	$().checkboxqx('.tab_con .qxCB');

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

	//增加马甲
	$('#add-button').click(function (){
		window.location.href="<?php echo url('RegistUser','add');?>";
	});
	
	$('.editAll .edit').click(function(){
		var id = $(this).attr('name');
		var type = $(this).attr("target");
		if(type == "majia"){
			window.location.href = "<?php echo url('RegistUser','edit')?>"+"&id="+id;
		}else{
			window.location.href = "<?php echo url('RegistUser','editRegistUser')?>"+"&id="+id;	
		}
	});
	$('.linkClass').click(function(){
		var name = encodeURIComponent($(this).attr('name'));
		window.open("<?php echo url('SessionHistory','index')?>&nickname="+name);
	});

	//取消
	$('._cancel').click(function(){
		var tr = $(this).parents('tr');
		tr.find('._sort_box').show();
		tr.find('._sort_input_box').hide()
		tr.find('.editName .edit').show();
		tr.find('._update_box').hide();
	});


});
</script>
<!--
	<link href="./Public_1/cj/popDiv/styles/core.css" type="text/css" rel="stylesheet"/>
    <script src="./Public_1/cj/popDiv/scripts/popup_layer.js" type="text/javascript" language="javascript"></script>
    -->
<?php tpl('Common.footer_1');?>