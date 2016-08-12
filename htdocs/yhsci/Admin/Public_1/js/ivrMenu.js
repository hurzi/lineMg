//弹出层
function ivrMenuPopCurr(title,url,conw,conh){
	if(!conw){conw = 500};
	if(!conh){conh = 500};
	var wb = new jsbox({
	onlyid:"ivr_mune_change",
	title:title,
	conw:conw,
	conh:conh,
	FixedTop:170,
	url:url,
	iframe:true,
	range:true,	
	mack:true
	}).show();
}
//打开顶层iframe
function ivrMenuPop(title,url,conw,conh){
	if(!conw){conw = 500};
	if(!conh){conh = 500};
	var wb = new window.top.jsbox({
	onlyid:"ivr_mune_change",
	title:title,
	conw:conw,
	conh:conh,
	FixedTop:170,
	url:url,
	iframe:true,
	range:true,	
	mack:true
	}).show();
}


//生成树html对象
var jsTree = {
	 	init : function(treeListJson){
		 	  if(!treeListJson){
				alert('请传入数据');
			  }
			  this.handleHtml(treeListJson);
		 },
		initParam : function(){ //初始化生成树需要的全局参数
			 this.treeStr = '';
			 this.depth   = 0 ; //树的深度
			 this.popTips1 = '<ol class="ivrBtnOl" style="display: none;">'
	                 +'<li class="green newClass">新建</li>'
	                 +'<li class="green editClass">修改</li>'
	                 +'<li class="green deleteClass">删除</li>'
	                 +'<li class="green shiftClass">转移菜单至</li>'
	                 +'</ol>';
		     this.popTipsV2 = '<ol class="ivrBtnOl" style="display: none;">'
			         +'<li class="green newClass">新建</li>'
			         +'<li class="green editClass">修改</li>'
			         +'<li class="green deleteClass">删除</li>'                
			         +'</ol>';
				
			this.popTipsV3 = '<ol class="ivrBtnOl" style="display: none;">'			      
			         +'<li class="green editClass">修改</li>'
			         +'<li class="green deleteClass">删除</li>'     
			         +'<li class="green shiftClass">转移菜单至</li>'
			         +'</ol>';
			this.popTipsV4 = '<ol class="ivrBtnOl" style="display: none;">'			      
		         +'<li class="green editClass">修改</li>'
		         +'<li class="green deleteClass">删除</li>' 		         
		         +'</ol>';
			
			this.legend = '<div class="ivrExplan">'
								+'<span class="blueSpan"></span><label> --<em>代表菜单</em></label>'
								+'<span class="redSpan"></span><label> --<em>代表流程</em></label>'
							+' </div>';
		},
		getTreeHtmlStr : function(treeListJson){
			this.initParam();
			return this.generateMenuTree(treeListJson);
		},
		handleHtml : function(treeListJson){
			 var treeHtml = this.getTreeHtmlStr(treeListJson);
			  $('.con_Ivredit').html(this.legend + treeHtml);
			  $('.con_Ivredit>ul').addClass('ivrTreePic');		
			  this.afterGenenatePage();
		},
		afterGenenatePage : function(){ //在生成页面后，后置处理
			//设置页面高度
			 $('.con_Ivredit').height( $('.con_Ivredit').height()+80);
			 $('body').height($('body').height());
		},
		generateMenuTree : function(treeListJson){ //递归函数，生成树字符串
			 var treeCount = treeListJson.length;
			 this.depth++ ; //alert(this.depth+'<>'+treeCount)
			 this.popTips = this.popTips1;
			 this.elementBeforeLine = '';
			//头元素去掉横线 和 转移菜单按钮
			 if(this.depth > 1){
					this.elementBeforeLine += "<em></em>"; //图标前导线条
			 }else{
					this.popTips = this.popTipsV2;
			 }	
			 this.treeStr += "<ul>";			 
			for(var i=0; i<treeCount; i++){
				//菜单、流程颜色
				var imTypeColor = '';
				 var process = ""; //流程变量
				if(treeListJson[i].im_type == 2){					
					process = "<div><em></em><span style='cursor:pointer;' title='点击查看流程明细' class='processRegion' id='"+treeListJson[i].ip_id+"'>"+treeListJson[i].ip_name+"</span></div>";
					if(this.depth == 1){
						this.popTips = this.popTipsV4;
					}else{
						this.popTips = this.popTipsV3;
					}
					
				}else{
					 if(this.depth != 1){
						 this.popTips = this.popTips1;
					 }
				}				
				var im_key = '<br/>('+treeListJson[i].im_key + ')';
				if(treeListJson[i]._child){//判断是否有子类
					 this.treeStr += "<li>";
					 this.treeStr += this.elementBeforeLine;
					 this.treeStr += "<span "+imTypeColor+" class='menuRegion' id='"+treeListJson[i].im_id+"'>"+treeListJson[i].im_name+im_key;
					 this.treeStr += this.popTips;
					 this.treeStr += "</span>";
						
					 this.treeStr  = this.generateMenuTree(treeListJson[i]._child);
					 this.treeStr += process; //流程
					 this.treeStr += "</li>";
				}else{
					this.treeStr += "<li>";
					this.treeStr += this.elementBeforeLine;		 
					this.treeStr += "<span  "+imTypeColor+" class='menuRegion' id='"+treeListJson[i].im_id+"'>"+treeListJson[i].im_name+im_key;
					this.treeStr += this.popTips;
					this.treeStr += "</span>";				
					this.treeStr += process; //流程
					this.treeStr += "</li>";
				}
			}
			this.treeStr += "</ul>";
			return this.treeStr ;
		}
};

