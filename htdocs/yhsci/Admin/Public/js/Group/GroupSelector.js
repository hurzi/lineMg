//这是选取客服组和客户组的js组件
function GroupSelector (getGroupUrl, param) {
	param = param || {};
	getGroupUrl = getGroupUrl || {};
	this._param = {
			callback: param.callback || null,
			defaultSelected: param.defaultSelected || null,//[{id:parent_id,name:parent_name, child:[{id:gid, name:gname}]}]
			maxNum: param.maxNum || -1, //允许选择几个,-1为不限
			title: param.title || '选取组',
			targetDiv: param.targetDiv || '',
			search: param.search || '',
			pagedVar: param.pagedVar || 'p',
			top: param.top || undefined ,
			invokeFrom : param.invokeFrom || 'admin',
			callbackParam : param.callbackParam || 'admin', //客服前端回调函数增加参数
			openId : param.openId || null,
			optAll: param.all,
			noGroup: param.noGroup
	};
	this._getFirstGroupUrl = getGroupUrl.one;
	this._getTwoGroupUrl = getGroupUrl.two;
	/**选中数据{parent_id:{id:'',name:'',child:[{id:'', name:'',count:''}]}}*/
	this._data = [];
	this._selectedNum = 0;
	this._firstGroupCurr = {id:0, name:''};//当前一级组
	
	this._ajaxData = {
			firstGroup:{page:1, data:[]},//data:1:[],2:[]
			twoGroup: {page:1, data:[]} //data:{firstGroupId: [1:[],2:[]]}
	};
	this._fetching = {one:false, two: false};
	
	this._id = GroupSelector.ID;
	this._showNum = 0;
	GroupSelector.ID ++;
}
GroupSelector.ID = 1;
GroupSelector.prototype.setDefaultData = function (data) {
	this._param.defaultSelected = data;
};

GroupSelector.prototype.getData = function() {
	return this._data;
};
GroupSelector.prototype.getTwoGIds = function() {
	var ids = [];
	if (this._showNum > 0) {
		var data = this._data || [];
	} else {
		var data = this._param.defaultSelected || [];
	}
	for (var id=0,len=data.length;id<len;id++) {
		var dd = data[id]['child'];
		if (!dd) continue;
		for (var i=0,len2=dd.length; i<len2; i++) {
			if (dd[i]['id']) ids.push(dd[i]['id']);
		}
	}
	return ids;
};
GroupSelector.prototype.getDefaultTwoGIds = function() {
	var ids = [];
	if (!this._param.defaultSelected) {
		return ids;
	}
	var def = this._param.defaultSelected;
	for (var id=0,len=def.length; id<len; id++) {
		var dd = def[id]['child'];
		if (!dd) continue;
		for (var i=0,len2=dd.length; i<len2; i++) {
			if (dd[i]['id']) ids.push(dd[i]['id']);
		}
	}
	return ids;
};

GroupSelector.prototype.show = function () {
	this._data = [];
	this._selectedNum = 0;
	this._firstGroupCurr = {id:0, name:''};
	this._ajaxData.firstGroup.page = 1;
	this._ajaxData.twoGroup.page = 1;
	$(this._getId(GroupSelector.FRAME_IDS.CHECK_ALL, '#')).attr('checked', false);
	$(this._getId(GroupSelector.FRAME_IDS.NO_GROUP, '#')).attr('checked', false);
	this._show();
	this._showNum ++;
};
//唯一
GroupSelector.prototype._getId = function(id, suffix) {
	suffix = suffix || '';
	return suffix + this._id + '_group_' + id;
};
//显示dom
GroupSelector.prototype._show = function() {
	this._createFrameDom();
	if (this._param.defaultSelected) {
		var _default = this._param.defaultSelected;
		for (var i=0,len=_default.length; i<len; i++) {
			if (_default[i]['child'] && _default[i]['child'].length > 0) {
				for (var ii=0,len2=_default[i]['child'].length; ii<len2; ii++) {
					if (-1 == _default[i]['child'][ii]['id']) {//未分组
						$(_this._getId(GroupSelector.FRAME_IDS.NO_GROUP, '#')).attr('checked', true);
						this._addChecked(_default[i]['child'][ii], {id:_default[i]['id'], name:_default[i]['name']});
					} else if (-2 == _default[i]['child'][ii]['id']) {//不限
						$(this._getId(GroupSelector.FRAME_IDS.CHECK_ALL, '#')).attr('checked', true);
						this._allClick(true);
					} else {
						this._addChecked(_default[i]['child'][ii], {id:_default[i]['id'], name:_default[i]['name']});
					}
				}
			}
		}
	}
	this._getFirstGroupData();
	this._listen();
};
GroupSelector.prototype.renderDefault = function () {
	if (!this._param.defaultSelected || !this._param.targetDiv) {
		return;
	}
	var _default = this._param.defaultSelected;
	var html = '';
	for (var i=0,len=_default.length; i<len; i++) {
		if (_default[i]['child'] && _default[i]['child'].length > 0) {
			html += "<div style='display:block;clear: both;' ><div class='group_s_d1'>"+_default[i]['name']+"：</div>";
			for (var ii=0,len2=_default[i]['child'].length; ii<len2; ii++) {
				html += "<div class='group_s_d2'>"
				html += "<a class='a1' href='javascript:;'>"+_default[i]['child'][ii]['name']+"</a>"
				html += "</div>";
			}
			html += "</div>";
		}
	}
	if (html) {
		$('#'+this._param.targetDiv).html(html);
	}
}

