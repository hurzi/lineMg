/**
 * 会话报表js
 **/
$(function() {
	/**初始化数据**/
	//初始化日期组件
	$('#startTime').Zebra_DatePicker({
		show_week_number : '周',
		direction : false
	});
	$('#endTime').Zebra_DatePicker({
		show_week_number : '周',
		direction : false,
		pair : $('#startTime')
	});
	$('#topData td:odd').css({
		'color' : 'red',
		'font-size' : '13',
		'font-weight' : 'bold'
	});

	$('#listDataTbl thead td').attr('orderValue', 1).css('cursor', 'pointer');

	reportSesObj.searchByDate();

	//搜索按钮
	$('#searchBtnId').live('click', function() {
		var startTime = $.trim($('#startTime').val());
		var endTime = $.trim($('#endTime').val());
		if (!startTime && !endTime) {
			jsAlert('请至少选择一个日期');
			return;
		}
		reportSesObj.searchByDate();
	});
	$('#listDataTbl thead td').live('click', function() {//点击列名触发事件		
		var currObj = $(this);

		currObj.parent().children().attr('classCurr', null);
		currObj.attr('classCurr', 'currColumn');

		var setOrderVal = 0;
		var getOrderVal = currObj.attr('orderValue');
		if (getOrderVal == 1) {
			setOrderVal = 2;
		} else if (getOrderVal == 2) {
			setOrderVal = 1;
		}
		currObj.attr('orderValue', setOrderVal);

		var currColumnName = currObj.attr('column');

		reportSesObj.orderByColumn(currColumnName, setOrderVal);
	});
});

//数据排序、查询、分页对象
var reportSesObj = {

	REQUESTURL : '?a=ReportSession&m=getAjaxData',
	chartFlushFlag : false,
	oldSearchCondition : null,
	init : function() {
	},

	getInitParams : function() {
		var startTime = $.trim($('#startTime').val());
		var endTime = $.trim($('#endTime').val());
		var currThObj;
		$('#listDataTbl thead td').each(function() {
			var tmpAttr = $(this).attr('classCurr');
			if (typeof (tmpAttr) != 'undefined' && tmpAttr == 'currColumn') {
				currThObj = $(this);
				return false;
			}
		});
		var columnName = currThObj.attr('column'); //列的位置
		var columnVal = currThObj.attr('orderValue'); //1:升序,2:降序
		var p = 1; //要跳转到的页码数
		var initParams = {
			startTime : startTime,
			endTime : endTime,
			columnName : columnName,
			columnVal : columnVal,
			p : p
		};
		return initParams;
	},
	searchByDate : function() {//通过日期查询			
		var objParams = {
			type : 'search'
		};
		this.getDataByCond(objParams);
	},
	orderByColumn : function(currColumnName, setOrderVal) {//根据标题排序
		var objParams = {
			type : 'column',
			columnName : currColumnName,
			columnVal : setOrderVal
		};
		this.getDataByCond(objParams);
	},
	paging : function(p) { //根据页码取数据			
		var objParams = {
			type : 'paging',
			p : p
		};
		this.getDataByCond(objParams);
	},
	getDataByCond : function(objParams) {//取后端数据入口
		var initParams = this.getInitParams();//得到初始化参数
		var startTime = initParams.startTime;
		var endTime = initParams.endTime;
		var columnName = initParams.columnName;
		var columnVal = initParams.columnVal;
		var p = initParams.p;

		var tmpSearchCondition = startTime + endTime;
		if (this.oldSearchCondition != tmpSearchCondition) {
			this.oldSearchCondition = tmpSearchCondition;
			this.chartFlushFlag = true;
		}
		switch (objParams.type) {
		case 'column':
			columnName = objParams.columnName;
			columnVal = objParams.columnVal;
			break;
		case 'search':
			break;
		case 'paging':
			p = objParams.p;
			break;
		default:
			break;
		}

		var datas = {
			startTime : startTime,
			endTime : endTime,
			columnName : columnName,
			columnVal : columnVal,
			p : p
		};
		var url = reportSesObj.REQUESTURL;
		jQuery.ajax({
			type : "GET",
			url : url,
			data : datas,
			dataType : 'json',
			beforeSend : function() {
				if (!reportSesObj.checkParam(datas)) {
					return false;
				}
				loadMack();
			},
			complete : function() {
				loadMack('off');
			},
			success : function(msg) {
				reportSesObj.createChart(msg); //chart图表显示部分
				reportSesObj.createTableHtml(msg); //列表数据显示	
			}
		});
	},
	checkParam : function(datas) { //验证传入后台的参数
		return true;
	},
	createChart : function(data) { //chart				
		//chart图表整体变量
		if (this.chartFlushFlag) {
			var dataAllChart = data.dataAllChart;
			var dataPieChart = data.dataPieChart;
			var dataHistogram = data.dataHistogram;
			window.jsonD = dataAllChart;
			var a = dataHistogram.defaultSonData.names;
			var b = dataHistogram.defaultSonData.values;
			var any = [ a, b ];
			var color = dataHistogram.defaultParentColor; //'#E59F3E'
			zsb(any, dataHistogram.defaultParentName, color);

			var pieArr = new Array();
			for ( var i in dataPieChart) {
				pieArr.push(dataPieChart[i]);
			}
			Btb(pieArr);
		}
	},
	createTableHtml : function(data) {//创建html table数据列表部分
		var listSesDay = data.listSesDayData;
		var dataPartContent = '';
		//数据列表部分
		if (listSesDay.length > 0) {
			for ( var i in listSesDay) {
				var tmpObj = listSesDay[i];
				dataPartContent += '<tr>';
				dataPartContent += '<td></td>';
				dataPartContent += '<td>' + tmpObj.gather_date + '</td>';
				dataPartContent += '<td>' + tmpObj.session_count + '</td>';
				dataPartContent += '<td>' + tmpObj.session_count_valid
						+ '</td>';
				dataPartContent += '<td>' + tmpObj.avg_session_time + '</td>';
				dataPartContent += '<td>' + tmpObj.avg_kf_response_time
						+ '</td>';
				dataPartContent += '<td>' + tmpObj.up_message_count_total
						+ '</td>';
				dataPartContent += '<td>' + tmpObj.down_message_count_total
						+ '</td>';
				dataPartContent += '<td>' + tmpObj.up_message_count + '</td>';
				dataPartContent += '<td>' + tmpObj.down_message_count + '</td>';
				dataPartContent += '</tr>';
			}
		} else {
			dataPartContent += ' <tr><td colspan="10" align="center">无内容</td></tr>';
		}
		$("#listDataTbl tbody").html(dataPartContent);
		$('.tab_foot').html(data.page);
		parentSH();
	}
};