var jsTreeIframe = {
	 	init : function(treeListJson){
		 	  if(!treeListJson){
				alert('请传入数据');
			  }
			  this.handleHtml(treeListJson);
		 },
		initParam : function(){ //初始化生成树需要的全局参数
			 this.treeStr = '';
			 this.depth   = 0 ; //树的深度
		},
		getTreeHtmlStr : function(treeListJson){
			this.initParam();
			return this.generateMenuTree(treeListJson);
		},
		handleHtml : function(treeListJson){
			 var treeHtml = this.getTreeHtmlStr(treeListJson);
			  $('.con_Ivredit').html(treeHtml);
			  $('.con_Ivredit>ul').addClass('ivrTreePic2');
		},
		generateMenuTree : function(treeListJson){ //递归函数，生成树字符串
			 var treeCount = treeListJson.length;
			 this.depth++ ; //alert(this.depth+'<>'+treeCount)
			 
			 this.elementBeforeLine = '';
			//头元素去掉横线 和 转移菜单按钮
			 if(this.depth > 1){
					this.elementBeforeLine += "<em></em>";
			 }
			 this.treeStr += "<ul>";			 
			for(var i=0; i<treeCount; i++){
				//菜单、流程颜色
				var imTypeColor = '';
				 var process = "";
				 var processClass = "";
				if(treeListJson[i].im_type == 2){					
					process = "<div><em></em><span>"+treeListJson[i].ip_name+"</span></div>";
					processClass = "processClass";
				}
				var im_key = '<br/>('+treeListJson[i].im_key + ')';
				if(treeListJson[i]._child){//判断是否有子类
					 this.treeStr += "<li>";
					 this.treeStr += this.elementBeforeLine;
					 this.treeStr += "<span "+imTypeColor+" class='"+processClass+"' id='"+treeListJson[i].im_id+"'>"+treeListJson[i].im_name+im_key;					 
					 this.treeStr += "</span>";
						
					 this.treeStr  = this.generateMenuTree(treeListJson[i]._child);
					 this.treeStr += process; //流程
					 this.treeStr += "</li>";
				}else{
					this.treeStr += "<li>";
					this.treeStr += this.elementBeforeLine;		 
					this.treeStr += "<span  "+imTypeColor+" class='"+processClass+"' id='"+treeListJson[i].im_id+"'>"+treeListJson[i].im_name+im_key;					
					this.treeStr += "</span>";				
					this.treeStr += process; //流程
					this.treeStr += "</li>";
				}
			}
			this.treeStr += "</ul>";
			return this.treeStr ;
		}
};

/**
 * 转移菜单
 * @param ids
 * @returns {Boolean}
 */
function shiftMenuTo(ids){
	if(ids.length == 0){
		loadMack({off:'on',Limg:0,text:'请选择要添加的父菜单',set:2000});
		return false;
	}
	var currentMenuIdHide = $('#currentMenuIdHide').val();
	if(!currentMenuIdHide || isNaN(currentMenuIdHide) ){
		loadMack({off:'on',Limg:0,text:'当前菜单参数丢失',set:2000});
		return false;
	}
	var params = {
			'targetId' : ids[0],
			'sourceId' : currentMenuIdHide
	};
	ajaxSubmit(urlShiftMenu, 'POST', params, function (status, result) {
		if (status == false || result.error == 0)
		{
			window.top.$('#ivr_mune_change .jsbox_close').click();
			window.top.$('#mainFrame')[0].contentWindow.location.reload(true);
		}
	},
	 '转移菜单成功');
}

//js递归生成树形菜单
/**
var treeStr = '';
var depth   = 0 ; //树的深度
function generateMenuTree_not_used(treeListJson){
	 var treeCount = treeListJson.length;
	 depth++ ;
	 treeStr += "<ul>";
	for(var i=0; i<treeCount; i++){
		if(treeListJson[i]._child){//判断是否有子类
			 treeStr += "<li>";
			 if(depth > 1){
				treeStr += "<em></em>";
			 }
			 treeStr += "<span id='"+treeListJson[i].im_id+"'>"+treeListJson[i].im_name+"</span>";
			 treeStr  = generateMenuTree(treeListJson[i]._child);
			 treeStr += "</li>";
		}else{
			treeStr += "<li>";
			 if(depth > 1){
				treeStr += "<em></em>";
			 }
			treeStr += "<span  id='"+treeListJson[i].im_id+"'>"+treeListJson[i].im_name+"</span>";
			treeStr += "</li>";
		}

	}
	treeStr += "</ul>";
	return treeStr ;
}
**/
