var is_ajax = false;
var ifShowKeyword = true;
var selectPushType = "text";
var selectPostType = "text";
var textRegexType = "email";
var textMatchType = "4";

var ifShowNumRegex = false;


//初始化相关内容
$(document).ready(function(){
	$('#ipdSubmit').click(function(){
		addIpdDetail();
	});
	$('#ipdClear').click(function(){
		parent.topClose();
	});
});

function addIpdDetail(){
	if(is_ajax==true) return;
	var ifChangeImgText = $('#change_imgtext').val();
	var ip_id 			= $('#ip_id').val(); 	//当前流程id
	var ipd_id 			= $('#ipd_id').val();   //步骤id
	var ipd_name		= $('#ipd_name').val();  //步骤名称
	var push_type 		= selectPushType; //推送类型，暂时只推送文本
	var push_data  	    = ""; //用于存放推送值
	if(push_type=="text"){
		push_data = $('#ipdPostText').val(); //推送文本值
	}else if(push_type=="image"){

	}else if(push_type=="news"){
		if($('#material_id').val()!="0"||initPostType=="news"){
			push_data = $('#material_id').val();
		}
	}else if(push_type=="music"){

	}else if(push_type=="third"){
		push_data = $('#ipdPostThirdUrl').val();
	}else{
		push_data = $('#ipdPostText').val();
	}

	var ipd_status = $("input[name='ipd_status']:checked").val(); //是否结束流程值

	var send_error_text = $('#sendErrorText').val(); //推送失败提示
	var match_type = selectPostType;
	var text_match_type = textMatchType;  //文本匹配类型(包括全部、精确、模糊、正则)
	var text_match_data = "";
	if(text_match_type=='3'){ //表示正则匹配
		text_match_data = textRegexType; //取正则匹配类型
	}else{
		if(text_match_type!='4'){ //表示精确匹配或模糊匹配
			text_match_data = $('#inputKeyOrReg').val(); //取匹配关键词
		}
	}

	var minRegexNum = $('#minRegexNum').val(); //匹配数字最小位数
	var maxRegexNum = $('#maxRegexNum').val(); //匹配数字最大位数

	if(ipd_name==''){
		jsAlert('步骤名称不能为空');
		return false;
	}
	if(checkNum(ipd_name)>20){
		jsAlert('步骤名称不能大于10个汉字');
		return false;
	}
	if(push_type==''){
		jsAlert('请选择推送方式');
		return false;
	}
	if(push_data==''||push_data=='请输入内容'||push_data=='http://'){
		jsAlert('推送内容不能为空');
		return false;
	}
	//判断url是否有效
	if(push_type=="third"){
// 		if(!push_data.match(/^http[s]{0,1}:\/\/([\w-]+\.)+[\w-]+[\/]?$/)){
// 			jsAlert('您输入的URL无效,请修改');
// 			return false;
// 		}
	}

	if(ipd_status=='0'){
		if(send_error_text==''||send_error_text=='请输入内容'){
			jsAlert('推送失败提示不能为空');
			return false;
		}
		var ruleListBodyIdLen = ('#ruleListBodyId>tr').length;
		if(ruleListBodyIdLen == 0){
			jsAlert('请至少创建一条推送规则');
			return false;
		}
		if(countObject(cacheRuleDataTmp) < RULE_MIN_COUNT){
			jsAlert('请至少创建'+RULE_MIN_COUNT+'条推送规则');return false;
		}		
	}

	is_ajax = true;
	var param = {};
	if(ipd_status=='1'){
		param = {
				ip_id : ip_id,
				ipd_id : ipd_id,
				ipd_name : ipd_name,
				push_type : push_type,
				push_data : push_data,
				ipd_status : ipd_status,
				ifChangeImgText : ifChangeImgText
		};
	}
	else{
		if(match_type=='text'){
			param = {
					ip_id : ip_id,
					ipd_id : ipd_id,
					ipd_name : ipd_name,
					push_type : push_type,
					push_data : push_data,
					ipd_status : ipd_status,
					send_error_text : send_error_text,
					match_type : match_type,
					
					ruleData   : cacheRuleData,
					ruleDataTmp:cacheRuleDataTmp,
					cacheRuleDataDelete : cacheRuleDataDelete,
					
					text_match_type : text_match_type,
					text_match_data : text_match_data,
					textRegexType : textRegexType,
					minRegexNum : minRegexNum,
					maxRegexNum : maxRegexNum,
					ifChangeImgText : ifChangeImgText
			};
		}
		else{
			param = {
					ip_id : ip_id,
					ipd_id : ipd_id,
					ipd_name : ipd_name,
					push_type : push_type,
					push_data : push_data,
					ipd_status : ipd_status,
					send_error_text : send_error_text,
					match_type : match_type,
					
					ruleData   : cacheRuleData,
					ruleDataTmp:cacheRuleDataTmp,
					
					ifChangeImgText : ifChangeImgText
			};
		}
	}
	loadMack({off:'on'});
	$.post(updateIpDetailUrl,param,function(msg){
		var new_msg = eval("("+msg+")");
		loadMack({off:'off'});
		is_ajax = false;
		
		var param_return = [new_msg];
		if(new_msg.error == 0){
			jsAlert(new_msg['msg'],addIpdDetailReturn,param_return);
		}else{
			jsAlert(new_msg['msg']);
		}
	});
}

//全局管理ui
/*function IpDetailManager () {
	
}*/
//管理规则
/*function IpDetailRule () {
	
}*/


