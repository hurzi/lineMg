//add by zhangpeng
$(function(){
	$('#ipdRuleTypeId').on('change',function(){
		var ruleTypeObj = $(this);
		handleRuleType(ruleTypeObj);
	});	
	//添加或修改规则的确定按钮
	$('#ipdRuleSubmit').on('click',function(){
		addRule();
	});
	$('#ipdRuleClear').on('click',function(){
		$('#show_rule_id .jsbox_close').click();
	});
	//文本匹配类型事件
	$("#ipUpTextMatchTypeId").change(function(){
	    textMatchType = $(this).find("option:selected").val();	    
	    textMatchEvent(textMatchType);
	});
	//触发上行数据类型事件
	$(".ipUpDataType").change(function(){
		selectPostType = $(this).find("option:selected").val();
	    if(selectPostType=="text"){	    	
	    	$("#textMathcTypeId").css("display","block");	    	
	    }else{	    	
	    	$("#textMathcTypeId").css("display","none");	    
	    }
	});
	//触发正则匹配类型
	$(".ipTextRegexType").change(function(){
	    textRegexType = $(this).find("option:selected").val();
	    if(textRegexType=="number"){	    	
	    	$("#inputRegexNumTr").show();
	    	$('#inputCustomTr').hide();
	    } else if(textRegexType == 'custom')   {	    	
	    	$("#inputRegexNumTr").hide();
	    	$('#inputCustomTr').show();
	    } else {
	    	$("#inputRegexNumTr").hide();
	    	$('#inputCustomTr').hide();
	    }
	});
	
		
	//初始化函数
	handleRuleType();
	textMatchEvent();
});

//文本匹配类型事件函数
function textMatchEvent(textMatchType){
	if(typeof(textMatchType) == 'undefined')textMatchType = 4;
	if(textMatchType=="3") {
    	$("#inputKeywordTr").hide();
    	$("#selectRegexTr").show();
    	if($(".ipTextRegexType").find("option:selected").val()=="number"){
    		$("#inputRegexNumTr").show();
    		$("#inputCustomTr").hide();
        }else if($(".ipTextRegexType").find("option:selected").val()=="custom"){
        	$("#inputRegexNumTr").hide();
        	$("#inputCustomTr").show();
        }
    }else{
    	$("#selectRegexTr").hide();
		$("#inputRegexNumTr").hide();
		$("#inputCustomTr").hide();
    	if(textMatchType=="4"){
    		$("#inputKeywordTr").hide();
        }
        else{
	    	$("#inputKeywordTr").show();
        }
    }
}
//触发规则类型事件
function handleRuleType(ruleTypeObj){
	var ruleTypeVal;
	if(typeof(ruleTypeObj) == 'undefined'){
		ruleTypeVal = -1;
	}else{
		ruleTypeVal = parseInt(ruleTypeObj.val());
	}	
	switch(ruleTypeVal){
		case 1:  //下一步			
			$('#inputExitTipTr').hide();
			$('#processTr').hide();
			break;
		case 2: //退出			
			$('#inputExitTipTr').show();
			$('#processTr').hide();
			break;
		case 3: //转入流程			 
			$('#inputExitTipTr').hide();
			$('#processTr').show();			
			break;
		default:			
			$('#inputExitTipTr').hide();
			$('#processTr').hide();
	}
}
function _validNum(minRegexNum, maxRegexNum){
	if(minRegexNum==null || minRegexNum==""){
		jsAlert('请输入最小位数');
		return false;
	}
	if(maxRegexNum==null || maxRegexNum==""){
		jsAlert('请输入最大位数');
		return false;
	}
	if(parseInt(minRegexNum)<1 ){
		jsAlert('最小位数不能小于1');
		return false;
	}
	if(parseInt(minRegexNum)>99 ){
		jsAlert('最小位数不能大于99');
		return false;
	}
	if(parseInt(maxRegexNum)>100){
		jsAlert('最大位数不能大于100');
		return false;
	}
	if(parseInt(maxRegexNum)<parseInt(minRegexNum)){
		jsAlert('最大位数不能小于最小位数');
		return false;
	}
	return true;
}
var ruleObj = new IpDetailRule();
function addRule(){
	if(ruleObj == null || typeof(ruleObj) == 'undefined'){
		 window.ruleObj = new IpDetailRule();
	}
	var params = window.ruleObj.getParam();
	if(!params)return;
	window.ruleObj.createHtml(params, 'ruleListBodyId');
	transferRuleData(params.params);	
	$('#show_rule_id .jsbox_close').click();
}

