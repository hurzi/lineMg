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
				<TABLE cellpadding="0" cellspacing="0" class="t">
					<THEAD>
						<tr>
							<td colspan="2">修改客户分组</td>
						</tr>
					</THEAD>
					<TBODY>
						<tr>
							<td width="120">
								<span>
									<label for="pid">所属父级分组：</label>
								</span>
							</td>
							<td>
								<select id="pid" class="add_input"  style="width:167px">
				            		<option value="0">--根组--</option>
				              	<?php foreach($groupList as $k => $v ){ ?>
									<option value="<?php echo $v['ug_id']; ?>" <?php if ($v['ug_id'] == $group['parent_id']) {echo 'selected="selected"';}?>><?php echo $v['ug_name']; ?></option>
				              	<?php }?>
				                </select>
								<bt class="hui"> (如不选,默认一级)</bt>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span>
									<label for="name">分组名称:</label>
								</span>
							</td>
							<td>
								<input type="text" id="name" class="add_input" value="<?php echo $group['ug_name'];?>"/>
								<bt class="hui">(必填)</bt>
							</td>
						</tr>

						<tr>
							<td></td>
							<td><a onclick="qrOk();" href="javascript:void(0);" class="button blue medium">提交</a></td>
						</tr>
					</TBODY>
				</TABLE>
				<input type="hidden" id="id" value="<?php echo $group['ug_id'];?>"/>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		var url = "<?php echo url('UserGroup', 'update');?>";
		var href = "<?php echo url('UserGroup', 'index');?>";
		//返回首页
		$('#go_back').click(function(){
			window.location.href = href;
		});

		/**
		 * 客户分组JS文件
		 */
		//新建/编辑检测提交
		function qrOk(){
			var id = $.trim($('#id').val());
			var name = $.trim($('#name').val());
			var pid = $('#pid').val();

			if (isEmpty(name)){
        		loadMack({off:'on',Limg:0,text:'分组名称不能为空',set:2000});
        		return false;
        	}

			var params = {
					id:id,
					name:name,
					pid:pid
				};
			ajaxSubmit(url, 'POST', params, function(status, result){
            	if (status == false || result.error == 0) window.location.href = href;
    		}, '修改成功');
		}
   </script>
<?php tpl('Common.footer_1');?>