function ejzt(name, color) {
	var jDl = jsonD.length;
	for ( var i = 0; i < jDl; i++) {
		if (jsonD[i].name[0] == name) {
			zsb(jsonD[i].fl, name, color);
			return;
		}
	};
}

function zsb(datas, title, color) {
	var chart2;
	chart2 = new Highcharts.Chart({
		chart : {
			renderTo : 'container2',
			type : 'column',
			margin : [ 50, 50, 100, 80 ]
		},
		title : {
			text : title + ' 会话数量'
		},
		xAxis : {
			categories : datas[0],
			labels : {
				rotation : -45,
				align : 'right',
				style : {
					fontSize : '13px',
					fontFamily : 'Verdana, sans-serif'
				}
			}
		},
		yAxis : {
			min : 0,
			title : {
				text : ' '
			}
		},
		legend : {
			enabled : false
		},
		tooltip : {
			formatter : function() {
				return '<b>' + this.x + '</b><br/>' + Highcharts.numberFormat(this.y, 1);
			}
		},
		series : [{
			name : 'Population',
			data : datas[1],
			color : color,
			dataLabels : {
				enabled : true,
				rotation : -90,
				color : '#FFFFFF',
				align : 'right',
				x : 4,
				y : 10,
				style : {
					fontSize : '13px',
					fontFamily : 'Verdana, sans-serif'
				}
			}
		}]
	});
}

function Btb(datas) {
	var chart1;
	chart1 = new Highcharts.Chart({
		chart : {
			renderTo : 'container',
			plotBackgroundColor : null,
			plotBorderWidth : null,
			plotShadow : false
		},
		title : {
			text : '会话类型占比'
		},
		tooltip : {
			pointFormat : '{series.name}: <b>{point.percentage}%</b>',
			percentageDecimals : 1
		},
		plotOptions : {
			/*pie : {
				allowPointSelect : true,
				cursor : 'pointer',
				dataLabels : {
					enabled : true,
					color : '#000000',
					connectorColor : '#000000',
					formatter : function() {
						return '<b>' + this.point.name + '</b>: ' + Highcharts.numberFormat(this.percentage, 1) + ' %';
					}
				}
			},*/
			 pie: {
                 allowPointSelect: true,
                 cursor: 'pointer',
                 dataLabels: {
                     enabled: false
                 },
                 showInLegend: true
             },
			series : {
				cursor : 'pointer',
				point : {
					events : {
						click : function() {
							ejzt(this.name, this.color);
						},
						selection : function() {
							//jsAlert(1);
						},
						load : function() {
							//jsAlert(2);
						}
					}
				}
			}
		},
		Loading : {

		},
		series : [ {
			type : 'pie',
			name : '占总数的',
			data : datas
		} ]
	});
}