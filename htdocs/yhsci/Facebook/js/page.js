// JavaScript Document
jQuery(document).ready(function(){
	//课酬体系——阶梯规则
	jQuery("#step-rule input[type='text']").focus(function(){
		jQuery(this).css({
			"border-bottom":"1px solid #a94442",
		});
	});
	jQuery("#step-rule input[type='text']").blur(function(){
		jQuery(this).css("border-bottom","0");
	});
	//课酬体系——添加城市展开 && 收起
	jQuery(".add-city").click(function(){
		jQuery(this).hide();
		jQuery(".more-city").show("flow");
	});
	jQuery(".hide-city").click(function(){
		jQuery(".add-city").show();
		jQuery(".more-city").hide("flow");
	});
	//课酬体系——点击添加适用城市
	jQuery(".more-city font").click(function(){
		jQuery(this).remove();
		jQuery(this).insertBefore(".fit-city a");
	});
	//课酬体系——点击删除适用城市
	jQuery(".fit-city font").click(function(){
		jQuery(this).remove();
		jQuery(this).insertBefore(".more-city a");
	});
	//教师标签管理——随机变色
	var tags_a = jQuery("#tags a"); 
	tags_a.each(function(){ 
		var x = 13; 
		var y = 0; 
		var rand = parseInt(Math.random() * (x - y + 1) + y); 
		jQuery(this).addClass("tags"+rand); 
	}); 
	//教师基础管理——教师基本信息
	jQuery(".teacher-basic-infor input[type='text']").focus(function(){
		jQuery(this).css({
			"border-bottom":"1px solid #a94442",
		});
	});
	//教师基础管理——教师基本信息
	jQuery(".teacher-infor .close").click(function(){
		jQuery(this).parent().remove();
	});
	//教师基础管理——教师基本信息
	jQuery(".edit-teacher-infor").click(function(){
		jQuery(".teacher-basic-infor input[type='text']").css({
			"border-bottom":"1px solid #a94442",
		});
	});
	//教师基础管理——添加代课科目、年级、校区展开 && 收起
	jQuery(".sub-class-list a, .grade a, .campus a").click(function(){
		jQuery(this).hide();
		jQuery(this).parent().next().show("flow");
	});
	jQuery(".more-sub a, .grade-more a, .campus-more a").click(function(){
		jQuery(this).parent().prev().find("a").show();
		jQuery(this).parent().hide("flow");
	});
	//教师基础管理——点击添加代课科目
	jQuery(".more-sub font").click(function(){
		jQuery(this).remove();
		jQuery(this).insertBefore(".sub-class-list a");
	});
	//教师基础管理——点击删除代课科目
	jQuery(".sub-class-list font").click(function(){
		jQuery(this).remove();
		jQuery(this).insertBefore(".more-sub a");
	});
	//教师基础管理——点击添加代课年级
	jQuery(".grade-more font").click(function(){
		jQuery(this).remove();
		jQuery(this).insertBefore(".grade a");
	});
	//教师基础管理——点击删除代课年级
	jQuery(".grade font").click(function(){
		jQuery(this).remove();
		jQuery(this).insertBefore(".grade-more a");
	});
	//教师基础管理——点击添加代课校区
	jQuery(".campus-more font").click(function(){
		jQuery(this).remove();
		jQuery(this).insertBefore(".campus a");
	});
	//教师基础管理——点击删除代课校区
	jQuery(".campus font").click(function(){
		jQuery(this).remove();
		jQuery(this).insertBefore(".campus-more a");
	});
	//教师基础管理——课和管理
	jQuery(".fc-day-number").click(function(){
		jQuery("#courses-management").fadeIn()
	});






	//花园管理——我的问答社区 显示回答输入框
	jQuery(".querstion-list a.answer-btn").click(function(){
		jQuery(this).parent().hide();
		jQuery(this).parent().prev().show("flow");
	});
	//花园管理——我的问答社区 隐藏回答输入框
	jQuery(".answer-box button.default").click(function(){
		jQuery(this).parent().hide("flow");
		jQuery(this).parent().next().show("flow");
	});
	//花园管理——我的问答社区 隐藏回答输入框
	jQuery(".seat-pop-body .col-md-2 > div .close").click(function(){
		jQuery(this).parent().remove();
	});
});































































