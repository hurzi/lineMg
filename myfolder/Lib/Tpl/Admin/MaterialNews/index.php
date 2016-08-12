<?php tpl('Common.header_1');
?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<div id="addOne" class="button green medium">新建单图文</div>
			<div id="addMore" class="button green medium">新建多图文</div>
			<div id="pageId" style="float: right">
			<?php echo $page;?>
			</div>
		</div>
	</div>
</div>
<div class="Mat_con">
<?php if(!$list){ ?>
	<p style="font-size: 20px; margin: 50px">无数据</p>
<?php }else{ ?>
	<div class="mat_l">
<?php
	foreach ($list as $k => $v) {
		if ($k % 2 == 0) {
			?>
		<div class="TW_box">
			<div class="tw_edit">
				<div class="czx">
					<a class="edit" href="<?php echo url('MaterialNews','edit',array('id'=>$v['id']));?>" name="<?php echo $v['id'];?>"></a> <a class="del" href="javascript:;" onclick="del(<?php echo $v['id'];?>)" name="<?php echo $v['id'];?>"></a>
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
			<?php }}?>
			</div>
	<div class="mat_r">
		<?php
		foreach ($list as $k => $v) {
			if ($k % 2 == 1) {
		?>
		<div class="TW_box">
			<div class="tw_edit">
				<div class="czx">
					<a class="edit" href="<?php echo url('MaterialNews','edit',array('id'=>$v['id']));?>" name="<?php echo $v['id'];?>"></a> <a class="del" href="javascript:;" onclick="del(<?php echo $v['id'];?>)" name="<?php echo $v['id'];?>"></a>
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
			<?php }}?>
			</div>
			<?php } ?>
		</div>
<div class="con_bzk">
	<div style="padding: 10px;">
		<div id="pageId" style="float: right">
			<?php echo $page;?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#addOne').click(function(){
		window.location.href = "<?php echo url('MaterialNews','add',array('news_type'=>1));?>";
	});
	$('#addMore').click(function(){
		window.location.href = "<?php echo url('MaterialNews','add',array('news_type'=>2));?>";
	});
	function del(news_id){
		var url = '<?php echo url('MaterialNews','delete');?>';

		jsConfirm(300, '确定要删除此条图文素材么？', function (){
			var params = {
				id:news_id
			};
			ajaxSubmit(url, 'POST', params, function(status, result){
				if(result.error==0){
					window.location.reload();
				}
			}, '删除成功');
		});
	}
</script>
<?php tpl('Common.footer_1');?>