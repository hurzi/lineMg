<?php tpl("Common.header_1")?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<div id="go_back" class="button green medium">返回</div>
		</div>
	</div>
</div>
<div class="con_c_t ">
	<div class="qf_module nav1">
		<div class="con_edit">
			<table cellpadding="0" cellspacing="0" class="t">
				<thead>
					<tr>
						<td colspan="2">编辑马甲</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="120"><span><label for="truthname">马甲名称:</label></span>
						</td>
						<td><input type="text" class="add_input" name="truthname"
							id="truthname" value="" /> <bt class="hui">(必填)</bt></td>
					</tr>
					<tr>
						<td width="120"><span>头像</span></td>
						<td>
							<div style="width: 309px;" class="cover-area">
								<div class="oh z cover-hd">
									<iframe src="./UploadImg/Upload.php?callback=uploadImg"
										class="uploadfile"
										style="filter: alpha(opacity = 0); -moz-opacity: 0; -khtml-opacity: 0; opacity: 0; position: absolute; top: 0; left: 0; width: 60px; height: 30px;"
										id="imgUpload"></iframe>
									<a class="icon28C upload-btn" href="javascript:;">上传</a>
								</div>
								<p class="cover-bd" id="imgArea" style="display: none;">
									<img width="309" id="img" src="">
									<a id="delImg" class="vb cover-del" href="javascript:;">删除</a>
								</p>
							</div>
						</td>
					</tr>

					<tr>
						<td></td>
						<td><a onclick="qrOk();" href="javascript:void(0);"
							class="button blue medium">提交</a></td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" id="uid" name="uid" value="0">
		</div>
	</div>
	<div id="system_preinstall"></div>

</div>
<script type="text/javascript">
		var url = "<?php echo url('RegistUser', 'insert');?>";
		var href = "<?php echo url('RegistUser', 'index');?>";

		function qrOk(){
        	var truthname = $.trim($('#truthname').val());
        	var headimgurl = $('#img').attr('src');
        	
        	if (isEmpty(truthname)){
        		loadMack({off:'on',Limg:0,text:'马甲名不能为空',set:2000});
        		return false;
        	} 

        	if(headimgurl==''){
        		loadMack({off:'on',Limg:0,text:'请选择上传的图片',set:2000});
        		return;
			}
        	

        	var params = {
        			truthname:truthname,
        			headimgurl:headimgurl
        		};

        	ajaxSubmit(url, 'POST', params, function(status, result){
            	if (status == false || result.error == 0) {window.location.href = href};
    		}, '添加成功');
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