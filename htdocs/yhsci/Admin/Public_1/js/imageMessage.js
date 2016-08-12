

//素材确定按钮回调函数
function newsOkButon(materialId, newsHtml) {
	var materialId = materialId || 0;
	var newsHtml = newsHtml || '';
	newsHtml.find('.tw_edit').remove();

	//赋值操作 			
	$('#material_id').val(materialId);
	$('#msg_type').val('news');
	changeShowType(1);

	var con = $('.Ofs_con').html(newsHtml);
	parentSH();
}

function changeShowType(type) {
	var _this = $('.nav2');
	_this.find('.tab li').removeClass('tab_xz').eq(type).addClass('tab_xz');
	var m = type;
	_this.find('.n_content_all').hide();
	_this.find('.n_content_all:eq(' + m + ')').show();
	if (type == 0) {
		$('.Ofs_con').html('');
		$('#msg_type').val('text');
	}
}

//Load 弹出层
function maptss_laod(title, url, conw, conh) {
	var initHtml = initNewsPopLayer();
	if (!conw) {
		conw = 500
	}
	;
	if (!conh) {
		conh = 500
	}
	;
	var wb = new jsbox({
		onlyid : "maptss",
		title : title,
		conw : conw,
		conh : conh,
		FixedTop : 60,
		content : initHtml,
		range : true
	}).show();

	loadNewsData();
}
//ajax加载弹出层数据
function loadNewsData() {
	var page = arguments[0] ? arguments[0] : 1;
	var data = {
		p : page,
		callback : 'loadNewsData'
	};
	$.ajax({
		url : getMaterialNewsUrl,
		type : 'get',
		data : data,
		dataType : 'json',
		beforeSend : function() {
			loadMack({
				off : 'on'
			})
		},
		complete : function() {
			loadMack({
				off : 'off'
			});
		},
		success : function(msg) {
			var data = msg.content;//内容
			var page = msg.page; //分页
			var content = createNewsTpl(data);
			$(".Mat_con").html(content);
			$('#pageId').html(page);
		}
	});
}



//初始化弹出模板
function initNewsPopLayer() {
	var html = '';
	html += ' <div class="Tccm">';
	html += '	<div class="con_c_t">';
	html += ' 		<div class="con_bzk" style=" border:none; background:#fff; margin-bottom:0px;">';
	html += '			 <div style="padding:10px;">';
	html += ' 				<a href="' + newOneNewsUrl
			+ '" target="mainFrame" class="button green medium">新建单图文</a>';
	html += '				<a href="' + newManyNewsUrl
			+ '" target="mainFrame" class="button green medium">新建多图文</a>';
	html += '				<div id="pageId"  style="float:right">';
	html += ' 				</div>';
	html += ' 			</div> ';
	html += ' 		</div> ';
	html += ' 	</div> ';
	html += ' <div class="Mat_con" style=" border:none; margin-top:5px; height:475px; overflow-x:hidden; overflow-y:auto;">';
	html += '</div>';
	html += ' <div class="mat_f">';
	html += ' 		<a href="javascript:;" class="button green medium AddOFs">确定</a>';
	html += '		<a href="javascript:;" class="button green medium ML_close">取消</a>';
	html += '</div>';
	html += ' </div>';
	return html;
}

//根据数据生成图文内容
function createNewsTpl(data) {
	var contentLeft = '<div class="mat_l">';
	var contentRight = '<div class="mat_r">';
	if (!data)
		return;
	$.each(data, function(i, obj) {
		var articles = obj.articles;
		var len = articles.length;
		//alert(len);
		var tmpStr = '';
		var materialId = obj.id;
		var date = obj.create_time;
		var title = articles[0].title;
		var picurl = articles[0].picurl;
		var desc = articles[0].description;
		var url = articles[0].url;
		if (len == 1) { //单条图文					
			tmpStr += ' <div class="TW_box">';
			tmpStr += ' 	<div class="tw_edit">';
			tmpStr += ' 		<a class="optFor" href="javascript:;" name="'
					+ materialId + '"></a>';
			tmpStr += '		</div>';
			tmpStr += ' 	<div class="appTwb1">';
			tmpStr += ' 		<h3 class="twh3"><a href="#">' + title + '</a></h3>';
			tmpStr += ' 		<p class="twp">' + date + '</p>';
			tmpStr += '			<div class="reveal">';
			tmpStr += '				<img src="' + picurl + '"  />';
			tmpStr += '		 	</div>';
			tmpStr += '		</div>';
			tmpStr += '	<div class="appTwb2">';
			tmpStr += ' 	<div class="tw_text">';
			tmpStr += ' 		<p>' + desc + '</p>';
			tmpStr += ' 	</div>';
			tmpStr += '	</div>';
			tmpStr += ' </div>';
		} else {//多条图文
			tmpStr += '<div class="TW_box">';
			tmpStr += ' <div class="tw_edit">';
			tmpStr += ' 		<a class="optFor" href="javascript:;" name="'
					+ materialId + '"></a>';
			tmpStr += '</div>';
			tmpStr += ' <div class="appTwb1">';
			tmpStr += '		<p class="twp">' + date + '</p>';
			tmpStr += '		<div class="reveal">';
			tmpStr += '			<h5 class="tw_z">';
			tmpStr += '  			<a class="z_title" href="#">' + title + '</a>';
			tmpStr += '			 </h5>';
			tmpStr += '			<img src="' + picurl + '"  />';
			tmpStr += '  	</div>';
			tmpStr += ' </div>';
			tmpStr += ' <div class="appTwb2">';
			for ( var j = 1; j < len; j++) {
				var title = articles[j].title;
				var picurl = articles[j].picurl;
				tmpStr += '<div class="tw_li">';
				tmpStr += '		<a class="atext" href="javascript:;">' + title + '</a>';
				tmpStr += ' 	<img width="70" height="70" src="' + picurl
						+ '" />';
				tmpStr += ' </div>';
			}
			tmpStr += ' </div>';
			tmpStr += ' </div>';
		}
		if (i % 2 == 0) {//左边内容					
			contentLeft += tmpStr;
		} else {//右边内容
			contentRight += tmpStr;
		}
	});
	contentLeft += ' </div>';
	contentRight += ' </div>';
	return (contentLeft + contentRight);
}