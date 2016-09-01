<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
			<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
			<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
			<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
			<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
			<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
			<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler"></div>
					<!-- END SIDEBAR TOGGLER BUTTON -->
				</li>
				<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
				<?php $active = (in_array(__ACTION_NAME__ ,array('Index','Notice')) )?'active-yes':'active-no';?>
				<li>
					<a class="menu_zt" href="./index.php" name="<?=$active?>">
					<i class="icon-home"></i>
					<span class="title">首页</span>
					</a>
				</li>
				<?php if(isset($sysmenu) && $sysmenu ): foreach ($sysmenu as $row):?>
				<li id="li_<?=$row['id'];?>">
					<a href="">
					<i class="<?=$row['icon']?>"></i>
					<span class="title"><?=$row['name'];?></span><span class="arrow "></span>
					</a>
					<ul class="sub-menu" id="ul_<?=$row['id'];?>">
					<?php foreach($row['child'] as $child):?>
					<?php
		            $active = 'active-no';
		            $method = __ACTION_NAME__.'.'.__ACTION_METHOD__;
		            $isMethod = (!empty($child['method']) && in_array($method, $child['method']));
		            @$child['key'] OR $child['key'] = array();
		            @$child['ignore_method'] OR $child['ignore_method'] = array();
		            if ($isMethod || (in_array(__ACTION_NAME__, $child['key']) && !in_array($method, $child['ignore_method']))) {
		            	$active = 'active-yes';
					}?>
					<li>
						<a class="menu_zt" href="<?=$child['url'];?>" name="<?=$active?>">
						<i class="<?=$child['icon']?>"></i><?=$child['name'];?></a>
					</li>
					<?php endforeach;?>
					</ul>
				</li>
				<?php endforeach; endif;?>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->