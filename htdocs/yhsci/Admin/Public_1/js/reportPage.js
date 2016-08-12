/**
 * 网页监测单一网页具体报表js
 **/
$(function() {
	/**初始化数据**/
	
	$('#topData td:odd').css({
		'color' : 'red',
		'font-size' : '13',
		'font-weight' : 'bold'
	});

	//reportSesObj.getDataByCond();
});

//数据排序、查询、分页对象
var reportSesObj = {

	REQUESTURL : '?a=MonitorPageReport&m=getPieAjaxData',
	chartFlushFlag : true,
	init : function() {
	},
	getDataByCond : function() {
		var posId	  =	$('#posId').val();
		var datas = {
			posId : posId
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
				//loadMack('off');
			},
			success : function(msg) {
				//chart图表显示部分
				reportSesObj.createChart(msg); 
				loadMack('off');
			}
		});
	},
	
	//验证传入后台的参数
	checkParam : function(datas) { 
		return true;
	},
	createChart : function(data) { 			
		//chart图表整体变量
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
			renderTo : 'pieContainer2',
			type : 'column',
			margin : [ 50, 50, 100, 80 ]
		},
		title : {
			text : title + ' 访问次数'
		},
		xAxis : {
			categories : datas[0],
			labels : {
				rotation : -45,
				align : 'right',
				style : {
					fontSize : '11px',
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
			renderTo : 'pieContainer',
			plotBackgroundColor : null,
			plotBorderWidth : null,
			plotShadow : false
		},
		title : {
			text : '来源活动占比'
		},
		tooltip : {
			pointFormat : '{series.name}: <b>{point.percentage}%</b>',
			percentageDecimals : 1
		},
		plotOptions : {
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