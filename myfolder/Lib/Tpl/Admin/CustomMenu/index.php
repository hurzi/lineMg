<?php tpl('Common.header_1');?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<div class="button green medium addFz" id="addBtnId">添加自定义菜单</div>
			&nbsp;&nbsp;
			<div class="button green medium addFz" onclick="synchronousMenu()">同步菜单到微信</div>
			&nbsp;&nbsp;
			<!--<div class="button green medium addFz" onclick="javascript:clearWxMenu()">清除微信手机端菜单</div>-->
			<p class="hong" style="padding: 10px 5px 0 5px;">
			菜单最近一次同步到微信时间：<?php echo $lastSynchronousTime;?> &nbsp;&nbsp;菜单最近一次更新时间：<?php echo $lastUpdateTime;?>
			</p>
		</div>
	</div>
</div>
<div class="con_c_c">
	<table class="tab_con" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<td width="10"></td>
				<td width="200">菜单名称</td>
				<td width="40">排序值</td>
				<td width="60">内容类型</td>
				<td width="">消息内容</td>
				<td width="80">操作</td>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($list) {
			foreach ($list as $vo) {
				?>
			<tr>
				<td width="10"></td>
				<td><?php echo $vo['name'];?></td>
				<td><?php echo $vo['order'];?></td>
				<td><?php
					if(isset($vo['children'])){
						echo '---';
					}else{
						echo $vo['type_name'];
					}
					?>
				</td>
				<td>
				<div class="break" style="width:300px;">
					<?php
					if(isset($vo['children'])){
						echo '---';
					}else{
						echo $vo['html'];
					}
					?>
					</div>
				</td>
				<td>
					<div class="czx">
						<a name="<?php echo $vo['id'];?>" href="javascript:;" class="edit"></a>
						<a name="<?php echo $vo['id'];?>" href="javascript:;" class="del"></a>
					</div>
				</td>
			</tr>
			<?php
			if (isset($vo['children']) && ! empty($vo['children'])) {
				foreach ($vo['children'] as $v) {
					?>
			<tr>
				<td width="10"></td>
				<td>
					<span class="cjsp">----</span><?php echo $v['name'];?></td>
				<td><?php echo $v['order'];?></td>
				<td><?php echo $v['type_name'];?></td>
				<td>
					<div class="break" style="width:300px;"><?php echo $v['html'];?></div>
				</td>
				<td>
					<div class="czx">
						<a name="<?php echo $v['id'];?>" href="javascript:;" class="edit"></a>
						<a name="<?php echo $v['id'];?>" href="javascript:;" class="del"></a>
					</div>
				</td>
			</tr>
                  <?php
					}
				}
			}
			?>
		<?php } else {?>
			<tr>
				<td colspan="6" align="center">无内容</td>
			</tr>
		<?php }?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
					<div class="tab_foot"></div>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	$(function(){
		$('#addBtnId').click(function(){
				window.location.href = "<?php echo url('CustomMenu', 'add') . '&is_thread=' . $isThread;?>";
			});

			$('.edit').click(function(){
				var id = $(this).attr('name');
				window.location.href = "<?php echo url('CustomMenu', 'edit') .'&is_thread=' . $isThread;?>&id="+id;
			});
			//单个删除
			$('.del').click(function(){
				var id = $(this).attr('name');
				id =[id];
				var url = '<?php echo url('CustomMenu', 'delete');?>';
			removeOne(url, id);
		});
	});
	var synchronousWxUrl = '<?php echo url('CustomMenu', 'synchronousMenu').'&is_thread=' . $isThread;?>';
	var clearWxUrl = '<?php echo url('CustomMenu', 'clearWxMenu').'&is_thread=' . $isThread;?>';

	var msgList = <?php echo $msgList;?> || {};
	$("a.more").live('click',function(){
		var id = $(this).attr('name');
		showMessage(msgList[id]['type'], msgList[id]['data']);
	});

</script>
<?php tpl('Common.footer_1');?>