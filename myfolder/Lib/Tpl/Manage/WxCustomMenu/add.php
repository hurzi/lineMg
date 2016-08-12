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
                        <a href="javascript:;">微信菜单管理</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="<?=url('WxCustomMenu','index')?>">菜单管理</a>
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
                                    <i class="fa fa-gift"></i>添加菜单</div>
                                
                            </div>
                            <div class="portlet-body form">
                                <!-- BEGIN FORM-->
                                <form id="saveBusinessForm" class="form-horizontal" method="post"  enctype="multipart/form-data">
                                  <div class="form-body">
                                    <h4 class="form-section">基本信息</h4>
                                        <div class="form-group margi_top">
                                            <label class="col-md-3 control-label">菜单名称<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="menuName" id="menuName" value="">
                                                <span class="help-block">必填，微信菜单的名称</span>
                                                <span class="help-block" id="onlyone" style='color:red;'></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">菜单排序值<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                            	<input type="text" class="form-control" name="menuOrder" id="menuOrder" value="">
                                                <span class="help-block">菜单的排序值</span>
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">上级菜单<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                            	<select class="form-control" id="parentId" name="parentId">
													<option  value="0">--根菜单--</option>
													<?php
													if($parentList){
														foreach($parentList as $k => $v ){
															$menuId = $v['id'];
													?>
														<option  value="<?php echo $menuId; ?>">
															<?php echo $v['name']; ?>
														</option>
													<?php }}?>
												</select>
                                                <span class="help-block">上级菜单</span>
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">消息来源<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">                                            	
                                            	<select class="form-control" id="menuType" name="menuType" onchange="changeMenuReturnType()">
													<option value="1">固定回复</option>
													<option value="2">动态获取</option>
													<option value="3">访问网页</option>
												</select>
                                                <span class="help-block">消息来源</span>
                                            </div>
                                        </div> 
                                        <div class="form-group dynamic_div">
                                            <label class="col-md-3 control-label">动态获取url</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="dynamic_url" id="dynamic_url" value="0">
                                                <span class="help-block">必填,地址必须以http://或https://开头</span>
                                            </div>
                                        </div>   
                                        <div class="form-group visit_div">
                                            <label class="col-md-3 control-label">访问网页url</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="visit_url" id="visit_url" value="0">
                                                <span class="help-block">必填,地址必须以http://或https://开头</span>
                                            </div>
                                        </div>   
                                        <div class="form-group preinstall_div">
                                            <label class="col-md-3 control-label">选择素材</label>
                                            <div class="col-md-4">
                                            	<div id="system_preinstall"></div>
                                            </div>
                                        </div>
                                                                             
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="button" id="submit" class="btn blue">提交</button>
                                                <button type="button" onclick='get_back()' class="btn">取消</button>
                                            </div>

                                        </div>
                                    </div>
                                </form>
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

var setUrl = "<?php echo url('WxCustomMenu', 'insert').'&is_thread=' . $isThread;?>";
var href = "<?php echo url('WxCustomMenu', 'index').'&is_thread=' . $isThread;?>";
var isThread = "<?php echo $isThread; ?>";

var oauth_set = true;
var oauth_checked = false;
//已选消息内容（messageSelector组件用到变量）
$(function() {
	//messageSelector组件构造方法传递参数
	var param = {
			callback : msgCallback,
			btn_value: '确认修改',
			remark: '',
			oauth_set: oauth_set,//是否显示选择OAuth选项
			oauth_checked: oauth_checked //当前选中状态
	};
	if (isThread == 1) {
		param.msg_types = ['text','news','music','image','voice','video'];
	} else {
		param.msg_types = ['text','news','music'];
	}
	//声明messageSelector组件对象
	var msgSelector = new MessageSelector(param);
	//在括号里的ID中显示组件
	msgSelector.render("system_preinstall");
	
	//类型初始化
	changeMenuReturnType();
	
	//确认修改
	$('#submit').click(function() {
		if(doAjax == true){
			return;
		}
		var params = getFormData(msgSelector);
		if (! params) {
			return;
		}
		doAjax == true;
		Common.request(setUrl, params, function(result,status){
			doAjax = false;
			Common.alert("增加成功",function(){window.location.href=href});
		});
	});
});
</script>

