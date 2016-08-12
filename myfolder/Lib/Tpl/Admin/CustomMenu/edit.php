<?php tpl("Common.header_1")?>
	<div class="con_c_t">
		<div class="con_bzk">
			<div style="padding: 10px;">
				<div id="go_back" class="button green medium"> 返回</div>
			</div>
		</div>
	</div>
	<div class="con_c_t ">
	<?php if ($menu) {?>
		<div class="qf_module nav1">
			<div class="con_edit">
				<table cellpadding="0" cellspacing="0" class="t">
					<thead>
						<tr>
							<td colspan="2">编辑自定义菜单</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="120">
								<span><label for="menuName">菜单名称:</label></span>
							</td>
							<td>
								<input type="text"  class="add_input" name="menuName" id="menuName" value="<?php echo $menu['name'];?>"/>
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span><label for="menuOrder">菜单排序值:</label></span>
							</td>
							<td>
								<input type="text"  class="add_input" name="menuOrder" id="menuOrder" value="<?php echo $menu['order'];?>"/>
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span><label for="name">上级菜单:</label></span>
							</td>
							<?php
								$parentId = $menu['parent'];
								$disa = '';
								if($parentId == 0){
									$disa = "disabled='disabled'";
								}
							?>
							<td>
								<select id="parentId" name="parentId" style="width:160px;">
									<option  value="0" <?php if($parentId == 0){echo "selected='selected'";} echo $disa;?>>--根菜单--</option>
									<?php
									if($parentList){
										foreach($parentList as $k => $v ){
											$menuId = $v['id'];
									?>
										<option  value="<?php echo $menuId; ?>" <?php if($parentId == $menuId){echo "selected='selected'";} echo $disa; ?> >
											<?php echo $v['name']; ?>
										</option>
									<?php }}?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="120" ><span><label>消息来源:</label></span></td>
				            <td>
				              	<input value="1" <?php if($menu['type'] == 1) echo 'checked="checked"';?> type="radio" name="menuType" id="menuType1"><label for="menuType1">固定返回</label>
				              	<input value="2" <?php if($menu['type'] == 2) echo 'checked="checked"';?> type="radio" name="menuType" id="menuType2"><label for="menuType2">动态获取</label>
				              	<input value="3" <?php if($menu['type'] == 3) echo 'checked="checked"';?> type="radio" name="menuType" id="menuType3"><label for="menuType3">访问网页</label>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" id="menuId" name="menuId" value="<?php echo $menu['id'];?>">
			</div>
		</div>
		<div id="system_preinstall" class="<?php if($menu['type'] != 1) echo "hide";?>">
		</div>
		<div id="dynamic_acquire" class="<?php if($menu['type'] != 2) echo "hide";?>">
			<div class="qf_module nav2">
				<div class="tab_n">
					<div style="padding:10px;">
						<span>动态获取url：</span>
						<input type="text" name="dynamic_url" id="dynamic_url" size="50" value="<?php echo $menu['url'];?>" />
						<bt class="hui">(必填,地址必须以http://或https://开头)</bt>
					</div>
					<div style="padding: 5px 15px 20px 15px;">
						<div id="submit_dynamic" class="button green medium">确认修改</div>
					</div>
				</div>
			</div>
		</div>
		<div id="visit_webpage" class="<?php if($menu['type'] != 3) echo "hide";?>">
			<div class="qf_module nav2">
				<div class="tab_n">
					<div style="padding:10px;">
						<span>访问网页url：</span>
						<input type="text" name="visit_url" id="visit_url" size="50" value="<?php echo $menu['url'];?>" />
						<bt class="hui">(必填,地址必须以http://或https://开头)</bt>
					</div>
					<div style="padding: 5px 15px 20px 15px;">
						<div id="submit_webpage" class="button green medium">确认修改</div>
					</div>
				</div>
			</div>
		</div>
	<?php } else {?>
		<div class="">要编辑的菜单不存在</div>
	<?php }?>
	</div>
	<script type="text/javascript">
		var setUrl = "<?php echo url('CustomMenu', 'update').'&is_thread=' . $isThread;?>";
		var href = "<?php echo url('CustomMenu', 'index').'&is_thread=' . $isThread;?>";
		var isThread = "<?php echo $isThread; ?>";

		//已选消息内容（messageSelector组件用到变量）
		var messageContent = <?php echo $messageContent; ?>;
		$(function() {
			var oauth_set = true;
			var oauth_checked = false;
			if (messageContent) {
				if (1 == messageContent.use_oauth) {
					oauth_checked = true;
				}
			}
			//messageSelector组件构造方法传递参数
			var param = {
					callback : msgCallback,
					btn_value: '确认修改',
					remark: '',
					oauth_set: oauth_set,//是否显示选择OAuth选项
					oauth_checked: oauth_checked //当前选中状态
			};
			if (1 == isThread) {
				param.msg_types = ['text','news','music','image','voice','video'];
			} else {
				param.msg_types = ['text','news','music'];
			}

			//声明messageSelector组件对象
			var msgSelector = new MessageSelector(param, messageContent);
			//在括号里的ID中显示组件
			msgSelector.render("system_preinstall");

		});
	</script>
<?php tpl('Common.footer_1');?>