GroupSelector.prototype._listen = function() {
	//1.点击一级组-显示对应二级组
	_this = this;
	$(this._getId(GroupSelector.FRAME_IDS.FIRST_GROUP_CON, '#') + ' .first_group')
		.die().live('click', function () {
			var firstGroupId = $(this).attr('ugid');
			if (_this._firstGroupCurr.id == firstGroupId
					|| _this._fetching.one == true || _this._fetching.two == true) {
				return;
			}
			$(_this._getId(GroupSelector.FRAME_IDS.FIRST_GROUP_CON, '#') + ' .one').css('background-color','');
			$(this).parent().css('background-color', '#D3D3D3');
			_this._firstGroupCurr = {id: firstGroupId, name:$(this).html()};
			_this._ajaxData.twoGroup.page = 1;
			_this._getTwoGroupData(firstGroupId);
			return false;
		});
	//2，点击二级组-显示到已选择组中
	$(this._getId(GroupSelector.FRAME_IDS.TWO_GROUP_CON, '#') + ' .two_group')
		.die().live('click', function () {
			if (_this._fetching.two == true) {
				return false;
			}
			var group_id = $(this).attr('ugid');
			var group_var = $(this).find('[name=ug_name]').html();
			var count = $(this).find('[name=count]').html();
			if (_this._addChecked({id:group_id, name:group_var, count:count})) {
				$(this).css('background-color','#D3D3D3');
			}
			return false;
		});
	//3,删除选中数据
	$(this._getId(GroupSelector.FRAME_IDS.SELECTED_CON, '#') + ' .group_del')
		.die().live('click', function () {
			var group_id = $(this).attr('ugid');
			var parent_id = $(this).attr('parent_id');
			_this._delChecked(group_id, parent_id);
			return false;
		});
	//4,一级组搜索按钮
	$(this._getId(GroupSelector.FRAME_IDS.SEARCH_FORM, '#'))
		.die().submit(function () {
			if (_this._fetching.one == true) {
				return false;
			}
			_this._ajaxData.firstGroup.page = 1;
			_this._getFirstGroupData();
			return false;
		});
	//5,点击确定按钮
	$(this._getId(GroupSelector.FRAME_IDS.OK_BTN, '#'))
		.die().click(function () {
			_this._ok();
			return false;
		});
	$(this._getId(GroupSelector.FRAME_IDS.CANCEL_BTN, '#'))
		.die().click(function () {
			_this._data = _this._param.defaultSelected || [];
			$(_this._getId(GroupSelector.FRAME_IDS.ONLY_ID, '#')+' .jsbox_close').click();
			return false;
		});
	//6,分页监听
	$(this._getId(GroupSelector.FRAME_IDS.GROUP_1_PAGE, '#')+' .g_page')
		.die().live('click', function () {
			if (_this._fetching.one == true) {
				return false;
			}
			var page = $(this).attr('page');
			_this._getFirstGroupData(page);
			return false;
		});
	//7,全选按钮
	$(this._getId(GroupSelector.FRAME_IDS.CHECK_ALL, '#'))
		.die().click(function () {
			_this._allClick($(this).attr('checked'));
		});
	//8,全选按钮未分组
	$(this._getId(GroupSelector.FRAME_IDS.NO_GROUP, '#'))
		.die().click(function () {
			if ($(this).attr('checked')) {
				_this._addChecked({'id':-1,'name':'未分组'}, {'id':-1,'name':''});
			} else {
				_this._delChecked(-1, -1);
			}
		});
};

