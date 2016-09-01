<?php tpl("Common.header")?>
<div class="bodyTitle">
	<div class="bodyTitleLeft"></div>
	<div class="bodyTitleText">添加管理员</div>
    <div style="float: right;padding:8px 10px;">
        <a href="<?php echo url('EntSuperAdmin', 'index');?>" class="sgbtn"> << 返回管理员列表</a>
    </div>
</div><br />
<form method="post" action="#" onsubmit="" name="form1">
	<table width="100%">
		<tr>
			<td width="90" align="center">登陆账号:</td>
			<td>
				<input size="32" name="username" type="text" class="txt" id="username" value="" />
				<strong class="cr"> * </strong>
				<strong class="cr" id="username_prompt"></strong>
			</td>
		</tr>
		<tr>
			<td width="90" align="center">姓名:</td>
			<td>
				<input size="32" name="nickname" type="text" class="txt" id="nickname" value="" />
				<strong class="cr"> * </strong>
				<strong class="cr" id="nickname_prompt"></strong>
			</td>
		</tr>
		<tr>
			<td width="90" align="center">初始密码:</td>
			<td>
				<input name="password"  type="checkbox" id="password" disabled="disabled" checked="checked" />
				<label for="che">自动生成(123456)</label>
				<strong class="cr" id="password_prompt"></strong>
			</td>
		</tr>
		<tr>
			<td width="90" align="center">所属企业:</td>
			<td>
				<select id="ent_id" name="ent_id">
					<option value="0">请选择</option>
				<?php if($entList){
					foreach ($entList as $ent){
				?>
					<option value="<?php echo $ent['ent_id'];?>"><?php echo $ent['ent_name'];?></option>
					<?php }}?>
				</select>
				<strong class="cr"> * </strong>
				<strong class="cr" id="ent_id_prompt"></strong>
			</td>
		</tr>
        <tr>
            <td width="90" align="center">管理员级别:</td>
            <td>
                <select id="level" name="level">
                    <option value="0">请选择</option>
                    <?php if($levelList){
                        foreach ($levelList as $k=>$v){
							if(UHome::getUserLevel()!=1 && $k == 1){
								continue;
							}
                            ?>
                            <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php }}?>
                </select>
                <strong class="cr"> * </strong>
                <strong class="cr" id="ent_id_prompt"></strong>
            </td>
        </tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td>

				<input type="button" name="Submit2" value="添加管理员" class="inputButton" id="submit" />
				<input type="reset" name="Reset" value="还原重填" class="inputButton" />
			</td>
		</tr>
	</table>
</form>

<p class="i">红色标记的内容必须填写</p>
<br>
<script type="text/javascript">
$(function (){

	var url = "<?php echo url('EntSuperAdmin', 'insert');?>";
	var href = "<?php echo url('EntSuperAdmin', 'index');?>";
	$('#submit').click(function (){
		checkSubmit(url, href);
	});
});
/**
 * 功能JS文件
 */
//新建/编辑检测提交
function checkSubmit(url, href){
	var username 		= $.trim($('#username').val());
	var nickname	    = $.trim($('#nickname').val());
	var password		= 123456;
	var ent_id		    = $.trim($('#ent_id').val());
    var level           = $('#level').val();

	showmsg('username_prompt', '');
	showmsg('nickname_prompt', '');
	showmsg('password_prompt', '');
	showmsg('ent_id_prompt', '');


	if (username == null || username == ''){
		showmsg('username_prompt', '管理员登陆名不能为空');
		return false;
	} else if (nickname == '' ||nickname== null) {
		showmsg('nickname_prompt', '管理员姓名不能为空');
		return false;
	} else if((password == null || password == '') && display == 'Y'){
		showmsg('password_prompt', '密码不能为空');
		return false;
	} else if(ent_id == 0 || ent_id == null || ent_id == ''){
        if(level!=1 && level!=6){
            showmsg('ent_id_prompt', '请选择管理员所属的企业');
            return false;
        }
	}


	var params = {
			username 	: username,
			nickname    : nickname,
			password    : password,
			ent_id      : ent_id,
            level       : level
		};
	submit_operate(url,params,href);
}
</script>
<?php tpl("Common.footer")?>