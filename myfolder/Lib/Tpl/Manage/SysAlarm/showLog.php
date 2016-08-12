<?php tpl('Common.header_1');?>
<div class="bodyTitle">
	<div class="bodyTitleLeft"></div>
	<div class="bodyTitleText">系统警告管理</div>
</div>
<br>
<h3 class="marginbot">
	<a href="<?php echo url('SysAlarm', 'index');?>" class="sgbtn"> << 返回系统警告管理</a>
</h3>
<?php if($SysAlarm):?>
<form method="post" action="#" name="form1">
	<table width="100%">
		<tr>
			<td width="90" align="center">系统名称:</td>
			<td>
				<?=$SysAlarm['system_name'];?>
			</td>
		</tr>
        <tr>
			<td width="90" align="center">警告标识:</td>
			<td>
				<?=$SysAlarm['alarm_name'];?>
			</td>
		</tr>
        <tr>
			<td width="90" align="center">警告描述:</td>
			<td>
				<?=$SysAlarm['alarm_desc'];?>
			</td>
		</tr>
		<tr>
			<td width="90" align="center">警告位置:</td>
			<td>
				<?=$SysAlarm['action_name'];?>/<?=$SysAlarm['method_name'];?>
			</td>
		</tr>
        <tr>
			<td width="90" align="center">警告时间:</td>
			<td>
				<?=date("Y-m-d H:i:s",$SysAlarm['create_time']);?>
			</td>
		</tr>
        <tr>
			<td width="90" align="center">警告日志:</td>
			<td>
				<?=$SysAlarm['param_log'];?>
			</td>
		</tr>
        
	</table>
</form>
<?php else:?>
<div>要编辑的信息不存在</div>
<?php endif?>

<br>
<?php tpl("Common.footer")?>