GroupSelector.prototype._allClick = function(flag) {
	if (flag) {
		$(this._getId(GroupSelector.FRAME_IDS.NO_GROUP, '#')).attr('disabled', true);
		$(this._getId(GroupSelector.FRAME_IDS.SELECTED_CON, '#')).hide('slow');
		$(this._getId(GroupSelector.FRAME_IDS.SELECTED_NUM_CON, '#')).hide();
	} else {
		$(this._getId(GroupSelector.FRAME_IDS.NO_GROUP, '#')).attr('disabled', false);
		$(this._getId(GroupSelector.FRAME_IDS.SELECTED_CON, '#')).show('slow');
		$(this._getId(GroupSelector.FRAME_IDS.SELECTED_NUM_CON, '#')).show();
	}
}

//生成dom数据
GroupSelector.prototype._createFrameDom = function() {
	var maxHtml = '';
	if (this._param.maxNum != -1) {
		maxHtml = "<span>最多可选&nbsp;"+"<span class='max_tip'>"+this._param.maxNum+"</span>&nbsp;个组</span>";
	}
	var topHtml = '';
	if ((this._param.optAll) || this._param.noGroup) {
		topHtml += "<div class='group_t_all'>";
		if (this._param.optAll) {
			topHtml += "<span><label><input type='checkbox' id='"+this._getId(GroupSelector.FRAME_IDS.CHECK_ALL)+"'>不限</label></span>";
		}
		if (this._param.noGroup) {
			topHtml += "<span><label><input type='checkbox' id='"+this._getId(GroupSelector.FRAME_IDS.NO_GROUP)+"'>未分组</label></span>";
		}
		topHtml += "</div>";
	}
	var html = "<div class='group_s_c'>" + topHtml
			+ "<div class='group_s_left group_s_h'>"
			+ 	"<div><b>一级组：</b><form action='javascrip:void(0)' onsubmit='return false' style='display: inline-block;' id='"+this._getId(GroupSelector.FRAME_IDS.SEARCH_FORM)+"'>"
			+ 		"<input class='s_input' id='"+this._getId(GroupSelector.FRAME_IDS.SEARCH_VAR)+"' value='"+this._param.search+"'/><input type='submit' value='搜索'/>"
			+       "<span id='"+this._getId(GroupSelector.FRAME_IDS.LOADING_G_1)+"' style='display:none;' class='req_load'><span class='load1'>&nbsp;</span></span>"
			+ 	"</form></div>"
			+ 	"<div class='group_1_con' id='"+this._getId(GroupSelector.FRAME_IDS.FIRST_GROUP_CON)+"'></div>"
			+ 	"<div class='group_page' id='"+this._getId(GroupSelector.FRAME_IDS.GROUP_1_PAGE)+"'></div>"
			+ "</div>"
			+ "<div class='group_s_right group_s_h'>"
			+ 	"<div><b>二级组：</b><span id='"+this._getId(GroupSelector.FRAME_IDS.LOADING_G_2)+"' style='display:none;'class='req_load'><span class='load1'>&nbsp;</span></span></div>"
			+ 	"<div><ul class='group_2_c' id='"+this._getId(GroupSelector.FRAME_IDS.TWO_GROUP_CON)+"'></ul></div>"
			+   "<div class='group_page'>&nbsp;</div>"
			+ "</div>"
			+ "<div class='group_s_checkzone'>"
			+ 	"<div id='"+this._getId(GroupSelector.FRAME_IDS.SELECTED_NUM_CON)+"'><b>已选组：</b>"+maxHtml+"&nbsp;&nbsp;已选：<span class='sel_tip' id='"+this._getId(GroupSelector.FRAME_IDS.SELECTED_NUM)+"'>0</span></div>"
			+	"<div class='group_s_check_l' id='"+this._getId(GroupSelector.FRAME_IDS.SELECTED_CON)+"'></div>"
			+ "</div>"
			+ "<div class='group_s_bottom'><a class='button ok_btn' id='"+this._getId(GroupSelector.FRAME_IDS.OK_BTN)+"'>确定</a>"
			+	"<a class='button cancel_btn' id='"+this._getId(GroupSelector.FRAME_IDS.CANCEL_BTN)+"'>取消</a>"
			+ "</div>"
			+ "</div>";
	if(this._param.invokeFrom == 'callCenter'){
		var wb = new window.top.jsbox({
			 onlyid: this._getId(GroupSelector.FRAME_IDS.ONLY_ID),
			 title: this._param.title,
			 conw:510,
			 content:html,
			 FixedTop:this._param.top,
			 range:true,
			 mack:true
		}).show();
	}
	else{
		var wb = new jsbox({
			 onlyid: this._getId(GroupSelector.FRAME_IDS.ONLY_ID),
			 title: this._param.title,
			 conw:510,
			 content:html,
			 FixedTop:this._param.top,
			 range:true,
			 mack:true
		}).show();
	}
};
//ajax请求获取一级组列表
GroupSelector.prototype._getFirstGroupData = function(page) {
	var group_name = $(this._getId(GroupSelector.FRAME_IDS.SEARCH_VAR, '#')).val();
	var page = page || this._ajaxData.firstGroup.page;
	this._loading(1, true);
	_this = this;
	var param = {group_name:group_name,ajax:1};
	param[this._param.pagedVar] = page;
	$.get(this._getFirstGroupUrl, param,
		function (result) {
			_this._loading(1, false);	
			var result = eval("(" + result + ")");
			if (!result && 0 != result.error) {
				///
			}
			_this._ajaxData.firstGroup.page ++;
			var page = _this._ajaxData.firstGroup.page;
			_this._ajaxData.firstGroup.data[page] = result.data;
			_this._createFirstGroupDom(result.data);
	});
};
//生成一级组dom
GroupSelector.prototype._createFirstGroupDom = function(data) {
	var html = "";
	if (!data || !data.list || data.list.length <= 0) {
		html = "暂无数据";
		$(this._getId(GroupSelector.FRAME_IDS.FIRST_GROUP_CON, '#')).html(html);
		return;
	}
	var groups = data.list;
	for (var i = 0, len = groups.length; i < len; i++) {
		var style = '';
		if (this._firstGroupCurr.id == groups[i]['ug_id']) {
			style = 'background-color:#D3D3D3';
		}
		html += "<div class='one' style='"+style+"'><span name='ug_name' class='first_group' ugid='"+groups[i]['ug_id']+"'>"+groups[i]['ug_name']+"</span>";
		html += "(<span name='count'>"+(groups[i]['count']||0)+"</span>)</div>";
	}
	$(this._getId(GroupSelector.FRAME_IDS.FIRST_GROUP_CON, '#')).html(html);
	$(this._getId(GroupSelector.FRAME_IDS.GROUP_1_PAGE, '#')).html(data.page);
};
//ajax请求获取二级组列表
GroupSelector.prototype._getTwoGroupData = function(firstGroupId) {
	var page = this._ajaxData.twoGroup.page;
	this._loading(2, true);
	var param = {group_id: firstGroupId, ajax:1};
	param[this._param.pagedVar] = page;
	$.get(this._getTwoGroupUrl, param,
		function (result) {
			_this._loading(2, false);
			var result = eval("(" + result + ")");
			if (!result && 0 != result.error) {
				//
			}
			_this._createTwoGroupDom(result.data);
	});
};
//生成二级组dom
GroupSelector.prototype._createTwoGroupDom = function(data) {
	if (!data || data['parent_id'] != this._firstGroupCurr.id) {
		return;
	}
	//1,生成列表
	var html = "";
	if (!data || !data.list || data.list.length <= 0) {
		html = "暂无数据";
		$(this._getId(GroupSelector.FRAME_IDS.TWO_GROUP_CON, '#')).html(html);
		return;
	}
	var groups = data.list;
	for (var i = 0, len = groups.length; i < len; i++) {
		var style = '';
		if (this._isInSelected(groups[i]['ug_id'])) {
			style = 'background-color:#D3D3D3';
		}
		html += "<li><a href='javascript:;' ugid='"+groups[i]['ug_id']+"' class='two_group' style='"+style+"' id='"+this._getId(GroupSelector.FRAME_IDS.GROUP_2_ITEM+groups[i]['ug_id'])+"'>"
			 +  "<span name='ug_name'>"+groups[i]['ug_name']+"</span>(<span name='count'>"+(groups[i]['count']||0)+"</span>)</a></li>";
	}
	$(this._getId(GroupSelector.FRAME_IDS.TWO_GROUP_CON, '#')).html(html);
	//2,生成 分页
};
//添加选择组
GroupSelector.prototype._addChecked = function (gdata, pdata) {
	var isAll = $(this._getId(GroupSelector.FRAME_IDS.CHECK_ALL, '#')).attr('checked');
	if (isAll) {
		return false;
	}
	//1，检测是否在已选择列表中
	if (!gdata || !gdata.id) {alert('group_id null');return false;}
	if (this._isInSelected(gdata.id)) {
		return false;
	}
	//2，验证是否达到允许选择个数最大值
	if (this._param.maxNum != -1) {
		if (this._selectedNum >= this._param.maxNum) {
			alert('选择的个数已达到最大');
			return false;
		}
	}
	//3,添加到已选择列表
	var child = {id:gdata.id, name:gdata.name, count:gdata.count};
	var pdata = pdata || this._firstGroupCurr;
	this._createSelectedGroupDom(pdata, child);
	var added = false;
	for (var id=0,len=this._data.length; id<len;id++) {
		if (this._data[id]['id'] == pdata.id) {
			this._data[id]['child'] = this._data[id]['child'] || [];
			this._data[id]['child'].push(child);
			added = true;
		}
	}
	if (false == added) {
		this._data = this._data || [];
		this._data.push({id:pdata.id, name:pdata.name, child:[child]});
	}
	this._selectedNum ++;
	$(this._getId(GroupSelector.FRAME_IDS.SELECTED_NUM, '#')).html(this._selectedNum);
	return true;
};
//生成默认选择数据dom
GroupSelector.prototype._createSelectedGroupDom = function(parentD, child) {
	var html = '';
	if (!this._isInSelected(parentD.id, true)) {
		html = "<div style='display:block;clear: both;' id='"
			 + this._getId(GroupSelector.FRAME_IDS.SELECTED_PARENT_ITEM+parentD.id)+"'><div class='group_s_d1'>"+(parentD.name?parentD.name+"：":"")+"</div></div>";
		$(this._getId(GroupSelector.FRAME_IDS.SELECTED_CON, '#')).append(html);
	}
	html = "<div class='group_s_d2' id='"+this._getId(GroupSelector.FRAME_IDS.SELECTED_ITEM+child.id)+"'>"
		+ "<a class='a1' href='javascript:;'>"+child.name+(child.count?"("+child.count+")":"")+"</a>"
		+ "<a class='a2 group_del' href='javascript:;' ugid='"+child.id+"' parent_id='"+parentD.id+"'>&nbsp;</a>"
		+ "</div>";
	$(this._getId(GroupSelector.FRAME_IDS.SELECTED_PARENT_ITEM+parentD.id, '#')).append(html);
};
//删除已选择组
GroupSelector.prototype._delChecked = function (group_id, parent_id) {
	if (!this._isInSelected(group_id)) {
		return;
	}
	//1，删除内存中对应的数据
	this._delSelectedData(group_id, parent_id);
	//2，删除dom
	$(this._getId(GroupSelector.FRAME_IDS.SELECTED_ITEM+group_id, '#')).remove();
	$(this._getId(GroupSelector.FRAME_IDS.GROUP_2_ITEM+group_id, '#')).css('background-color', '');
	//3,判断父节点中是否还有字节点，没有则删除父 节点dom
	if (!this._isInSelected(parent_id, true)) {
		$(this._getId(GroupSelector.FRAME_IDS.SELECTED_PARENT_ITEM+parent_id, '#')).remove();
	}
	$(this._getId(GroupSelector.FRAME_IDS.SELECTED_NUM, '#')).html(this._selectedNum);
	if (group_id == -1) {//未分组
		$(this._getId(GroupSelector.FRAME_IDS.NO_GROUP, '#')).attr('checked', false);
	}
};
GroupSelector.prototype._isInSelected = function (group_id, parent) {
	parent = parent || false;
	for (var id=0,len=this._data.length; id<len; id++) {
		if (true == parent && this._data[id]['id'] == group_id) {
			return true;
		}
		var dd = this._data[id]['child'];
		for (var i = 0,len2 = dd.length; i < len2 ; i++) {
			if (group_id == dd[i]['id']) {
				return true;
			}
		}
	}
	return false;
};
GroupSelector.prototype._delSelectedData = function (group_id, parent_id){
	var newArr = [];
	for (var id=0,len=this._data.length; id<len; id++) {
		if (parent_id != this._data[id]['id']) {
			newArr.push(this._data[id]);
			continue;
		}
		var dd = this._data[id]['child'];
		var newChildArr = [];
		for (var i = 0,len2 = dd.length; i < len2 ; i++) {
			if (group_id != dd[i]['id']) {
				newChildArr.push(dd[i]);
			}
		}
		if (newChildArr.length > 0) {
			newArr.push({id:this._data[id]['id'], name:this._data[id]['name'], child:newChildArr});
		}
	}
	this._data = newArr;
	this._selectedNum --;
};
GroupSelector.prototype._loading = function (level, flag) {
	flag = flag || false;
	if (level == 1) {
		var obj = $(this._getId(GroupSelector.FRAME_IDS.LOADING_G_1, '#'));//#f3D3D3
		var c_obj = $(this._getId(GroupSelector.FRAME_IDS.FIRST_GROUP_CON, '#'));
		if (flag == true) {
			obj.show();
			c_obj.css('color', '#F3D3D3');
			this._fetching.one = true;
		} else {
			obj.hide();
			c_obj.css('color', '');
			this._fetching.one = false;
		}
	} else if (level == 2) {
		var obj = $(this._getId(GroupSelector.FRAME_IDS.LOADING_G_2, '#'));
		var c_obj = $(this._getId(GroupSelector.FRAME_IDS.TWO_GROUP_CON, '#'));
		if (flag == true) {
			obj.show();
			c_obj.find('.two_group').css('color', '#F3D3D3');
			this._fetching.two = true;
		} else {
			obj.hide();
			c_obj.find('.two_group').css('color', '');
			this._fetching.two = false;
		}
	}
};

