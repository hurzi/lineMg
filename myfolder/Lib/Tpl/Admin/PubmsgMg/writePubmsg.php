<?php tpl("Common.header_1")?>
	<div class="con_c_t">
		<div class="con_bzk">
			<div style="padding: 10px;">
				<div id="go_back" class="button green medium"> 返回</div>
			</div>
		</div>
	</div>
	<div class="con_c_t ">
		<div class="qf_module nav1">
			<div class="con_edit">
				<table cellpadding="0" cellspacing="0" class="t">
					<thead>
						<tr>
							<td colspan="2">发布贴子</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="120">
								<span>选择马甲</span>
							</td>
							<td>
								<select class="nav_select" id="mjopenid" name="mjopenid" style="width: 80px">
									<?php
									foreach ($majias as $key => $val) {
										?>
									<option value="<?php echo $val['openid'];?>" ><?php echo $val['truthname'];?></option>
								<?php }?>
								</select>
				
							</td>
						</tr>
						<tr>
							<td width="120">
								<span>贴子内容</span>
							</td>
							<td>
								<textarea placeholder="说几句吧..."  rows="8" cols="80" id="content" style="word-wrap:break-word; word-break:break-all;"></textarea>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span>是否匿名</span>
							</td>
							<td>
								<label><input type="radio" name="is_private" value="0" checked="checked"/>公开</label><label><input type="radio" value="1"  name="is_private" />匿名</label>
        
							</td>
						</tr>		
						<tr>
							<td></td>
							<td><a onclick="qrOk();" href="javascript:void(0);" class="button blue medium">提交</a></td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" id="uid" name="uid" value="0">
			</div>
		</div>
		<div id="system_preinstall"></div>
		
	</div>
	<script type="text/javascript">
		var url = "<?php echo url('PubmsgMg', 'ajax_addPubmsg');?>";
		var href = "<?php echo url('PubmsgMg', 'index');?>";

		function qrOk(){

			var mjopenid = $("#mjopenid").val();			
			var content = $("#content").val();			
			var is_private = $('input[name="is_private"]:checked').val();
			if(!content){
				loadMack({off:'on',Limg:0,text:'内容不能为空',set:2000});
        		return false;
			}
			if(is_private == null || is_private==undefined || is_private == ""){
				is_private = 0;
			}
			var params = {
					mjopenid:mjopenid,
					content : content,
					is_private :is_private
				};			

        	ajaxSubmit(url, 'POST', params, function(status, result){
            	if (status == false || result.error == 0) {window.location.href = href};
    		}, '添加成功');
	    }
	</script>
<?php tpl('Common.footer_1');?>