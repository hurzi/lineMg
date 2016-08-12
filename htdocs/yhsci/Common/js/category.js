
SSH_PrimaryCategory = function (param, initData) {
	this._id = ++SSH_PrimaryCategory.ID;
	this._divId = '';
	this._initData = initData || {first:{},second: {}};
	param = param || {};
	this._param = {
		first_id: param.first_id || this.getId('category_first_id'),
		first_name: param.first_name || this.getId('category_first_name'),
		first_css: param.first_css || 'default_select',
		first_style: param.first_style || '',
		second_id: param.second_id || this.getId('category_second_id'),
		second_name: param.second_name || this.getId('category_second_name'),
		second_css: param.second_css || 'default_select',
		second_style: param.second_style || '',
		create_select: param.create_select || true,
	};
	this._render = false;
	if (this._initData.first.name && !this._initData.first.id) {
		this._initData.first.id = this._getIdByName(SSH_PrimaryCategory.data.first, this._initData.first.name);
	}
	var secondSel = SSH_PrimaryCategory.data.second[this._initData.first.id];
	if (this._initData.first.id && this._initData.second.name && secondSel && !this._initData.second.id) {
		this._initData.second.id = this._getIdByName(secondSel, this._initData.second.name);
	}
}
SSH_PrimaryCategory.ID = 1;
SSH_PrimaryCategory.prototype.render = function (divId) {
	if (this._render == true) {
		return;
	}
	this._divId = divId;
	if (this._param.create_select) {
		$('#'+divId).html(this.createDom());
	}
	this.initData();
	if (!$('#'+this._param.first_id).val()) {
		$('#'+this._param.second_id).hide();
	} else {
		$('#'+this._param.second_id).show();
	}
	this._listen();
}
SSH_PrimaryCategory.prototype._listen = function () {
	$('#'+this._param.first_id).on('change', SuiShibind(this._firstChange, this));
};
SSH_PrimaryCategory.prototype._getIdByName = function (data, name) {
	data = data || [];
	for (var i = 0, len=data.length; i<len; i++) {
		if ($.trim(name) == data[i]['name']) {
			return data[i]['id'];
		}
	}
	return false;
};
SSH_PrimaryCategory.prototype._firstChange = function () {
	var firstVal = $('#'+this._param.first_id).val();
	var firstId = this._getIdByName(SSH_PrimaryCategory.data.first, firstVal);
	if (!firstId) {
		$('#'+this._param.second_id).html(this.createOption([],{},'请选择子行业')).hide();
		return;
	}
	var secondSel = SSH_PrimaryCategory.data.second[firstId];
	var tt = this.createOption(secondSel || [], {}, '请选择子行业');
	$('#'+this._param.second_id).html(tt).show();
};

SSH_PrimaryCategory.prototype.get = function () {
	var firstVal = $('#'+this._param.first_id).val();
	var secondVal = $('#'+this._param.second_id).val();
	var firstId = this._getIdByName(SSH_PrimaryCategory.data.first, firstVal);
	var secondSel = SSH_PrimaryCategory.data.second[firstId];
	return {first:{id:firstId,name:firstVal},
			second:{id:this._getIdByName(secondSel, secondVal),name:secondVal}};
}
SSH_PrimaryCategory.prototype.createDom = function () {
	return '<select id="'+this._param.first_id+'" name="'+this._param.first_name+'" class="'+this._param.first_css+'" style="'+this._param.first_style+'"></select>'
	      +'<select id="'+this._param.second_id+'" name="'+this._param.second_name+'" class="'+this._param.second_css+'" style="'+this._param.second_style+'"></select>';
}
SSH_PrimaryCategory.prototype.createOption = function (data, initD, dname) {
	var t = '<option value="">'+dname+'</option>';
	for (var i = 0, len=data.length; i<len; i++) {
		if (initD && initD.id == data[i]['id']) {
			t += '<option value="'+data[i]['name']+'" selected="selected">'+data[i]['name']+'</option>';
		} else {
			t += '<option value="'+data[i]['name']+'">'+data[i]['name']+'</option>';
		}
	}
	return t;
}
SSH_PrimaryCategory.prototype.initData = function () {
	var t = this.createOption(SSH_PrimaryCategory.data.first, this._initData.first, '请选择行业');
	var tt = this.createOption([], {}, '请选择子行业');
	if (this._initData.second && this._initData.second.id) {
		var secondSel = SSH_PrimaryCategory.data.second[this._initData.first.id];
		if (secondSel) {
			tt = this.createOption(secondSel, this._initData.second, '请选择子行业');
		}
	}
	$('#'+this._param.first_id).html(t);
	$('#'+this._param.second_id).html(tt);
}
SSH_PrimaryCategory.prototype.getId = function (id) {
	return this._id+'_'+id;
}

