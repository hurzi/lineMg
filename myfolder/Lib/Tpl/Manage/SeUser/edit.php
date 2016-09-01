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
                        <a href="javascript:;">管理员管理</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="<?=url('ZgykdxEvaluating','index')?>">增加管理员</a>
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
                                    <i class="fa fa-gift"></i>修改管理员</div>
                                
                            </div>
                            <div class="portlet-body form">
                                <!-- BEGIN FORM-->
                                <form id="saveBusinessForm" class="form-horizontal" method="post"  enctype="multipart/form-data">
                                  <div class="form-body">
                                    <h4 class="form-section">基本信息</h4>
                                        <div class="form-group margi_top">
                                            <label class="col-md-3 control-label">登陆账号<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                            	<input size="32" class="form-control" name="username" type="text" class="txt" id="username" value="<?php echo $operator['username'];?>" />
                                                <span class="help-block">用于系统登录</span>
                                                <span class="help-block" id="onlyone" style='color:red;'></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">姓名<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                            	<input size="32" class="form-control"  name="nickname" type="text" class="txt" id="nickname" value="<?php echo $operator['nickname'];?>" />
                                            	<span class="help-block">真实姓名</span>
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">所属企业<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                            	<select class="form-control" id="ent_id" name="ent_id">
													<option value="0">请选择</option>
													<?php if($entList){
														foreach ($entList as $ent){
														$selected = '';
														if($operator['ent_id'] == $ent['ent_id']){
															$selected = 'selected="selected"';
														}
													?>
													<option value="<?php echo $ent['ent_id'];?>"  <?php echo $selected;?>><?php echo $ent['ent_name'];?></option>
													<?php }}?>
												</select>
                                                <span class="help-block">所属企业</span>
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">管理员级别<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">                                            	
                                            	<select class="form-control" id="level" name="level">
													<option value="0">请选择</option>
								                    <?php if($levelList){
								                        foreach ($levelList as $k=>$v){
															if(UHome::getUserLevel()!=1 && $k == 1){
																continue;
															}
								                            ?>
								                    <option value="<?php echo $k;?>"<?php if($operator['level']==$k){ echo 'selected="selected"'; } ?>><?php echo $v;?></option>
								                    <?php }}?>
												</select>
                                                <span class="help-block">管理员级别</span>
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">重置密码<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                                <input name="password" class="form-control"  type="checkbox" id="password"  />
                                                <span class="help-block">重置密码默认为123456</span>
                                            </div>
                                        </div>
                                                                             
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                            	<input type="hidden" name="hideId" id="hideId" value="<?php echo $operator['user_id'];?>">
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

$(function () {
	$('#submit').click(function (){
		var username 		= $.trim($('#username').val());
		var nickname	    = $.trim($('#nickname').val());
		var password		= $('#password').attr('checked');
		var ent_id		    = $.trim($('#ent_id').val());
	    var level           = $('#level').val();
		var hideId          = $('#hideId').val();

		if (username == null || username == ''){
			Common.alert('管理员登陆名不能为空');
			return false;
		} else if (nickname == '' ||nickname== null) {
			Common.alert('管理员姓名不能为空');
			return false;
		}  else if(ent_id == 0 || ent_id == null || ent_id == ''){
	        if(level!=1 && level!=6){
	        	Common.alert('请选择管理员所属的企业');
	            return false;
	        }
		}
		if(password == 'checked'){
			password = 123456;
		}else{
			password = '';
		}
			
		var params = {
				user_id : hideId,
				username 	: username,
				nickname    : nickname,
				password    : password,
				ent_id      : ent_id,
	            level       : level
			};
		var url = "<?php echo url('SeUser', 'update');?>";
		var href = "<?php echo url('SeUser', 'index');?>";
		
		Common.request(url, params, 
				function(result,status){
					doAjax = false;
					Common.alert("操作成功",function(){
						window.location.href = href;
					});
				});
		});
});

function get_back(){

    history.go(-1);
}

</script>
