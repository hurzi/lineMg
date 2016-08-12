<?php tpl("Common.header_1")?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<div id="go_back" class="button green medium con_l">返回</div>
			<div class="material_update">您的修改将影响所有引用本素材的功能</div>
		</div>
	</div>
</div>
<div class="con_c_t ">
	<div class="con_edit">
		<form class="addform">
			<TABLE cellpadding="0" cellspacing="0" class="have_combo">
				<THEAD>
					<tr>
						<td colspan="2">修改图片素材</td>
					</tr>
				</THEAD>
				<TBODY>
					<tr>
						<td width="120" class="tr"><span> <label for="image_title">图片标题:</label>
						</span></td>
						<td><input type="text" id="image_title" value="<?php echo $data['title'];?>" style="width: 305px" /> <bt class="hui">(必填)</bt></td>
					</tr>
					<tr>
						<td class="tr"><span> <label for="nickname">图片上传:</label>
						</span></td>
						<td>
							<div style="width: 309px;" class="cover-area">
								<div class="oh z cover-hd">
									<iframe src="./UploadImg/Upload.php?callback=uploadImg&max_size=<?php echo $maxSize;?>" class="uploadfile" style="filter: alpha(opacity =   0); -moz-opacity: 0; -khtml-opacity: 0; opacity: 0; position: absolute; top: 0; left: 0; width: 60px; height: 30px;" id="imgUpload"></iframe>
									<a class="icon28C upload-btn" href="javascript:;">上传</a>
								</div>
								<p class="cover-bd" id="imgArea">
									<img width="309" id="img" src="<?php echo $data['path'];?>" /> <a id="delImg" class="vb cover-del" href="javascript:;">删除</a>
								</p>
							</div>
						</td>
					</tr>
					<tr>
						<td><input type="hidden" id="img_id" value="<?php echo $data['id'];?>" /></td>
						<td><a onclick="qrOk();" href="javascript:void(0);" class="button blue medium">提交</a></td>
					</tr>
				</TBODY>
			</TABLE>
		</form>
	</div>
</div>
<script type="text/javascript">
		var url = "<?php echo url('MaterialImage', 'update');?>";
		var href = "<?php echo url('MaterialImage', 'index');?>";
		//返回首页
		$('#go_back').click(function(){
			window.location.href = href;
		});

		function qrOk(){
			var title = $('#image_title').val();
			var img_url = $('#img').attr('src');
			var img_id = $('#img_id').val();

			if(title==''){
				jsAlert('图片标题不能为空。');
				return;
			}

			if(img_url==''){
				jsAlert('请选择上传的图片。');
				return;
			}

			var params = {
				'title':title,
				'img_url':img_url,
				'img_id':img_id
			};
			jsConfirm(300, '您的修改将影响所有引用本素材的功能，您确认修改么？', function (){
				ajaxSubmit(url, 'POST', params, function(status, result){
					if (status == false || result.error == 0) window.location.href = href;
				}, '修改成功');
			});
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