SSH_PrimaryCategory.data = {"first":[{"id":1,"name":"美食"},{"id":2,"name":"休闲娱乐"},{"id":3,"name":"生活服务"},{"id":4,"name":"旅游"},{"id":5,"name":"酒店"},{"id":6,"name":"运输票务"},{"id":7,"name":"电影票"},{"id":8,"name":"购物"},{"id":9,"name":"虚拟"},{"id":11,"name":"网络传媒"}],"second":{"1":[{"id":101,"name":"粤菜"},{"id":102,"name":"茶餐厅"},{"id":103,"name":"川菜"},{"id":104,"name":"湘菜"},{"id":105,"name":"东北菜"},{"id":106,"name":"西北菜"},{"id":107,"name":"火锅"},{"id":108,"name":"自助餐"},{"id":109,"name":"小吃"},{"id":110,"name":"快餐"},{"id":111,"name":"日本料理"},{"id":112,"name":"韩国料理"},{"id":113,"name":"东南亚菜"},{"id":114,"name":"西餐"},{"id":115,"name":"面包甜点"},{"id":116,"name":"咖啡厅"},{"id":117,"name":"江浙菜"},{"id":119,"name":"外卖"},{"id":118,"name":"其它美食"}],"2":[{"id":201,"name":"美容美发"},{"id":202,"name":"美甲"},{"id":203,"name":"艺术写真"},{"id":204,"name":"酒吧/俱乐部"},{"id":205,"name":"文化文艺"},{"id":206,"name":"KTV"},{"id":207,"name":"棋牌室"},{"id":208,"name":"运动健身"},{"id":209,"name":"演出门票"},{"id":210,"name":"足疗按摩"},{"id":211,"name":"宠物美容"},{"id":212,"name":"展览展出"},{"id":213,"name":"会议活动"},{"id":214,"name":"培训拓展"},{"id":215,"name":"温泉洗浴"}],"3":[{"id":303,"name":"婚庆服务"},{"id":304,"name":"汽车服务"},{"id":305,"name":"家政服务"},{"id":306,"name":"物业管理"},{"id":307,"name":"医疗保健"},{"id":308,"name":"快递"},{"id":309,"name":"宠物医疗"},{"id":310,"name":"教育学校"},{"id":311,"name":"留学中介"},{"id":312,"name":"货运"},{"id":313,"name":"客服/售后"},{"id":314,"name":"养生养护"},{"id":315,"name":"政务民生"}],"4":[{"id":401,"name":"旅游套餐"},{"id":402,"name":"景点门票"}],"5":[{"id":501,"name":"星级酒店"},{"id":502,"name":"度假村"},{"id":503,"name":"快捷酒店"}],"6":[{"id":601,"name":"机票"},{"id":602,"name":"船票"},{"id":603,"name":"车票"}],"7":[{"id":701,"name":"电影票"}],"8":[{"id":801,"name":"服饰"},{"id":802,"name":"鞋类箱包"},{"id":803,"name":"运动户外"},{"id":804,"name":"化妆品"},{"id":817,"name":"家纺家装"},{"id":807,"name":"乐器"},{"id":808,"name":"鲜花礼品"},{"id":809,"name":"数码家电"},{"id":812,"name":"母婴用品"},{"id":813,"name":"图书报刊杂志"},{"id":814,"name":"珠宝配饰"},{"id":811,"name":"普通食品"},{"id":815,"name":"保健食品"},{"id":816,"name":"酒类"},{"id":818,"name":"钟表眼镜"},{"id":819,"name":"药房/药店"},{"id":820,"name":"综合电商"},{"id":821,"name":"百货商场"},{"id":822,"name":"超市/便利店"},{"id":823,"name":"购物中心/购物街"},{"id":824,"name":"副食品门市"},{"id":825,"name":"家居"},{"id":826,"name":"建材五金/机械仪表"},{"id":827,"name":"日户用品"}],"9":[{"id":901,"name":"话费/流量/宽带"},{"id":902,"name":"水电煤缴费"},{"id":903,"name":"有线电视缴费"}],"11":[{"id":1101,"name":"广告/传媒/营销"},{"id":1102,"name":"网络技术"}]}};