//管理规则
function IpDetailRule() {
	
}
IpDetailRule.prototype.init = function(dataList, $_UP_DATA_TYPE, $_TEXT_MATCH_TYPE,$_RULE_TYPE,$_REG_MATCH_TYPE){
	if(dataList.length>0){
		var strHtml = '';	
		for(var i in dataList){
			var data = dataList[i];
			var text_match_type = text_match_type_value = rule_match_value = '---';
			if(data.msg_type == 'text'){
				text_match_type = $_TEXT_MATCH_TYPE[data.match_type];			
				if(data.match_type == 3){
					var tmpValue = '';
					if(data.match_data.type == 'number'){
						tmpValue = '['+data.match_data.minNum+'-'+data.match_data.maxNum+']位';
					}else if(data.match_data.type == 'custom'){
						tmpValue = '['+data.match_data.preg+']';
					}
					text_match_type_value = $_REG_MATCH_TYPE[data.match_data.type]+tmpValue;
				}else if(data.match_type == 1 || data.match_type == 2){
					text_match_type_value = data.match_data;
				}
			}
			if(data.type == 2){
				rule_match_value = data.exit_message.data;
			}else if(data.type == 3){
				rule_match_value = data.ip_name;
			}
			strHtml += '<tr id="'+data.ipdr_id+'">';
				strHtml += '<td width="20"></td>';
				strHtml += '<td align="center">'+$_UP_DATA_TYPE[data.msg_type]+'</td>';
				strHtml += '<td align="center">'+text_match_type+'</td>';
				strHtml += '<td align="center">'+text_match_type_value+'</td>';
				strHtml += '<td align="center">'+$_RULE_TYPE[data.type]+'</td>';
				strHtml += '<td align="center">'+rule_match_value+'</td>';
				strHtml += '<td align="center">'+data.sort+'</td>';
				strHtml += '<td>';
					strHtml += '<div class="czx">';
					strHtml += '<a name="'+data.ipdr_id+'" href="javascript:void(0);" class="edit"></a>';				
					strHtml += '<a name="'+data.ipdr_id+'" href="javascript:void(0);" class="del"></a>';
					strHtml += '</div>';
				strHtml += '</td>';
			strHtml += '</tr>';  
		}
	}	
	$('#ruleListBodyId').append(strHtml);
	resetH();
};
IpDetailRule.prototype.getParam = function(){
	var hiddenOnlyId        = $.trim($('#hiddenOnlyId').val());
	var upDataType 	    	= $('#ipUpDataTypeId').val(); //上行数据类型
	var text_match_type 	= $('#ipUpTextMatchTypeId').val(); //正则匹配类型
	var text_match_data 	= $.trim($('#inputKeyOrReg').val()); //匹配关键字 
	var ipTextRegexTypeId 	= $('#ipTextRegexTypeId').val(); //匹配数字
	var minRegexNum 		= $('#minRegexNum').val(); //正则  匹配数字
	var maxRegexNum 		= $('#maxRegexNum').val(); //正则  匹配数字
	var ruleSortId			= $('#ruleSortId').val();
	var inputCustomReg      = $.trim($('#inputCustomReg').val()); //自定义正则
	
	var page_upDataType 		= $('#ipUpDataTypeId>option:selected').html();
	var page_text_match_type    = $('#ipUpTextMatchTypeId>option:selected').html();	
	var page_ipTextRegexTypeId	= $('#ipTextRegexTypeId>option:selected').html();
	var page_minRegexNum		= minRegexNum;
	var page_maxRegexNum		= maxRegexNum;	
	switch(upDataType){
		case 'text':			
			if(text_match_type == 1 || text_match_type == 2){				
				if(text_match_data==''||text_match_data==null){
					jsAlert('请输入关键词');
					return false;
				}
			}else if( text_match_type == 3){
				text_match_data = ipTextRegexTypeId;
				if(ipTextRegexTypeId == 'number'){	
					var flag =  _validNum(minRegexNum, maxRegexNum);
					if(!flag)return false;
				}else if(ipTextRegexTypeId == 'custom'){
					var reg = new RegExp(".*{.*\\\$.*}.*", "gi");
					if(isEmpty(inputCustomReg)){
						jsAlert('请输入自定义正则');
						return false;
					}else if( inputCustomReg.match(reg) != null){
						jsAlert('自定义正则格式错误，$不能出现在{}中');
						return false;
					}					
				}
			}
			break;
		case 'image':			
		case 'voice':
		case 'video':
		case 'location':			
		default:
			text_match_type 	 = '---';
			page_text_match_type = '---';
	}
	
	var page_text_match_data	= text_match_data;
	
	var ipdRuleTypeId = $('#ipdRuleTypeId').val();
	var rtnTipId 	  = $.trim($('#rtnTipId').val()); //返回提示信息
	var processId 	  = $('#processId').val();
	
	var page_ipdRuleTypeId 	= $('#ipdRuleTypeId>option:selected').html();
	var page_rtnTipId		= rtnTipId;
	var page_processId		= $('#processId>option:selected').html();
	
		switch(ipdRuleTypeId){
			case '2':
				
				if(rtnTipId==''||rtnTipId==null){
					jsAlert('请输入退出提示信息');
					return false;
				}
				break;
			case '3':				
				if(processId == null){
					jsAlert('当前没有其他的流程，不能转让流程');
					return false;
				}else if($('#processId>option').length == 0){
					jsAlert('当前没有其他的流程，不能转让流程');
					return false;
				}
				break;
			default:
		}
		if(ruleSortId<1 || ruleSortId>100){
			jsAlert('规则排序值超出范围，请重新输入');
			return false;
		}
	var params = {			
			upDataType 		: upDataType,
			text_match_type : text_match_type,
			text_match_data : text_match_data,
			ipTextRegexTypeId:ipTextRegexTypeId,
			minRegexNum     : minRegexNum,
			maxRegexNum     : maxRegexNum,
			ipdRuleTypeId   : ipdRuleTypeId,
			rtnTipId		: rtnTipId,
			processId		: processId,
			ruleSortId	    : ruleSortId,
			inputCustomReg  : inputCustomReg
	};
	if(typeof(hiddenOnlyId) != 'undefined' && hiddenOnlyId != ''){
		params.hiddenOnlyId = hiddenOnlyId;
	}
	var pageParams = {
			page_upDataType		: page_upDataType,
			page_text_match_type: page_text_match_type,
			page_text_match_data	: page_text_match_data,
			page_ipTextRegexTypeId : page_ipTextRegexTypeId,
			page_minRegexNum	: page_minRegexNum,
			page_maxRegexNum	: page_maxRegexNum,
			page_ipdRuleTypeId	: page_ipdRuleTypeId,
			page_rtnTipId		: page_rtnTipId,
			page_processId		: page_processId,
			page_ruleSortId		: ruleSortId,
			page_inputCustomReg		: inputCustomReg
	};
	var returnArr = {params:params,showText:pageParams};
	return returnArr;
};
IpDetailRule.prototype.deleteRule = function(id){
	$('#'+id).remove();
	resetH();
};
//生成单个tbody>tr
IpDetailRule.prototype.createHtml = function(result,htmlId){
	var data 	= result.showText;
	var params  = result.params;
	var textMatchTypeVal = '---';
	var text_match_type = params.text_match_type;
	if(text_match_type == 1 || text_match_type== 2){ //匹配关键字
		textMatchTypeVal = data.page_text_match_data;//data.page_text_match_type +'['+data.page_text_match_data+']';
	}else if(text_match_type == 3){ //正则匹配
		if(params.ipTextRegexTypeId == 'number'){
			textMatchTypeVal = data.page_ipTextRegexTypeId+'['+data.page_minRegexNum+'-'+data.page_maxRegexNum+']位';
		}else if(params.ipTextRegexTypeId == 'custom'){
			textMatchTypeVal = data.page_ipTextRegexTypeId+'['+data.page_inputCustomReg+']';
		}else{
			textMatchTypeVal = data.page_ipTextRegexTypeId;
		}		
	}
	var ruleTypeValue = '---';
	var ipdRuleTypeId = params.ipdRuleTypeId;
	if(ipdRuleTypeId == 2){
		ruleTypeValue = data.page_rtnTipId;
	}else if(ipdRuleTypeId == 3){
		ruleTypeValue = data.page_processId;
	}
	var idTr = 0;
	if(typeof(params.hiddenOnlyId) != 'undefined'){
		idTr = params.hiddenOnlyId;
	}else{
		idTr = totalKey;
	}
	var strHtml = '';			
			strHtml += '<td width="20"></td>';
			strHtml += '<td align="center">'+data.page_upDataType+'</td>';
			strHtml += '<td align="center">'+data.page_text_match_type+'</td>';
			strHtml += '<td align="center">'+textMatchTypeVal+'</td>';
			strHtml += '<td align="center">'+data.page_ipdRuleTypeId+'</td>';
			strHtml += '<td align="center">'+ruleTypeValue+'</td>'; 
			strHtml += '<td align="center">'+data.page_ruleSortId+'</td>';
			strHtml += '<td>';
				strHtml += '<div class="czx">';
				strHtml += '<a name="'+idTr+'" href="javascript:void(0);" class="edit"></a>';				
				strHtml += '<a name="'+idTr+'" href="javascript:void(0);" class="del"></a>';
				strHtml += '</div>';
			strHtml += '</td>';	
	if(typeof(params.hiddenOnlyId) != 'undefined'){
		window.$('#'+htmlId+'>tr[id="'+idTr+'"]').html(strHtml);
	}else{
		strHtml ='<tr id="'+idTr+'">' + strHtml + '</tr>';
		window.$('#'+htmlId).append(strHtml);
	}	
	resetH();
};
function resetH() {
	window.parent.resetIframeH($('body').height()+ 30, 'editIpDetail');
}
