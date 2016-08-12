<?php tpl('Common.header_1');?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<input type="button" class="button green medium" value="增加" id="addEvalBtn"></input>
			<form class="" style="margin-top: 5px;">
				<select class="nav_select" id="subscribe" name="subscribe" style="width: 80px">
					<option value="0">类型</option>
					<?php
					foreach ($evalType as $key => $val) {
						?>
					<option value="<?php echo $key;?>" <?php if ($param['eval_type'] == $key) {echo'selected="selected"';}?>><?php echo $val;?></option>
				<?php }?>
				</select>
				<select class="nav_select" id="subscribe" name="subscribe" style="width: 80px">
					<option value="0">状态</option>
					<?php
					foreach ($evalStatus as $key => $val) {
						?>
					<option value="<?php echo $key;?>" <?php if ($param['eval_status'] == $key) {echo'selected="selected"';}?>><?php echo $val;?></option>
				<?php }?>
				</select>
				<input class="inputS" type="text" placeholder="名称" value="<?php echo $param['eval_name'];?>" name="eval_name" id="eval_name">
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
				<td width="15">ID</td>
				<td width="160">名称</td>
				<td width="80">开始时间</td>
				<td width="80">结束时间</td>
				<td width="30">类型</td>
				<td width="30">状态</td>
				<td width="90">创建时间</td>
				<td width="40">操作</td>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($list) {
			foreach ($list as $vo) {
				?>
			<tr>
				<td height="25"><?php echo $vo['eval_id']?></td>
				<td><?php echo $vo['eval_name']?></td>
				<td><?php echo $vo['eval_starttime']?></td>
				<td><?php echo $vo['eval_endtime']?></td>
				<td><?php echo $evalType[$vo['eval_type']];?></td>
				<td><?php echo $evalStatus[$vo['eval_status']]?></td>
				<td><?php echo $vo['create_time']?></td>
				<td class="editAll">
					<div class="czx">
						<a name="<?php echo $vo['eval_id'];?>" href="javascript:;" class="edit"></a>
					</div>
				</td>
			</tr>
			<?php
			}
		} else {
			?>
			<tr>
				<td colspan="8" align="center">无内容</td>
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

	
	//搜索
	$('#search-button').click(function (){
		$(this).closest('form').submit();
	});
	$('.editAll .edit').click(function(){
		var id = $(this).attr('name');
		window.location.href = "<?php echo url('ZgykdxEvaluating','update')?>"+"&eval_id="+id;
	});

	$('#addEvalBtn').click(function(){
		window.location.href = "<?php echo url('ZgykdxEvaluating','add')?>";
	});
});
</script>
<?php tpl('Common.footer_1');?>