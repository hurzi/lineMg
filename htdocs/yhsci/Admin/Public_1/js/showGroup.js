/**
 * 显示二级组
 */
var showGroups = {
   
	show : function(getGroupUrl, param) 
	{
		param = param || {};
		getGroupUrl = getGroupUrl || {};
		this._param = {
				edit_url : param.edit_url || '/Admin/index.php?a=UserGroup&m=edit',
				delete_url : param.del_url || '/Admin/index.php?a=UserGroup&m=delete'	
		};
		this._getGroupurl = getGroupUrl;
		this._parent_id = param.parent_id;
		
		this.loadData();
		this.listen(param);
	},

	loadData : function ()
	{
		var page = arguments[0] ? arguments[0] : 1;
		var data = {
				p : page,
				parent_id : this._parent_id,
				callback : 'showGroups.loadData'
			};
		var _this = this;
		$.ajax({
			url : this._getGroupurl,
			type : 'post',
			data : data,
			dataType : 'json',
			beforeSend : function() {
				loadMack({
					off : 'on'
				});
			},
			complete : function() {
				loadMack({
					off : 'off'
				});
			},
			success : function(result) {
				var data = result.data;
				if (result.error != 0) {
					jsAlert(result.msg);
					return ;
				}
				var html = _this.createTpl(data);
				//alert(html);
				$('#show_html_'+_this._parent_id).html(html);
				$('#show_html_tr_'+_this._parent_id).show();
				parentSH();
			}
		});
	},

	createTpl : function(result)
	{
		if(!result) return;
		var _this = this;
		var list = result.list;// 内容
		var page = result.page; // 分页
		var create_type_list = result.create_type;  //创建类型
		
		var head_html = '<table class="tab_two tab_two_usergroup" cellpadding="0" cellspacing="0">'
			   +'<tbody>';
		var foot_html =	'<tr>'
			   +'	<td colspan="6">'
			   +'		<div class="tab_foot">'+page+'</div>'
			   +'	</td>'
			   +'</tr>'
			   +'</tbody>'
			   +'</table>' ;
	
		var body_html = '';
		$.each(list, function(i, obj){
			var group_id = obj.group_id;
			var group_name = obj.group_name;
			var count = obj.count;
			var create_type = obj.create_type;
			var create_time = obj.create_time;
			var operate_html = '---';
			if(create_type == 1 ) {
				operate_html = '    <div class="czx">'
						  +'        <span style="display:inline-block;width:13px;"></span>'
						  +'		<a name="'+group_id+'" href="javascript:void(0);" class="edit"></a>'
						  +'	    <a name="'+group_id+'" href="javascript:void(0);" class="del"></a>'
						  +'	</div>';
						 
			};			
			body_html += '<tr>'
				+'    <td width="5"></td>'
				+'    <td width="242"><div style="padding-left:24px;">'+group_name+'</div></td>'
				+'	  <td align="center" width="96">'+count+'</td>'
				+'	  <td width="132">'+create_type_list[create_type]+'</td>'
				+'	  <td>'
				+'		<span class="hui">'+create_time+'</span>'
				+'	  </td><td width="80">'
				+operate_html
				+'</td></tr>';
		});
		return head_html+body_html+foot_html;
	},
	
	listen : function(param)
	{	
		var _this = this;
		//编辑
		$('.edit').die().live('click', function() {
			var id = $(this).attr('name');
			window.location.href = _this._param.edit_url+"&id="+id;
		});
		//单个删除
		$('.del').die().live('click',function(){
			var id = $(this).attr('name');
			removeOne(_this._param.delete_url, id);
		});
	}
};