//点击确定
GroupSelector.prototype._ok = function() {
	var isAll = $(this._getId(GroupSelector.FRAME_IDS.CHECK_ALL, '#')).attr('checked');
	var obj = null;
	if (isAll) {
		var str = '<div style="display: block; clear: both;">'
				+ '<div class="group_s_d1"></div>'
				+ '<div class="group_s_d2">'
				+ '<a href="javascript:;" class="a1">不限</a></div></div>';
		obj = $(str);
		this._data = [{id:-2,name:'',child:[{id:-2,name:'不限'}]}];
	} else {
		obj = $(this._getId(GroupSelector.FRAME_IDS.SELECTED_CON, '#')).clone();
		obj.attr('id','').find('div, .group_s_d2').removeAttr('id');
		obj.find('.group_del').die().remove();
	}
	if (this._param.targetDiv) {
		$('#'+this._param.targetDiv).html('').append(obj.html());
	}
	if (this._param.callback) {
		if(this._param.callbackParam == 'callcenter'){
			this._param.callback.call(null, this.getData(),this._param.openId);
		}else{
			this._param.callback.call(null, this.getData());
		}
	}
	this._param.defaultSelected = this.getData();
	
	$(this._getId(GroupSelector.FRAME_IDS.ONLY_ID, '#')+' .jsbox_close').click();
}
GroupSelector.bind = function(fn, selfObj, var_args) {
	  if (!fn) {
	    throw new Error();
	  }

	  if (arguments.length > 2) {
	    var boundArgs = Array.prototype.slice.call(arguments, 2);
	    return function() {
	      var newArgs = Array.prototype.slice.call(arguments);
	      Array.prototype.unshift.apply(newArgs, boundArgs);
	      return fn.apply(selfObj, newArgs);
	    };

	  } else {
	    return function() {
	      return fn.apply(selfObj, arguments);
	    };
	  }
};

GroupSelector.FRAME_IDS = {
	ONLY_ID: 'group',
	SELECTED_CON:'selected_con',
	SELECTED_NUM_CON: 'selected_num_con',
	SELECTED_NUM:'selected_num',
	FIRST_GROUP_CON: 'first_gcon',
	TWO_GROUP_CON:'two_gcon',
	OK_BTN: 'ok_btn',
	CANCEL_BTN: 'cancel_btn',
	SEARCH_FORM: 'search_form',
	SEARCH_VAR: 'search_var',
	SELECTED_ITEM: 's_item',
	SELECTED_PARENT_ITEM: 's_p_item',
	GROUP_2_ITEM: 'g2_item',
	GROUP_1_PAGE: 'g1_page',
	LOADING_G_1: 'l_g_1',
	LOADING_G_2: 'l_g_2',
	CHECK_ALL: 'check_all',
	NO_GROUP: 'no_group'
};
