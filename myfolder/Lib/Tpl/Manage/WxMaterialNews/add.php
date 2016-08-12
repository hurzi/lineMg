<?php tpl('Common.header')?>
<!-- begin main -->
<script type="text/javascript" src="<?=URL;?>js/areaSelecter.js"></script>
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
                        <a href="<?=url('WxMateriaNews','index')?>">素材管理</a>
                    </li>
                </ul>

            </div>
            <div class="clearfix"></div>
            <!-- end 页面导航 -->

            <!-- 表单开始 -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box grey-cascade">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-gift"></i>编辑素材</div>
                                
                            </div>
                            <div class="portlet-body">
                            	<?php if($do_type !='add') {?><div class="material_update">您的修改将影响所有引用本素材的功能</div><?php };?>
                                <div class="box row-fluid">
                                	<div id="show_editor"></div>
                                </div>
                                <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">                                            
												<?php if($do_type=='add'){?>
												<input type="hidden" id="news_type" value="<?php echo $news_type;?>" />
												<?php }else{?>
												<input type="hidden" id="news_id" value="<?php echo $id;?>" />
												<?php }?>
                                                <button type="button" id="preview_btn" class="btn blue" onclick="previewBtn()">发送预览</button>
                                                <button type="button" id="submit" class="btn blue">提交</button>
                                                <button type="button" onclick='get_back()' class="btn">取消</button>
                                            </div>

                                        </div>
                                </div>
                                <!-- END FORM-->
                            </div>
                            </div>
                            </div>
                    </div>
                    <!-- 表单结束 -->
        </div>
        <!-- begin 快捷侧边栏 -->
        <a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-login"></i></a>
        <!-- end 快捷侧边栏 -->
    </div>
</div>
<!-- end main -->
<?php tpl('Common.footer')?>
<script type="text/javascript">
$('#reTo').click(function(){
	var news_id = $('#news_id').val();
	if(!news_id){
		window.location.href = "<?php echo url('WxMaterialNews','index');?>";
	}else{
		history.back(-1);
	}
});
if(window.top==window){
	//$('body').css({'width':'960px','margin':'0px auto'});
	//$('.con_c_t').remove();
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
		'</div>';
	$('.Mat_con').append(footer_html);
});

$('#submit').click(function(){
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
			var url = '<?php echo url('WxMaterialNews','insert');?>';
			Common.request(url,param,function(msg){
				Common.alert(msg['msg'], function () {
					is_ajax = false;
					if (do_type=='add') {
						location.href='<?php echo url('WxMaterialNews','index');?>';
					} else {
						history.back(-1);
					}
				});
			});

		}else{
			var url = '<?php echo url('WxMaterialNews','update');?>';
			Common.confirm('您的修改将影响所有引用本素材的功能，您确认修改么？', function (){
				Common.request(url,param,function(result){
					Common.alert(result['msg'], function () {
						is_ajax = false;
						if (do_type=='add') {
							location.href='<?php echo url('WxMaterialNews','index');?>';
						} else {
							history.back(-1);
						}
					});
				});
			});
		}
	}
});

function previewBtn() {
	var initHtml = '<div style="margin-left:30px;margin-top:24px;height:50px"><span class="twp" style="font-size:13px;">请输入微信昵称:</span><input class="form-control" style="display:inline;width:370px"type="text" id="wx_number"/></div>';
	var title = "预览素材";
	bootbox.dialog({
		"message":initHtml,  //内容
		"title":title,		//标题
		"onEscape":function(){},  //退出时事件
		"show":true,  //是否显示此dialog,
		"closeButton" : true, //是否显示关闭按钮，默认true
		"animate":true,//是否动画弹出dialog，IE10以下版本不支持
		"className":"preview_dialog",  //dialog的类名通过此可改变高宽
		"buttons":{
			"success" :{
				label: "确定",
				className: "btn-success AddOFs",
				callback: send_preview_message
			},
			"cancel" :{
				label: "取消",
				className: "btn-cancel ML_close",
				callback: function(){}
			}
		}			
	});

}

//发送预览
function send_preview_message(){
	var weixin_id = $('#wx_number').val();
	if(weixin_id==''){
		Common.alert('微信号码不能为空。');
		return false;
	}
	var data = editor.getData();
	if(!Abc.MessageEditor.checkData(data)){
		return false;
	}else{
		var url = '<?php echo url('WxMaterialNews','send');?>';
		var param = {nickname:weixin_id,send_data:data};
		Common.request(url,param,function(result){
				Common.alert("发送成功");
		});
	}
}
</script>

