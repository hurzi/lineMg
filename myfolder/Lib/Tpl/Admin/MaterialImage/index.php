<?php tpl('Common.header_1');?>
<div class="con_c_t">

	<div class="con_bzk">
		<div style="padding: 10px;">

			<div id="addOne" class="button green medium">添加图片素材</div>
			<div id="pageId" style="float: right">
			<?php echo $page;?>
			</div>
		</div>
	</div>
</div>
<div class="Mat_con">
<?php if(!$list){ ?>
	<p style="font-size: 15px; margin: 50px">无数据</p>
<?php }else{ ?>
	<div class="mat_l">
<?php
	foreach ($list as $k => $v) {
		if ($k % 2 == 0) {
			?>
		<div class="TW_box">
			<div class="tw_edit">
				<div class="czx">
				<a  class="<?php  if($v['is_show']==1){echo 'edit2';}else{echo 'edit1';}?> show1"   	uid="<?php echo $v['id'];?>"   state="<?php  echo $v['is_show']?>"></a>
					<a class="edit"
						href="<?php echo url('MaterialImage','edit',array('id'=>$v['id']));?>"
						name="<?php echo $v['id'];?>"></a>
					<a class="del" href="javascript:;"
						onclick="del(<?php echo $v['id'];?>)"
						name="<?php echo $v['id'];?>"></a>
				</div>
			</div>
			<div class="appTwb1">
				<h3 class="twh3">
					<a href="#"><?php echo $v['title'];?></a>
				</h3>
				<p class="twp"><?php echo date('Y-m-d', $v['inputtime']);?></p>
				<div class="reveal news_first" onclick="window.top.ligImg.showImg(this,'<?php echo $v['path'];?>')" style="background-image:url('<?php echo $v['path'];?>')">
				</div>
			</div>

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
				<a  class="<?php  if($v['is_show']==1){echo 'edit2';}else{echo 'edit1';}?> show1"   	uid="<?php echo $v['id'];?>"   state="<?php  echo $v['is_show']?>"></a>
					<a class="edit"
						href="<?php echo url('MaterialImage','edit',array('id'=>$v['id']));?>"
						name="<?php echo $v['id'];?>"></a>
					<a class="del" href="javascript:;"
						onclick="del(<?php echo $v['id'];?>)"
						name="<?php echo $v['id'];?>"></a>
				</div>
			</div>

			<div class="appTwb1">
				<h3 class="twh3">
					<a href="#"><?php echo $v['title'];?></a>
				</h3>
				<p class="twp"><?php echo date('Y-m-d', $v['inputtime']);?></p>
				<div class="reveal news_first"  onclick="window.top.ligImg.showImg(this,'<?php echo $v['path'];?>')"   style="background-image:url('<?php echo $v['path'];?>')">
				</div>
			</div>
		</div>
 	<?php }}?>
  </div>
  <?php } ?>
  </div>
<script type="text/javascript">
$(function(){
	var url = "<?php echo url('MaterialImage','ajaxIsSHow')?>";
	var href = "<?php echo url('MaterialImage','index')?>";
	$(".show1").click(function(){
		var id = $(this).attr('uid');
		var flag= $(this).attr('state');
		var content ={
				'id':id,
				state:flag
			};
			if(flag==1){
				var msg = '确认将该图片取消首页显示?' ;
			}else{
				var msg = '确认将该图片设为首页?' ;
			}
		jsConfirm(300,msg,function() {
			ajaxSubmit(url, 'post', content, function(status,result){
			if (status == false || result.error == 0) window.location.href=href;
		})
		});
	})
})

	$('#addOne').click(function(){
		window.location.href = "<?php echo url('MaterialImage','add');?>";
	});
	function del(img_id){
		var reqUrl = '<?php echo url('MaterialImage','delete');?>';
		var id = img_id;
		var datas = {'id':id};
		var param = {reqUrl:reqUrl,datas:datas};
		if(!jsConfirm(300,'确定要删除此条图片素材么？',request,param)){
			return;
		}
	}
	function request(param){
		$.ajax({
			url 	: param.reqUrl,
			type	: 'post',
			data	: param.datas,
			dataType: 'json',

			success:function(msg){
				window.top.loadMack({off:'on',Limg:0,text:msg.msg,set:1000});
				if(msg.error==0){
					setTimeout(function(){location.reload()},1000);
				}
			}
		});
	}
</script>

<?php tpl('Common.footer_1');?>
