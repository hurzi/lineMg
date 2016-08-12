
//之前的函数
//---------------业务函数----------------
function addIpdDetailReturn(new_msg){
	var ip_id = $('#ip_id').val();
	loadMack({off:'on'});
	window.parent.location.href =addIpDetialRtn +"&ip_id="+ip_id;
}

//--------------功能函数------------
//验证字符个数
function checkNum(chars){
	var sum = 0;
	for(var i=0;i<chars.length;i++){
		var c = chars.charCodeAt(i);
		if((c>=0x0001 &&c<=0x007e)||(0xff60<=c && c<=0xff9f)){
			sum++;
		}else{
			sum+=2;
		}
	}
	return sum;
}
//判断input只能输入数字
function IsNum(e) {
    var k = window.event ? e.keyCode : e.which;
    if (((k >= 48) && (k <= 57)) || k == 8 || k == 0) {
    } else {
        if (window.event) {
            window.event.returnValue = false;
        }
        else {
            e.preventDefault(); //for firefox
        }
    }
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
/*
 * 判断对象是否为空
 * return boolean 为空返回true,否则返回false
 * (例如为obj = {} 或 obj = null都是为空的)
 */
function isNullObject(object){
	if(!object)return true;
	for(var i in object){
		return false;
	}
	return true;
}
/**
 * 合并对象，把对象obj2合并到对象obj1上
 * @param obj1
 * @param obj2
 * @returns
 */
function objMerger(obj1, obj2){
    for(var r in obj2){    	
        obj1[r] = obj2[r]; 
    }
    return obj1;
}
/**
 * 输出对象属性个数
 * @param obj
 * @returns {Number}
 */
function countObject(obj){
	var num = 0;
	if(!obj)return num;
	for(var i in obj){
		num ++;
	}
	return num;
}

function resetH() {
	window.parent.resetIframeH($('body').height()+ 30, 'editIpDetail');
}
//新增加的内容函数
$(function(){	
	window.parent.resetIframeH($('body').height()+ 30, 'editIpDetail');	
	
	//显示规则弹出层
	$('#addRuleBtnId').on('click',function(){ //弹出规则层
		if(countObject(cacheRuleDataTmp) >= RULE_MAX_COUNT){
			jsAlert('一个步骤最多只能创建'+RULE_MAX_COUNT+'条规则');return;
		}else{
			showRulePage();
		}		
	});	
	//编辑规则
	$('#ruleListBodyId').on('click','.edit',function(){
		var name	 = $(this).attr('name');
		var params   = {index:name};
		showRulePage(params);
	});
	//删除规则
	$('#ruleListBodyId').on('click','.del',function(){
		var name = $(this).attr('name');
		//if(parseInt(name) <=0){
			jsConfirm(300, '你确定要删除吗？',function(){
				$('#ruleListBodyId>tr[id="'+name+'"]').remove();
				jsAlert('删除成功');
				cacheRuleDataDelete[name] = cacheRuleDataTmp[name];
				delete cacheRuleDataTmp[name];
				delete cacheRuleData[name];
			});
		/*}else{
			jsConfirm(300, '你确定要删除吗？', function (){
				var params = {
					ruleId : name
				}
				ajaxSubmit(URL_DELETE_RULE, 'POST', params, function(){
					delete cacheRuleDataTmp[name];
					delete cacheRuleData[name];					 
					window.location.reload();
				}, '删除成功');
			});
		}*/
	});
	//是否结束流程
	$('input[name="ipd_status"]').click(function(){
		var thisVal = $(this).val();
		if(thisVal == 1){
			$(this).parents('table').nextAll('.procseeTab').hide();
		}else{
			$(this).parents('table').nextAll('.procseeTab').show();
		}		
		window.parent.resetIframeH($('body').height()+ 30, 'editIpDetail');
	});
	$('input[name="ipd_status"]:checked').click();

});


function showRulePage(params){
	var title = '处理规则';
	var wb = new jsbox({
		onlyid:"show_rule_id",
		content:"<div id='show_rule_id_con'><image src='./Public_1/cj/jsbox/images_jsbox/loading.gif' style='position:relative;left:200px;top:170px;'/></div>",
		title:title,
		conw:480,
		conh:420,
		FixedTop:50,
		//iframe:false, //是使用iframe方式弹出层
		//loads: true, //是使用DIV URL方式弹出层
		range:true,	
		mack:true
		}).show();
	var params = params || {};
	
	var ip_id = $('#ip_id').val();
	var url = URL_SHOW_RULE_PAGE+'&ip_id='+ip_id;
	$.get(url, null, function(html) {
		$('#show_rule_id_con').html(html);
		resetH();
		if(!isNullObject(params)){ 
			var index = params.index;;
			initRulelayer(index);
		}	
		
	});	
}
//规则编辑时初始化数据哦
function initRulelayer(index){
	var data = cacheRuleDataTmp[index];
	if(!data){
		console.error('编辑规则时，初始化数据报错，请检查');
	}
	var upDataType 		= data.upDataType; //上行数据类型
	var ipdRuleTypeId   = parseInt(data.ipdRuleTypeId); //规则类型
	$('#textMathcTypeId').hide();
	$('#hiddenOnlyId').val(index);
	$('#ruleSortId').val(data.ruleSortId);	
	
	$('#inputKeywordTr').hide();
	$('#selectRegexTr').hide();
	$('#inputRegexNumTr').hide();
	$('#inputCustomTr').hide();
	if(upDataType == 'text'){	
		$('#textMathcTypeId').show();
		var text_match_type = parseInt(data.text_match_type);
		$('#ipUpTextMatchTypeId').val(text_match_type);
		if(text_match_type == 1 || text_match_type == 2){
			$('#inputKeyOrReg').val(data.text_match_data);
			$('#inputKeywordTr').show();
		}else if(text_match_type == 3){
			$('#ipTextRegexTypeId').val(data.ipTextRegexTypeId);
			$('#selectRegexTr').show();
			var ipTextRegexTypeId = data.ipTextRegexTypeId;
			
			if(ipTextRegexTypeId == 'number'){
				$('#minRegexNum').val(data.minRegexNum);
				$('#maxRegexNum').val(data.maxRegexNum);
				$('#inputRegexNumTr').show();
			}else if(ipTextRegexTypeId == 'custom'){
				$('#inputCustomReg').val(data.inputCustomReg);
				$('#inputCustomTr').show();
			}
		}
	}else{
		$('#ipUpDataTypeId option[value="'+upDataType+'"]').attr('selected','selected');
	}
	
	$('#ipdRuleTypeId').val(ipdRuleTypeId);
	$('#inputExitTipTr').hide();
	$('#processTr').hide();
	if(ipdRuleTypeId == 2){
		$('#rtnTipId').val(data.rtnTipId);
		$('#inputExitTipTr').show();
	}else if(ipdRuleTypeId == 3){
		$('#processId option[value="'+data.processId+'"]').attr('selected','selected');
		$('#processTr').show();
	}
}

var totalKey = -1; //全局key
//添加或编辑时追加数据
function transferRuleData(data){
	getTotalVar();
	if(typeof(data.hiddenOnlyId) == 'undefined' || data.hiddenOnlyId == "" || hiddenOnlyId == null){
		cacheRuleData[totalKey] 	=data; //规则数据数组添加
		cacheRuleDataTmp[totalKey]  =data;
		totalKey -- ;
	}else{
		cacheRuleData[data.hiddenOnlyId] 	 = data; //规则数据数组添加
		cacheRuleDataTmp[data.hiddenOnlyId]  = data;
	}
	
}
/**
 * 初始化全局变量
 */
function getTotalVar(){
	if(typeof(cacheRuleData) == 'undefined'){//用来提交到后台的添加或者更新到数据库的对象
		cacheRuleData = {};
	}
	if(typeof(cacheRuleDataTmp) == 'undefined'){//用来显示编辑内容的对象
		cacheRuleDataTmp = {};
	}
	if(typeof(cacheRuleDataDelete) == 'undefined'){//用来存储删除对象的
		cacheRuleDataDelete = {};
	}
	
}
