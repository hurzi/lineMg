;(function($) {
		$.fn.extend({
			
	    "checkboxqx":function(options){
			
				$(options).live('click',function(){

					//所有checkbox跟着全选的checkbox走。
					$('[name=items]:checkbox').attr("checked", this.checked );
				 });
				 $('[name=items]:checkbox').live('click',function(){
					//定义一个临时变量，避免重复使用同一个选择器选择页面中的元素，提升程序效率。
					var $tmp=$('[name=items]:checkbox');
					//用filter方法筛选出选中的复选框。并直接给CheckedAll赋值。
					$(options).attr('checked',$tmp.length==$tmp.filter(':checked').length);
				 });
			
		 },
		 "TabNav":function(options, callback){
			 var _this= $(this);
			 $('.tab li',_this).live('click',function(){
				  var m = $('.tab li',_this).index(this);
				  _this.find('.tab li').removeClass('tab_xz');
				  $(this).addClass('tab_xz');
				  _this.find('.n_content_all').hide();
				  _this.find('.n_content_all:eq('+m+')').show();
				  
				  if (callback) {
					  callback.call(null, m);  
				  }
				  parentSH();
				  
			 });
		 }	  
             
		});

})(jQuery);