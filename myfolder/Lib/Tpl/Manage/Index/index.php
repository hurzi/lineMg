<?php tpl('Common.header')?>
<!-- begin main -->
<div class="page-container">
    <?php tpl('Common.left')?>
    <!-- begin 主体内容 -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- begin 页面导航 -->
			<div class="page-bar page-bar-top">
				<ul class="page-breadcrumb">
					<li>
						<i class="fa fa-home"></i>
						<a href="<?=url('index')?>">首页</a>
						<i class="fa fa-angle-right"></i>
					</li>
				</ul>

			</div>
			<div class="clearfix"></div>
			<!-- end 页面导航 -->
			<div class="row">
				<div class="col-sm-12">
					<div class="portlet box grey-cascade">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-gift"></i>快捷菜单
                            </div>
                        </div>
                        <div class="portlet-body form">
                        	<div class="tiles mag_box">
								<a href='<?=url("Enterprise","add")?>'><div class="tile bg-blue col-lg-2 col-md-2 col-sm-6 col-xs-12">
									<div class="tile-body">
										<i class="glyphicon glyphicon-plus col_font"></i>
									</div>
									<div class="tile-object">
										<div class="name text-center">
											 增加商户
										</div>
										<div class="number">
										</div>
									</div>
								</div></a>
								<a href='<?=url("Order","add")?>'><div class="tile bg-blue col-lg-2 col-md-2 col-sm-6 col-xs-12">
									<div class="tile-body">
										<i class="glyphicon glyphicon-th-list col_font"></i>
									</div>
									<div class="tile-object">
										<div class="name text-center">
											 增加订单
										</div>
										<div class="number">
										</div>
									</div>
								</div></a>
								<a href='<?=url("Order","index")?>'><div class="tile bg-blue col-lg-2 col-md-2 col-sm-6 col-xs-12">
									<div class="tile-body">
										<i class="glyphicon glyphicon-search col_font"></i>
									</div>
									<div class="tile-object">
										<div class="name text-center">
											 查询订单
										</div>
										<div class="number">
										</div>
									</div>
								</div></a>
								<a href='<?=url("Agent","index")?>'><div class="tile bg-blue col-lg-2 col-md-2 col-sm-6 col-xs-12">
									<div class="tile-body">
										<i class="glyphicon glyphicon-cog col_font"></i>
									</div>
									<div class="tile-object">
										<div class="name text-center">
											 修改信息
										</div>
										<div class="number">
										</div>
									</div>
								</div></a>
								
				
                        </div>
                    </div>
    			</div>
    		<!-- 表单结束 -->
				</div>
			</div>
		</div>
		<!-- begin 快捷侧边栏 -->
		<a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-login"></i></a>
		<!-- end 快捷侧边栏 -->
</div>
<?php tpl('Common.footer')?>
