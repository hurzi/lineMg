<?php if (!empty($pagebar)) {?>
<div class="page-bar page-bar-top">
	<?php foreach ($pagebar as $k => $bar) {?>
	<ul class="page-breadcrumb">
		<li>
		<i class="fa <?=($k==0?'fa-home':'fa-angle-right')?>"></i>
		<a href="<?=($bar['url']?$bar['url']:'javascript:;')?>"><?=$bar['name']?></a>
		
	</ul>
	<?php }?>
</div>
<div class="clearfix"></div>
<?php }?>