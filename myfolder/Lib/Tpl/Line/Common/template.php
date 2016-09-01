<?php tpl('Common.header')?>
<!-- begin main -->
<div class="page-container">
    <?php tpl('Common.left')?>
    <!-- begin 主体内容 -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- begin 页面导航 -->
			<?php tpl("Common.pagebar")?>
			<!-- end 页面导航 -->
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box grey-cascade">
						<div class="portlet-title">
	                        <div class="caption"><i class="fa fa-gift"></i><?=@$pannelTitle?></div>
	                    </div>
	                    <div class="portlet-body form">
	                    <?php tpl($mainTemplate);?>
	                    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end 主体内容 -->
	<!-- begin 快捷侧边栏 -->
	<a href="javascript:;" class="page-quick-sidebar-toggler"><i
		class="icon-login"></i></a>
	<!-- end 快捷侧边栏 -->
</div>
<!-- end main -->
<?php tpl('Common.footer')?>
