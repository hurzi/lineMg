<?php tpl('Common.header')?>
<!-- begin main -->
<div class="page-container">
    <?php tpl('Common.left')?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- begin 页面导航 -->
            <div class="page-bar page-bar-top">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="javascript:;">微信素材管理</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="<?=url('WxCustomMenu','index')?>">图文素材管理</a>
                    </li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <!-- end 页面导航 -->

            <!-- 表格开始 -->
        <div class="row">
                <div class="col-md-12">
                    <div class="portlet box grey-cascade">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-gift"></i>图文素材管理
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="form-group ">
                                    <a class="btn blue dropdown-toggle" href="<?php echo url('WxMaterialNews','add',array('news_type'=>1));?>" > 增加单图文 </a>  <!-- data-toggle="dropdown" -->
                                    <a class="btn blue dropdown-toggle" href="<?php echo url('WxMaterialNews','add',array('news_type'=>2));?>" > 增加多图文 </a>  <!-- data-toggle="dropdown" -->
                            </div>
                            <div class="table-scrollable">
                            <!-- start content -->
                            <?php if(!$list){ ?>
	<p style="font-size: 20px; margin: 50px">无数据</p>
<?php }else{ ?>
<?php
	foreach ($list as $k => $v) {
			?>			
	<div class="mat_f3">
		<div class="TW_box">
			<div class="tw_edit">
				<div class="czx">
					<a class="edit" href="<?php echo url('WxMaterialNews','edit',array('id'=>$v['id']));?>" name="<?php echo $v['id'];?>"></a> <a class="del" href="javascript:;" onclick="del(<?php echo $v['id'];?>)" name="<?php echo $v['id'];?>"></a>
				</div>
			</div>
			<?php
			//$data = unserialize($v['articles']);
			$data = $v['articles'];
			$data_count = count($data);
			if ($data_count > 1) {
				?>
			<div class="appTwb1">
				<p class="twp"><?php echo date('Y-m-d',strtotime($v['create_time']))?></p>
				<div class="reveal news_first" style="background-image:url('<?php echo $data[0]['picurl'];?>')">
					<h5 class="tw_z">
						<a class="z_title" href="javascript:;"><?php echo htmlspecialchars($data[0]['title']);?></a>
					</h5>
				</div>
			</div>
			<div class="appTwb2">
			<?php
				for ($i = 1; $i < $data_count; $i ++) {
					?>
				<div class="tw_li">
					<a class="atext" href="javascript:;"><?php echo htmlspecialchars($data[$i]['title']);?></a> <img width="70" height="70" src="<?php echo $data[$i]['picurl'];?>" />
				</div>
			<?php } ?>
			</div>
			<?php }else{ ?>
			<div class="appTwb1">
				<h3 class="twh3">
					<a href="javascript:;"><?php echo htmlspecialchars($data[0]['title']);?></a>
				</h3>
				<p class="twp"><?php echo date('Y-m-d', strtotime($v['create_time']));?></p>
				<div class="reveal news_first" style="background-image:url('<?php echo $data[0]['picurl'];?>')"></div>
			</div>
			<div class="appTwb2">
				<div class="tw_text">
					<p><?php echo htmlspecialchars($data[0]['description']);?></p>
				</div>
			</div>
		<?php } ?>
			</div>
			</div>
			<?php } } ?>
                            <!-- end content -->
                            </div>
                            
                        </div>
                            <?=$page;?>
                            <a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-login"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <!-- begin 快捷侧边栏 -->
        <a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-login"></i></a>
        <!-- end 快捷侧边栏 -->
    </div>
</div>
<!-- end main -->
<?php tpl('Common.footer')?>
<script type="text/javascript">
function del(news_id){
	var url = '<?php echo url('WxMaterialNews','delete');?>';
	Common.confirm('确定要删除此条图文素材么？', function (){
		var params = {
			id:news_id
		};
		Common.request(url,  params, function(){
			Common.alert("删除成功",function(){
				window.location.reload();
			});			
		});
	});
}
</script>









