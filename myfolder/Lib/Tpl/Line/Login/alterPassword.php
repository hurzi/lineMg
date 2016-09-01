<?php tpl("Common.header_1")?>
	<div class="con_c_t ">
		<div class="con_edit">
			<form class="addform">
				<TABLE cellpadding="0" cellspacing="0" class="t">
					<THEAD>
						<tr>
							<td colspan="2">修改密码</td>
						</tr>
					</THEAD>
					<TBODY>
						<tr>
							<td width="120">
								<span>
									<label for="name">用户名:</label>
								</span>
							</td>
							<td>
								<?php echo UHome::getUserName();?>
							</td>
						</tr>
						<tr>
							<td>
								<span>
									<label for="passwordOld">旧密码:</label>
								</span>
							</td>
							<td>
								<input name="passwordOld" id="passwordOld" type="password" class="add_input" />
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td>
								<span>
									<label for="passwordNew">新密码:</label>
								</span>
							</td>
							<td>
								<input name="passwordNew" id="passwordNew" type="password" class="add_input" />
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td>
								<span>
									<label for="passwordNewConf">确认密码:</label>
								</span>
							</td>
							<td>
								<input name="passwordNewConf" id="passwordNewConf" type="password" class="add_input" />
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><a onclick="qrOk();" href="javascript:void(0);" class="button blue medium">确认修改</a></td>
						</tr>
					</TBODY>
				</TABLE>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		var url = "<?php echo url('Login', 'updatePass');?>";

        function qrOk(){
        	var passwordOld = $.trim($('#passwordOld').val());
			var passwordNew = $.trim($('#passwordNew').val());
			var passwordNewConf = $.trim($('#passwordNewConf').val());

        	if (! passwordOld){
        		loadMack({off:'on',Limg:0,text:'旧密码不能为空',set:2000});
        		return false;
        	} else if (isEmpty(passwordNew)) {
        		loadMack({off:'on',Limg:0,text:'新密码不能为空',set:2000});
        		return false;
        	} else if (passwordOld == passwordNew) {
        		loadMack({off:'on',Limg:0,text:'新密码和旧密码不能相同',set:2000});
        		return false;
        	} else if (passwordNewConf != passwordNew) {
        		loadMack({off:'on',Limg:0,text:'新密码和确认密码不一样,请重新输入',set:2000});
        		return false;
        	}
        	var params = {
                	passwordOld:passwordOld,
                	passwordNew:passwordNew,
                	passwordNewConf:passwordNewConf
                };

        	ajaxSubmit(url, 'POST', params, function(status, result){
            	if (status == false || result.error == 0) window.location.reload();
    		}, '修改成功');
	    }
   </script>
<?php tpl("Common.footer_1")?>