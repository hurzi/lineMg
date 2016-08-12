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
							<td colspan="2">增加评教</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="120">
								<span><label for="eval_name">名称:</label></span>
							</td>
							<td>
								<input type="text"  class="add_input" name="eval_name" id="eval_name" value=""/>
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span><label for="eval_descript">描述</label></span>
							</td>
							<td>
								<textarea rows="5" cols="100" id="eval_descript" name="eval_descript"></textarea>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span><label for="eval_starttime">时间范围</label></span>
							</td>
							<td>
								<input type="text"  class="add_input" name="eval_starttime" id="eval_starttime" value=""/>
								--<input type="text"  class="add_input" name="eval_endtime" id="eval_endtime" value=""/>
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span><label for="eval_type">类型</label></span>
							</td>
							<td>
								<select id="eval_type" name="eval_type">
								<?php foreach ($evalType as $key=>$val){?>
									<option value="<?=$key ?>"><?=$val;?></option>
								<?php }?>
								</select>
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span><label for="eval_status">状态</label></span>
							</td>
							<td>
								<select id="eval_status" name="eval_status">
								<?php foreach ($evalStatus as $key=>$val){?>
									<option value="<?=$key ?>"><?=$val;?></option>
								<?php }?>
								</select>
								<bt class="hui">(必填)</bt>
							</td>
						</tr>
						<tr>
							<td width="120">
								<span><label for="eval_name">用户答题数量:</label></span>
							</td>
							<td>
								<input type="text"  class="add_input" name="eval_max_topic" id="eval_max_topic" value=""/>
								<p>填0代表答所有的题</p>
							</td>
						</tr>	
						<tr>
							<td width="120">
								
							</td>
							<td>
								<div id="submit_dynamic" class="button green medium">提交</div>
							</td>
						</tr>					
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var setUrl = "<?php echo url('ZgykdxEvaluating', 'ajax_addEvaluating');?>";
		var href = "<?php echo url('ZgykdxEvaluating', 'index');?>";


		//修改菜单获取表单数据
		function getFormData(msgData) {
			msgData = msgData || {};
			var eval_name = $.trim($('#eval_name').val());
			var eval_descript = $.trim($('#eval_descript').val());
			var eval_starttime = $.trim($('#eval_starttime').val());
			var eval_endtime = $.trim($('#eval_endtime').val());
			var eval_type = $.trim($('#eval_type').val());
			var eval_status = $.trim($('#eval_status').val());
			var eval_max_topic = parseInt($('#eval_max_topic').val());

			

			if (eval_name == '') {
				Common.alert('请输入名称');
				return false;
			} else if(eval_starttime == ''){
				Common.alert('请选择开始时间');
				return false;
			}else if(eval_endtime == ''){
				Common.alert('请选择结束时间');
				return false;
			}

			var params = {
					eval_name : eval_name,
					eval_descript : eval_descript,
					eval_starttime : eval_starttime,
					eval_endtime : eval_endtime,
					eval_type : eval_type,
					eval_status : eval_status,
					eval_max_topic : eval_max_topic
			};
			if(doAjax == true){
				return;
			}
			doAjax == true;
			ajaxSubmit(setUrl, 'POST', params, function(status, result){
				doAjax = false;
				if (status == false || result.error == 0) window.location.href = href;
			}, '修改成功');
		}
	</script>
<?php tpl('Common.footer_1');?>