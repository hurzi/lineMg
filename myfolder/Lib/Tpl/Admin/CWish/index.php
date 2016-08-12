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
					<option value="">选择类型</option>
				</select>
				<select class="nav_select" id="ctype" name="ctype" style="width: 80px">
					<option value="0">是否来</option>
					<option value="1">不来</option>
					<option value="2">来</option>
				</select>
				<p>
					<input type="button" class="button blue medium" value="查询" id="search-button">
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
				<td width="80">序号</td>
				<td width="80">类型</td>
				<td width="80">姓名</td>
				<td width="30">手机号</td>
				<td width="30">祝福语</td>
				<td width="30">参加婚礼人数</td>
				<td width="30">祝福时间</td>
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
				<td>
					<div>
						<?php echo $vo['id'];?>
					</div>					
				</td>
				<td><div style="width: 70px;" class="break"><?php echo $vo['type']==2?"专属":"普通";?></div></td>
				<td><div style="width: 80px;" class="break"><?php echo $vo['cname'];?></div></td>
				<td><?php echo $vo['cphone'];?></td>
				<td><?php echo $vo['cwish'];?></td>
				<td><?php echo $vo['ctype']==1?0:$vo['ccount'];?>	</td>
				<td><?php echo $vo['create_time'];?></td>
				
				<td class="editAll">
					<div class="czx">
						<a name="<?php echo $vo['id'];?>" href="javascript:;" class="del" >删除</a>
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

	
	//搜索
	$('#search-button').click(function (){
		$(this).closest('form').submit();
	});

	$('.editAll .del').click(function(){
		var url = '<?php echo url("CWish","ajax_del");?>';
		var id = $(this).attr('name');
		//删除
		var params = {
    			id : id
    	};

    	

		ajaxSubmit(url,"post", params, function (tt,type,result) {
    		try{
        		
    		}catch(e){
    			alert(result.msg);
    	 	}
    	});   
	});


});
</script>
<!--
	<link href="./Public_1/cj/popDiv/styles/core.css" type="text/css" rel="stylesheet"/>
    <script src="./Public_1/cj/popDiv/scripts/popup_layer.js" type="text/javascript" language="javascript"></script>
    -->
<?php tpl('Common.footer_1');?>