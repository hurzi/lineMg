<?php tpl('Common.header_1');?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<div class="button red medium delPubmsg">删除所选</div>
			<br/><br/>
			<form class="" style="margin-top: 5px;">
				<input class="inputS" type="text" placeholder="发布人" value="<?php echo $truthname;?>" name="truthname" id="truthname">
				
				<p>
					<input name="a" type="hidden" value="<?php echo $action;?>" /> 
					<input name="m" type="hidden" value="<?php echo $method;?>" /> 
					<input type="button" class="button blue medium" value="查询" id="search-button">
					<input type="button" class="button blue medium" value="发布帖子" id="add-button">
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
				<td width="50">用户</td>
				<td width="120">发贴内容</td>
				<td width="80">发贴时间</td>
				<td width="30">是否匿名</td>
				<td width="30">回复数</td>
				<!--  
				<td width="90">点赞数</td>
				<td>收藏数</td>
				-->
				<td width="50">操作</td>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($list) {
			foreach ($list as $vo) {
				?>
			<tr>
				<td height="25"><input type="checkbox" name="items" value="<?php echo $vo['as_pubmsg_id'];?>" /></td>
				<td>
					<div>
						<?php echo $vo['a_truthname'];?>
					</div>					
				</td>
				<td><div style="width: 270px;" class="break"><?php echo mb_substr($vo['content'], 0,100);?></div></td>
				<td><div style="width: 70px;" class="break"><?php echo $vo['create_time'];?></div></td>
				<td><?php if($vo['is_private'] == 1){echo "匿名";}else{echo "公开";};?></td>
				<td><?php echo $vo['reply_count'];?></td>
				<!-- 
				<td><?php echo $vo['zan_count'];?></td>
				<td><?php echo $vo['store_count'];?></td>
				 -->
				<td class="editAll">
					<div class="czx">
						<a name="<?php echo $vo['as_pubmsg_id'];?>" href="javascript:;" class="edit"></a>
						<a name="<?php echo $vo['as_pubmsg_id'];?>" href="javascript:;" class="del"></a>
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

	

	//搜索
	$('#search-button').click(function (){
		$(this).closest('form').submit();
	});

	//增加马甲
	$('#add-button').click(function (){
		window.location.href="<?php echo url('PubmsgMg','writePubmsg');?>";
	});
	
	$('.editAll .edit').click(function(){
		var id = $(this).attr('name');
		window.location.href = "<?php echo url('PubmsgMg','onePubmsg')?>"+"&pid="+id;
	});
	$('.editAll .del').click(function(){
		var as_pubmsg_id = $(this).attr('name');
		var url = '<?php echo url('PubmsgMg','ajax_deletePubmsg');?>';

		jsConfirm(300, '确定要删除吗？', function (){
			var params = {
				as_pubmsg_id:as_pubmsg_id
			};
			ajaxSubmit(url, 'POST', params, function(status, result){
				if(result.error==0){
					window.location.reload();
				}
			}, '删除成功');
		});		
	});
	$('.linkClass').click(function(){
		var name = encodeURIComponent($(this).attr('name'));
		window.open("<?php echo url('SessionHistory','index')?>&nickname="+name);
	});


	//批量删除
	$('.delPubmsg').click(function(){
		var url = '<?php echo url('PubmsgMg','ajax_deletePubmsgBatch');?>';

		var ids = [];
		$("input[name='items']:checkbox:checked").each(function (){
			ids.push($(this).val());
		});
		if (ids.length == 0) {
			var prompt = "请选择要操作的贴子";
			loadMack({off:'on',Limg:0,text:prompt,set:1000});
		 	return false;
	 	}
	 	
		jsConfirm(300, '确定要删除吗？', function (){
			var params = {
					as_pubmsg_id_arr:ids
			};
			ajaxSubmit(url, 'POST', params, function(status, result){
				if(result.error==0){
					window.location.reload();
				}
			}, '删除成功');
		});		
	});
	
});
</script>
<!--
	<link href="./Public_1/cj/popDiv/styles/core.css" type="text/css" rel="stylesheet"/>
    <script src="./Public_1/cj/popDiv/scripts/popup_layer.js" type="text/javascript" language="javascript"></script>
    -->
<?php tpl('Common.footer_1');?>