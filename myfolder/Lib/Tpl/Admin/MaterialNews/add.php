<?php tpl('Common.header_1');?>
<?php if($do_type=='add'){?>
<input type="hidden" id="news_type" value="<?php echo $news_type;?>" />
<?php }else{?>
<input type="hidden" id="news_id" value="<?php echo $id;?>" />
<?php }?>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<div id="reTo" class="button blue medium addFz con_l">返回素材库</div>
			<?php if($do_type !='add') {?><div class="material_update">您的修改将影响所有引用本素材的功能</div><?php };?>
		</div>
	</div>
</div>
<div id="show_editor"></div>

<script type="text/javascript">
$('#reTo').click(function(){
	var news_id = $('#news_id').val();
	if(!news_id){
		window.location.href = "<?php echo url('MaterialNews','index');?>";
	}else{
		history.back(-1);
	}
});
if(window.top==window){
	$('body').css({'width':'960px','margin':'0px auto'});
	$('.con_c_t').remove();
	$('.msg-input').css({'max-width':'400px','width':'293px'});
}

var is_ajax = false;
var do_type = '<?php echo $do_type;?>' || 'add';
var param = {title_max:<?php echo $title_max; ?>,desc_max:<?php echo $desc_max;?>};
var editor = {};

$(function() {
	if(do_type=='add'){
		var type = $('#news_type').val();
		editor = new Abc.MessageEditor(type, null, param);
	}else{
		var data = <?php echo @$news ? $news : '[]';?>;
		var parse_data = new Array();
		for(var i=0;i<data.length;i++){
			parse_data[i] = {};
			parse_data[i].news_img_url = data[i]['news_img_url'];
			parse_data[i].news_show_cover_pic = data[i]['news_show_cover_pic'];
			parse_data[i].news_title = data[i]['news_title'];
			parse_data[i].news_author = data[i]['news_author'];
			parse_data[i].news_description = data[i]['news_description'];
			parse_data[i].news_url= data[i]['news_url'];
			parse_data[i].news_content = data[i]['news_content'];
		}
		var type = 1;
		if(data.length>1){
			type = 2;
		}
		editor = new Abc.MessageEditor(type, parse_data, param);
	}
	editor.show('show_editor');

	var footer_html = '<div class="mat_f">' +
		'<a href="javascript:;" class="button green medium Yfs" id="send_preview">发送预览</a>'+
		'<a href="javascript:;" class="button green medium" id="add">完成</a>' +
		'</div>';
	$('.Mat_con').append(footer_html);
});

$('#add').live('click',function(){
	if(is_ajax){
		return;
	}
	is_ajax = true;
	var data = editor.getData();
	if(!Abc.MessageEditor.checkData(data)){
		is_ajax = false;
		return;
	}else{

		var id = $('#news_id').val();
		var param = {id:id,news:data};

		if(do_type=='add'){
			var url = '<?php echo url('MaterialNews','insert');?>';
			topLoadMack({off:'on',text:'新建中...'});
			$.post(url,param,function(msg){
				topLoadMack({off:'off'});
				msg = eval('('+msg+')');
				jsAlert(msg['msg'], function () {
					is_ajax = false;
					if(msg['error']==0){
					if (do_type=='add') {
						location.href='<?php echo url('MaterialNews','index');?>';
					} else {
						history.back(-1);
					}
				}});
			});

		}else{
			var url = '<?php echo url('MaterialNews','update');?>';
			jsConfirm(300, '您的修改将影响所有引用本素材的功能，您确认修改么？', function (){
				topLoadMack({off:'on',text:'更新中...'});
				$.post(url,param,function(msg){
					topLoadMack({off:'off'});
					msg = eval('('+msg+')');
					jsAlert(msg['msg'], function () {
						is_ajax = false;
						if(msg['error']==0){
						if (do_type=='add') {
							location.href='<?php echo url('MaterialNews','index');?>';
						} else {
							history.back(-1);
						}
					}});
				});
			}, '', function () {
				is_ajax = false;
			});
		}
	}
});

//发送预览
function send_preview_message(){
	if(is_ajax){
		return;
	}
	is_ajax = true;
	var weixin_id = $('#wx_number').val();
	if(weixin_id==''){
		loadMack({off:'on',Limg:0,text:'微信号码不能为空。',set:1000});
		is_ajax = false;
		return;
	}
	var data = editor.getData();
	if(!Abc.MessageEditor.checkData(data)){
		is_ajax = false;
		return;
	}else{
		loadMack({off:'on',text:'发送中...'});
		var url = '<?php echo url('MaterialNews','send');?>';
		var param = {nickname:weixin_id,send_data:data};
		$.post(url,param,function(msg){
				msg = eval('('+msg+')');
				loadMack({off:'off'});
				is_ajax = false;
				loadMack({off:'on',Limg:0,text:msg['msg'],set:1000});
				if(msg['error']==0){
					$('.jsbox_close').click();
					//var id = msg['data'];
					//$('#news_id').val(id);
					jsAlert('发送成功');
				}else{
					jsAlert(msg['msg']);
				}
		});
	}
}
</script>
<?php tpl('Common.footer_1');?>