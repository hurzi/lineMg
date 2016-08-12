var QrcMassageManager = {
	msgSelectors : {},
	param : {
		msg_types : [ 'text', 'news', 'music'/*,'image','voice','video'*/],
		mode : 'pull_down'
	},
	showMessageSelect : function(qrMsgDefData, indexs,oauth_set) {
		var qrMsgData = [ {
			'qrc_msg_key' : '1',
			'msg_type' : 'text'
		}, {
			'qrc_msg_key' : '2',
			'msg_type' : 'text'
		}, {
			'qrc_msg_key' : '3',
			'msg_type' : 'text'
		}, {
			'qrc_msg_key' : '4',
			'msg_type' : 'text'
		} ];
		if (indexs) {
			qrMsgData = [];
			for ( var i = 0, len = indexs.length; i < len; i++) {
				qrMsgData.push({
					'qrc_msg_key' : indexs[i],
					'msg_type' : 'text'
				});
			}
		}
		var key, qrc_msg_key, msg_type, obj;
		for ( var i = 0, len = qrMsgData.length; i < len; i++) {
			var qrcMK = qrMsgData[i]['qrc_msg_key'];
			if (qrMsgDefData && qrMsgDefData[qrcMK]) {
				key = qrMsgDefData[qrcMK].qrc_msg_key;
				qrc_msg_key = key;
				msg_type = qrMsgDefData[key].msg_type || qrMsgData[i].msg_type;
				obj = qrMsgDefData[qrcMK];
				//修改是否OAUTH授权
				if(oauth_set){
					this.param['oauth_set'] = oauth_set;
					var is_oauth = qrMsgDefData[qrcMK]['is_oauth'];
					if(is_oauth){
						this.param['oauth_checked'] = parseInt(is_oauth);
					}
				}
			} else {
				key = qrMsgData[i].qrc_msg_key;
				qrc_msg_key = key;
				msg_type = qrMsgData[i].msg_type;
				obj = null;
				//添加是否OAUTH授权
				if(oauth_set){
					this.param['oauth_set'] = oauth_set;
				}	
			} 
			this.show(qrc_msg_key, msg_type, obj);
		}
		parentSH();
	},
	show : function(qrc_msg_key, msg_type, obj) {
		var msgSelector = null;
		var check_value = 0;
		switch (msg_type) {
		case 'third': //动态获取
			$('#third_path_' + qrc_msg_key).val((obj || {}).msg_data || '');
			$("#reply_type_third_" + qrc_msg_key).show();
			check_value = 2;
			msgSelector = new MessageSelector(this.param);
			break;
		default:
			check_value = 1;
			$("#reply_type_sys_" + qrc_msg_key).show();
			msgSelector = new MessageSelector(this.param, obj);
			break;
		}
		obj = obj || {};
		$('input[name="reply_info_type_' + qrc_msg_key + '"][value="'
				+ check_value + '"]').attr('checked', true);
		var qrMsgIdHide = '<input type="hidden" id="qrc_msg_id_' + qrc_msg_key
				+ '" value="' + (obj['id'] || 0) + '"/>';
		$('body').append(qrMsgIdHide);
		msgSelector.render("qrc_msg_key_" + qrc_msg_key);
		this.msgSelectors[qrc_msg_key] = msgSelector;
	},
	getData : function() {
		var objs = this.msgSelectors;
		var data = {};
		for ( var i in objs) {
			if (objs[i]) {
				data[i] = objs[i].getData();
			}
		}
		return data;
	},
	submitData : function(url, href, params, data) {
		params = params || {};
		
		data = data || this.getData();
		if (!url || !href || !data) {
			var prompt = ("操作有误，请重试！");
			window.top.loadMack({
				off : 'on',
				Limg : 0,
				text : prompt,
				set : 1000
			});
			return false;
		}
		var datas = this.checkData(data);
		if (datas === false) {
			return false;
		}
		params.replyData = datas;
		//return false;
		jsConfirm(300, '确认操作吗？', function() {
			ajaxSubmit(url, 'POST', params, function(status, result) {
				if (status == true || result.error == 0)
					window.location.href = href;
			}, '操作成功');
		});
	},
	checkData : function(data) {
		var params = [];
		for ( var i in data) {
			var welType = $("input[name='reply_info_type_" + i + "']:checked")
					.val();
			var welPath = $('#third_path_' + i).val();
			var msg_type = data[i].msg_type;
			var qrc_msg_id = $('#qrc_msg_id_' + i).val();
			var content = '';
			var material_id = 0;
			var is_oauth = 0;
			if (data[i].msg_type == "text") {
				content = data[i].content;
			} else {
				material_id = data[i].material_id;
				is_oauth = data[i].is_oauth;
			}
			if (parseInt(welType) === 2) {
				if (!isUrl(welPath)) {
					window.top.loadMack({
						off : 'on',
						Limg : 0,
						text : '请填写正确的URL',
						set : 2000
					});
					return false;
				}
				msg_type = 'third';
			} else if (parseInt(welType) === 1) {
				if (! content && 0 == material_id) {
					window.top.loadMack({
						off : 'on',
						Limg : 0,
						text : '请设置推送信息',
						set : 1000
					});
					return false;
				}
			}
			params.push({
				qrc_msg_key : i,
				reply_info_type : welType,
				third_path : welPath,
				msg_type : msg_type,
				content : content,
				material_id : material_id,
				qrc_msg_id : qrc_msg_id,
				is_oauth : is_oauth
			});
		}
		return params;
	},
	initTypeCascade : function(indexs) {
		if (!indexs || indexs.length <= 0) {
			return;
		}
		for ( var i = 0, len = indexs.length; i < len; i++) {
			$('input[name="reply_info_type_' + indexs[i] + '"]').click(
					Abcbind(this.switchTypeCascade, this, indexs[i]));
			/* $('input[name="reply_info_type_'+indexs[i]+'"]').click(function() {
			   var index = $(this).attr('name').replace('reply_info_type_', '');
			   QrcMassageManager.switchTypeCascade(index);//系统动态切换
			});*/
		}
	},
	switchTypeCascade : function(index) {
		var objVal = $('input[name="reply_info_type_' + index + '"]:checked')
				.val();
		if (1 == objVal) {
			$("#reply_type_sys_" + index).show();
			$("#reply_type_third_" + index).hide();
		} else {
			$("#reply_type_sys_" + index).hide();
			$("#reply_type_third_" + index).show();
		}
		parentSH();
	},
	clickRedirect : function(param) {
		if (!param)
			return;
		for ( var id in param) {
			$('#' + id).click(Abcbind(function(href) {
				window.location.href = href;
				return;
			}, null, param[id]));
		}
	}
};