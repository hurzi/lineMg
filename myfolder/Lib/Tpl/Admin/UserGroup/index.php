<?php tpl('Common.header_1');?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<div class="button green medium addFz" id="addBtnId">添加客户分组</div>
			<form class="soso" id="search-form" name="searchform">
			<select class="nav_select" id="type" name="type">
					<option value="0">请选择类型</option>
					<?php
					foreach ($createType as $key => $val) {
						?>
					<option value="<?php echo $key;?>"
					<?php if ($type == $key) {echo'selected="selected"';}?>><?php echo $val;?></option>
				<?php }?>
                </select>
				<input class="inputS" type="text" placeholder="分组名称" id="keyword"
					name="keyword" value="<?php echo $keyword;?>">
				<input name="a" type="hidden" value="<?php echo $action;?>" />
				<input name="m" type="hidden" value="<?php echo $method;?>" />
				<input type="button" class="button blue medium" value="查询" id="search-button">
			</form>
		</div>
	</div>
</div>
<div class="con_c_c">
	<table class="tab_group" cellpadding="0" cellspacing="0">
		<thead>
			<tr class='one_tr'>
				<td width="5"></td>
				<td width="240">分组名称</td>
				<td width="94">分组/客户(数量)</td>
				<td width="130">类型</td>
				<td>创建日期</td>
				<td width="80">操作</td>
			</tr>
		</thead>

		<tbody>

		<?php
			if ($list) {
				foreach ($list as $vo) {
			?>
            <tr class="one_tr">
                <td></td>
				<td><?php echo $vo['ug_name'];?></td>
				<td align="center"><?php echo $vo['count'];?></td>
				<td><?php echo $createType[$vo['create_type']];?></td>
				<td>
					<span class="hui"><?php echo $vo['create_time'];?></span>
				</td>
				<td>
					<div class="czx">
						<p name="<?php echo $vo['ug_id'];?>" class="report_icon_sys1 twoGroupList"  style="cursor:pointer;" title="二级列表" ></p>
					<?php if($vo['create_type']=='1'){?>
						<a name="<?php echo $vo['ug_id'];?>" href="javascript:void(0);" class="edit"></a>
						<a name="<?php echo $vo['ug_id'];?>" href="javascript:void(0);" class="del"></a>
					<?php }?>
					</div>
				</td>
            </tr>
            <tr id="show_html_tr_<?php echo $vo['ug_id'];?>" style="display:none;" class="two_tr">
            	<td colspan='6'  style="padding:0px" class="tr_two">
            	<div id="show_html_<?php echo $vo['ug_id'];?>" style=""></div>
            	</td>
            </tr>
			<?php }?>
	 <?php } else {?>
				<tr>
					<td colspan="6" align="center" class="empty">无内容</td>
				</tr>
	 <?php }?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
					<div class="tab_foot"><?php echo $page;?></div>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
$(function () {
	//搜索
	$('#search-button').click(function (){
		$('#search-form').submit();
	});

	$('#addBtnId').click(function(){
		window.location.href = "<?php echo url('UserGroup', 'add');?>";
	});

	//编辑
	$('.edit').click(function(){
		var id = $(this).attr('name');
		window.location.href = "<?php echo url('UserGroup', 'edit');?>&id="+id;
	});

	//单个删除
	$('.del').click(function(){
		var id = $(this).attr('name');
		var url = '<?php echo url('UserGroup', 'delete');?>';
		removeOne(url, id);
	});
	//获取二级组列表
	$('.twoGroupList').click(function(){
		var parent_id = $(this).attr('name');
		if($('#show_html_tr_'+parent_id).css('display') == 'none'){
			var url = "<?php echo url('UserGroup', 'showTwoGroups');?>";
			var param = {
				parent_id : parent_id
			};
			if ($.trim($('#show_html_'+parent_id).html()) == '') {
				showGroups.show(url,param);
			}
			$('.twoGroupList').removeClass('report_icon_sys2');
			$('.two_tr').hide();
			$('#show_html_tr_'+parent_id).show();
			$(this).addClass("report_icon_sys2");
			$('.one_tr').children("td").removeClass("usergroup_trsel");
			$(this).parents().children("td").addClass("usergroup_trsel");
		}else{
			$('#show_html_tr_'+parent_id).hide();
			$(this).removeClass("report_icon_sys2");
			$(this).parents().children("td").removeClass("usergroup_trsel");
		}
	});
});

</script>
<?php tpl('Common.footer_1');?>