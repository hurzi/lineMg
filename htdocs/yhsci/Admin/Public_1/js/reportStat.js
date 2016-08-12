
/*
 * 生成html结构函数(自动生成table里面的tr和td结构)
 * clnNames Array 要显示的列名数组(一维) (必填参数)
 * data     Array 内容数据数组(二维) (必填参数)
 * bodyId   String 数据展示id (可选参数,不填写或为空,返回字符串)
 * page     String 分页数据  (可选参数)
 * pageId   String 分页展示id (可选参数)
 */
function createHtml(clnNames, data, bodyId, page, pageId) {
	var str = '';
	if(!(data instanceof Array)){
		data = [];
	}
	var len = data.length;
	if (len > 0) {
		var clnLen = clnNames.length;
		for ( var i = 0; i < len; i++) {
			str += '<tr>';
			str += '<td></td>';
			for(var j=0;j<clnLen;j++){
				str += '<td>' + data[i][clnNames[j]] + '</td>';
			}			
			str += '</tr>';
		}
	} else {
		str += '<tr>';
		str += '<td colspan="10" align="center">无内容</td>';
		str += '</tr>';
	}
	if(bodyId){
		$('#' + bodyId + '').html(str);
		if (page && pageId) {
			$('#' + pageId + '').html(page);
		}
		parentSH(); //设置顶层页面高度
	}else{
		return str;
	}	
}

/**
 * 导出数据
 * @param url String 导出数据时请求的url地址
 * @param exportFormat (FORMAT_EXCEL|FORMAT_CSV)
 * @param params 导出请求时传递给后台的参数，例如:var params = {startTime:startTime,endTime:endTime};
 */
function exportFn(url,exportFormat,params){	
	if(typeof($('#iframe_id').attr('src')) == 'undefined'){
		var iframeHtml = '<iframe id="iframe_id" style="display:none"></iframe>';
		$('body').append(iframeHtml);
	}	
	var exportFormat = exportFormat || 'FORMAT_CSV';
	var params       = params || {};
		exportFormat = exportFormat.toUpperCase();
		params.exportFormat= exportFormat;
	if(!url){
		alert('参数丢失，请传入导出url');return;
	}	
	var paramStr = getEscapeParamStr(params);

	url = url + '&' + paramStr;

	$("#iframe_id").attr('src',url);
}
//编码url
function getEscapeParamStr (jsonData){
	if (!jsonData) return '';
	var qarr = [];
		for(i in jsonData){
	  		qarr.push(i+"="+encodeURIComponent(jsonData[i]));
	}
	return qarr.join('&');
}

/**
 * table排序功能
 * @returns {tableSort}
 * 例如：class="headerOrderBy"
 * var tableSort = new TableSort();
		tableSort.init('testOrderId',['date','use_send','ent_reply'],'use_send');
 */
function TableSort(){	
	//E:\zhangpeng\项目\微博派\政务版\wbp_v2_zw\library\library.html
	//E:\zhangpeng\项目\微博派\政务版\wbp_v2_zw\css\public.css
	this.defaultClass = 'headerOrderBy';
	this.currClass    = "currActive";
	var _this = this;
	/*
	 * id 			String header上的id
	 * columnNames  Array  列名
	 * currColumn   String 当前排序列名
	 * orderBy      String 排序类型 ASC:升序,DESC:倒序
	 */
	this.init = function(id, columnNames, currColumn, orderBy){
		orderBy   = orderBy || 'DESC';
		orderBy   = orderBy.toUpperCase();
		var objTds = $('#'+id+'>td.'+this.defaultClass);
			objTds.attr({title:'点击可排序',style:'cursor:pointer;'});
			$.each(objTds,function(index,dom){				
				$(this).attr('column',columnNames[index]);
				if(currColumn == columnNames[index]){					
					$(this).addClass(_this.currClass).attr('orderBy',orderBy);
				}				
			});
			
			$('#'+id).delegate('.'+_this.defaultClass,'click',function(){
				var thisObj = $(this);
				//1,切换当前选中列
				objTds.removeClass(_this.currClass);
				$(this).addClass(_this.currClass);		
				//2，切换排序值
				 thisColumn		= thisObj.attr('column');
				 thisOrderBy	= thisObj.attr('orderBy');
				if(typeof(thisOrderBy) == 'undefined' || thisOrderBy == 'ASC'){
					futureOrderVal = 'DESC';
				}else{
					futureOrderVal = 'ASC';
				}
				thisObj.attr('orderBy', futureOrderVal);
			});
	};
	
	/**
	 * 获取当前排序的列名的排序值
	 * @returns array('column':'列名','order':'排序值ASC或DESC')
	 */
	this.getOrderProperty = function(id){
		 var thisObj    = $('#'+id+'>.'+this.currClass);
		  thisColumn 	= thisObj.attr('column');
		  futureOrderVal= thisObj.attr('orderBy');
		  if(typeof(futureOrderVal) == 'undefined'){
				futureOrderVal = 'DESC';
		  }
		  var rtnParams = {column:thisColumn,order:futureOrderVal};
		  return rtnParams;
	};
}