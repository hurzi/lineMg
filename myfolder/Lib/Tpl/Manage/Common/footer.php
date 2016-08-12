<!--弹窗框架小中大 开始-->
<div class="modal fade bs-example-modal-sm" id="smallModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
    
    </div>
  </div>
</div>

<div class="modal fade" id="defaultModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="largerModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    
    </div>
  </div>
</div>
<!--弹窗框架小中大 结束-->

<!-- begin footer -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2016 &copy; 技术支持：何钟强(hurzi@126.com)
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- end footer -->
<!-- begin 返回顶部 -->
<div class="scroll-to-top">
	<i class="icon-arrow-up"></i>
</div>
<script>
jQuery(document).ready(function() {
	// initiate layout and plugins
	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	Demo.init(); // init demo features
	bootbox.setDefaults("locale","zh_CN");
	ComponentsPickers.init();
	//左侧菜单当前状态
	$(".menu_zt").each(function(){
		var active=$(this).attr("name");
		if('active-yes'==active){
			//alert(menu_href);sub-menu
			var menu_up_href=$(this).parent().parent().attr("class");
			if(menu_up_href=="sub-menu"){
				//添加展开式菜单hover
				//alert($(this).parent().parent().parent().attr("class"));
				$(this).parent().addClass("active");
				$(this).parent().parent().css({"display":"block"});
				$(this).parent().parent().parent().addClass("active open");
				$(this).parent().parent().parent().children("a").children(".arrow").addClass("open");
			}else{
				//添加直接进入菜单hover
				$(this).parent().addClass("active");
			}
		}
	})
	/*=========错误提示=======*/
	UIToastr.init();
	toastr.options = {
	  "closeButton": true,
	  "positionClass": "toast-bottom-center"
	}
	/*=========弹层=======*/
  UIAlertDialogApi.init();
	$("#draggable").draggable({
	  handle: ".modal-header"
  });
	/*page.init();*/

});
</script>
</body>
<!-- end BODY -->
</html>