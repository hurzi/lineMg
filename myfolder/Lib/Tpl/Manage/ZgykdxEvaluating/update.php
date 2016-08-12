<?php tpl('Common.header')?>
<!-- begin main -->
<style>
.upload_ifr{border:0 none; height:0; width:0; opacity:0;}
.upload_con {margin-right: 30px;}
.upload_con .img_con1 {height: 100px;margin-top: 10px;}
.upload_con .img_con2  {height: 230px;margin-top: 10px;}
.upload_con .img_con1 img {width:167px;height:95px;}
.upload_con .img_con2 img {width:167px;height:215px;}
</style>
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
                        <a href="javascript:;">评教管理</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="<?=url('ZgykdxEvaluating','index')?>">评教管理</a>
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
                                    <i class="fa fa-gift"></i>添加评教</div>
                                
                            </div>
                            <div class="portlet-body form">
                                <!-- BEGIN FORM-->
                                <form id="saveBusinessForm" class="form-horizontal" method="post"  enctype="multipart/form-data">
                                  <div class="form-body">
                                    <h4 class="form-section">基本信息</h4>
                                        <div class="form-group margi_top">
                                            <label class="col-md-3 control-label">名称<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="eval_name" id="eval_name" value="<?php echo $evalInfo['eval_name']?>">
                                                <span class="help-block">名称，用于识别不同的评教信息</span>
                                                <span class="help-block" id="onlyone" style='color:red;'></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">评教时间<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="eval_starttime" id="eval_starttime" value="<?php echo $evalInfo['eval_starttime']?>">－－
                                                <input type="text" class="form-control" name="eval_endtime" id="eval_endtime" value="<?php echo $evalInfo['eval_endtime']?>">
                                                <span class="help-block">时间范围</span>
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">类型<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                            	<select class="form-control" id="eval_type" name="eval_type">
													<?php foreach ($evalType as $key=>$val){?>
														<option value="<?=$key ?>" <?php if($evalInfo['eval_type']==$key) echo 'selected="selected"';?>><?=$val;?></option>
													<?php }?>
												</select>
                                                <span class="help-block">类型</span>
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">状态<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">                                            	
                                            	<select class="form-control" id="eval_status" name="eval_status">
													<?php foreach ($evalStatus as $key=>$val){?>
														<option value="<?=$key ?>" <?php if($evalInfo['eval_status']==$key) echo 'selected="selected"';?>><?=$val;?></option>
													<?php }?>
												</select>
                                                <span class="help-block">状态</span>
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">答题数量<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="eval_max_topic" id="eval_max_topic" value="<?php echo $evalInfo['eval_status']; ?>">
                                                <span class="help-block">0代表答完所有题</span>
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">描述<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                            	<textarea rows="5" cols="" class="form-control" name="eval_descript" id="eval_descript"><?php echo $evalInfo['eval_descript']; ?></textarea>
                                                <span class="help-block">描述信息</span>
                                            </div>
                                        </div>
                                                                             
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                            	<input type="hidden" id="eval_id" name="eval_id" value="<?php echo $evalInfo['eval_id'];?>" />
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
$("#eval_starttime").datepicker({
    todayBtn: true,
    pickerPosition: "bottom-left"
}).on("click",function(ev){
    $("#eval_starttime").datepicker("setEndDate", $("#eval_endtime").val());
});
$("#eval_endtime").datepicker({
    todayBtn: true,
    pickerPosition: "bottom-left"
}).on("click", function (ev) {
    $("#eval_endtime").datepicker("setStartDate", $("#eval_starttime").val());
});


$(function () {
	var doAjax = false;
	$("#submit").click(function(){
		var eval_id = $.trim($('#eval_id').val());
		var eval_name = $.trim($('#eval_name').val());
		var eval_descript = $.trim($('#eval_descript').val());
		var eval_starttime = $.trim($('#eval_starttime').val());
		var eval_endtime = $.trim($('#eval_endtime').val());
		var eval_type = $.trim($('#eval_type').val());
		var eval_status = $.trim($('#eval_status').val());
		var eval_max_topic = $('#eval_max_topic').val();	

		if (eval_name == '') {
			Common.alert('请输入名称');
			return false;
		} else if(eval_starttime == ''){
			Common.alert('请选择开始时间');
			return false;
		}else if(eval_endtime == ''){
			Common.alert('请选择结束时间');
			return false;
		}


		var setUrl = "<?php echo url('ZgykdxEvaluating', 'ajax_updateEvaluating');?>";
		var jumpHref = "<?php echo url('ZgykdxEvaluating', 'index');?>";
		var params = {
				eval_id : eval_id,
				eval_name : eval_name,
				eval_descript : eval_descript,
				eval_starttime : eval_starttime,
				eval_endtime : eval_endtime,
				eval_type : eval_type,
				eval_status : eval_status,
				eval_max_topic : eval_max_topic
		};
		if(doAjax == true){
			return;
		}
		doAjax == true;
		Common.request(setUrl, params, 
				function(result,status){
					doAjax = false;
					Common.alert("操作成功",function(){
						window.location.href = jumpHref;
					});
				});
		});
});

function get_back(){

    history.go(-1);
}

</script>

