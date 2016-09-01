<?php tpl("Common.header")?>
<div class="bodyTitle">
	<div class="bodyTitleLeft"></div>
	<div class="bodyTitleText">管理员账号管理</div>
</div><br />
<!-- 搜索 -->
<form method="get" name="searchform" action="<?php echo url('EntSuperAdmin', 'index');?>" id="search-form">
	<table class="fixwidth search-bar" >
		<tr>
			<td>
				管理员姓名:
				<input name="keyword" type="text" value="<?php echo $keyword;?>" class="txt" id="keyword" />
				企业名称:
				<input name="ent_name" type="text" value="<?php echo @$entName;?>" class="txt" id="ent_name" />
				<select name="level">
					 <option value="0">请选择用户类型</option>
                    <?php if($levelList){
                        foreach ($levelList as $k=>$v){
							if(UHome::getUserLevel()!=1 && $k == 1){
								continue;
							}
                            ?>
                            <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php }}?>
				</select>
				&nbsp;&nbsp;&nbsp;
				<input name="a" type="hidden" value="<?php echo $action;?>" />
				<input name="m" type="hidden" value="<?php echo $method;?>" />
				<input id="search-button" type="button" value="搜索" class="inputButton" style="height:21px;" />
                <div style="float: right;padding: 5px 10px;">
                    <a class="sgbtn" href="<?php echo url('EntSuperAdmin', 'add');?>">＋添加管理员</a>
                </div>
			</td>
		</tr>
	</table>
</form>

<form name="form2" method="post" action="#">
	<table class="datalist fixwidth" id="main_tb">
		<tr>
			<th style="text-align:center; width:60px;">
                <input name="checkbox" type="checkbox" class="checkbox" onClick="selAll(this)" value="选择全部">ID
            </th>
			<th >登陆账号</th>
			<th >姓名</th>
			<th>所属企业</th>
			<th>创建日期</th>
			<th >用户类型</th>
			<th style="text-align:center;">操作</th>
		</tr>
		<?php
		if ($list) {
			foreach ($list as $vo) {
		?>
        <tr>
			<td style="text-align:center; width:60px;">
                <input name="ids[]" type="checkbox" id="ids[]" value="<?php echo $vo['user_id'];?>" class="checkbox" <?php if($vo['level']==1){ echo "disabled"; } ?>>
				<?php echo $vo['user_id'];?>
			</td>
			<td><?php echo $vo['username'];?></td>
			<td><?php echo $vo['nickname'];?></td>
            <?php if($vo['level']!=1): ?>
			<td><?php echo $ent_name = isset($entList[$vo['ent_id']]) ? $entList[$vo['ent_id']] : 'ID:'.$vo['ent_id'];?></td>
			<?php else : ?>
            <td><span style="color: red;">[Adsit]</span></td>
            <?php endif; ?>
            <td><?=date('Y-m-d H:i:s',$vo['create_time']);?></td>
			<th><?php echo $levelList[$vo['level']];?></th>
			<td style="text-align:center;">
				<a href="javascript:;" name="<?php echo $vo['user_id'];?>"  class="edit">
					<img align="middle" alt="编辑" src="./Public/images/edit.gif">
				</a>
				<?php if(in_array($vo['level'], array(1,6))){?>
				<a href="<?php echo url("EntSuperAdmin","adsitUserMenu",array("user_id"=>$vo['user_id']))?>"  class="edit">
					设置权限
				</a>
				<?php }?>
			</td>
		</tr>
		<?php }?>

		<tr class="nobg">
			<td colspan="3">
				<font color="red">操作类型：</font>
				<input name="act" type="radio" class="checkbox" checked="checked" value="delete">
				删除
				<input id="batch-delete" type="button" value="提交操作" class="inputButton" style="height:21px;" />
			</td>
			<td colspan="7" class="tdpage" align="right"><div class="tab_foot"><?php echo $page;?></div></td>
		</tr>
		<?php }else{?>
		<tr>
			<td colspan="7" align="center">----无内容----</td>
		</tr>
		<?php }?>
	</table>
</form>

<p class="i">删除不可恢复,谨慎操作</p>
<br>
<script type="text/javascript">
	$(function () {
		//搜索
		$('#search-button').click(function (){
			$('#search-form').submit();
		});
		//批量删除
		$('#batch-delete').click(function () {
			var url	= "<?php echo url('EntSuperAdmin','delete');?>";
			var ids = [];
			$("input[name='ids[]']:checkbox:checked").each(function (){
				ids.push($(this).val());
			});
			delete_operate(url,ids);

		});
		$('.edit').click(function(){
			var name = encodeURIComponent($(this).attr('name'));
				window.location.href = "<?php echo url('EntSuperAdmin', 'edit');?>&id="+name;
		});
	});
</script>
<?php tpl("Common.footer")?>