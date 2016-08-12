<?php tpl("Common.header_1")?>
	<div class="con_c_t">
		<div class="con_bzk">
			<div style="padding: 10px;">
				<div id="go_back" class="button green medium"> 返回</div>
			</div>
		</div>
	</div>
	<div class="con_c_t ">
	<?php if ($userinfo) {?>
		<div class="qf_module nav1">
			<div class="con_edit">
				<table cellpadding="0" cellspacing="0" class="t">
					<thead>
						<tr>
							<td colspan="2">编辑用户</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="120">
								<span><label for="truthname">邮编:</label></span>
							</td>
							<td>
								<input type="text"  class="add_input" name="as_code" id="as_code" value="<?php echo $userinfo['as_code'];?>"/>
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><a onclick="qrOk();" href="javascript:void(0);" class="button blue medium">提交</a></td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" id="uid" name="uid" value="<?php echo $userinfo['uid'];?>">
			</div>
		</div>
	<?php } else {?>
		<div class="">要编辑的姓名不存在</div>
	<?php }?>
	</div>
	<script type="text/javascript">
		var url = "<?php echo url('RegistUser', 'updateRegistUser');?>";
		var href = "<?php echo url('RegistUser', 'index');?>";

		function qrOk(){
			var uid = $.trim($('#uid').val());
        	var as_code = $.trim($('#as_code').val());
        	
        	if (isEmpty(as_code)){
        		loadMack({off:'on',Limg:0,text:'邮编不能为空',set:2000});
        		return false;
        	} 

        	var params = {
                	uid:uid,
                	as_code:as_code
        		};

        	ajaxSubmit(url, 'POST', params, function(status, result){
            	if (status == false || result.error == 0){ window.location.href = href };
    		}, '修改成功');
	    }


		//图片上传回调函数
		function uploadImg(url){
			$('#img').attr('src','');
			$('#img').attr('src',url);
			$('#imgArea').show();
		}

		//图片删除
		$('#delImg').click(function(){
			$('#imgArea').hide();
			$('#img').attr('src','');
		});
	</script>
<?php tpl('Common.footer_1');?>