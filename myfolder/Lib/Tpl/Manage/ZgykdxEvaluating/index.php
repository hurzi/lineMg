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

            <!-- 表格开始 -->
        <div class="row">
                <div class="col-md-12">
                    <div class="portlet box grey-cascade">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-gift"></i>商户管理
                            </div>
                        </div>
                        <div class="portlet-body">
                            <form method="get" action="<?=url('ZgykdxEvaluating','index')?>">
                                <div class="form-group fl pull-left">
                                    <input class="form-control input-small" value="<?=$param['eval_name'];?>" name='eval_name'  placeholder="名称"/>
                                </div>
                                <div class="form-group fl pull-left">
                                    <select name='status' class="form-control input-small">
                                        <option value="" >请选择类型</option>
                                        <?php foreach (@$evalType as $key => $value) { ?>
                                            <option value="<?=$key?>" <?php if($param['eval_type']==$key){ echo 'selected="selected"'; } ?> ><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <input type="hidden" name="a" value="ZgykdxEvaluating" />
                                <input type="hidden" name="m" value="index" />
                                <input class="btn submit blue marg_box" type="submit" value="查询" />
                                <div class="btn-group pull-right">
                                    <a class="btn blue dropdown-toggle" href="<?=url('ZgykdxEvaluating','add');?>" > 添加评教 </a>  <!-- data-toggle="dropdown" -->
                                </div>
                            </form>
                            <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover" id="sample_3">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>名称</th>
                                <th>开始时间</th>
                                <th>结束时间</th>
                                <th>类型</th>
                                <th>题库数</th>
                                <th>状态</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(count($list)){
                                foreach ($list as $key => $vo) {
                            ?>
                            <tr class="odd gradeX">
                            	<td height="25"><?php echo $vo['eval_id']?></td>
								<td><?php echo $vo['eval_name']?></td>
								<td><?php echo $vo['eval_starttime']?></td>
								<td><?php echo $vo['eval_endtime']?></td>
								<td><?php echo $evalType[$vo['eval_type']];?></td>
								<td><?php echo $vo['topic_count']?></td>
								<td><?php echo $evalStatus[$vo['eval_status']]?></td>
								<td><?php echo $vo['create_time']?></td>
								<td>
                                    <a href="<?=url('ZgykdxEvaluating','update', array('eval_id'=>$vo['eval_id']))?>"><button class="btn default btn-xs purple"><i class="fa fa-edit"></i>编辑</button></a>
                                    <a href="<?=url('ZgykdxEvaluating','indexTopicList', array('eval_id'=>$vo['eval_id']))?>"><button class="btn default btn-xs purple"><i class="fa fa-edit"></i>管理题库</button></a>
                                </td>
                            </tr>
                            <?php }}else{ ?>
                            <tr  class="odd gradeX"><td colspan="9">暂无数据</td><tr>
                            <?php } ?>
                            </tbody>
                        </table>
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
function delBusiness (obj) {
    Common.confirm('您是否删除此商户吗？', function () {
        var param = {
            business_id: encodeURIComponent($(obj).attr('data'))
        };
        //loadMack({off:'on',text:"处理中请稍后..."});
        $.ajax({
            url : '<?php echo url('Enterprise', 'delAjax')?>',
            type : 'post',
            data : param,
            dataType : 'json',
            error : function(){
                //loadMack({off:'off'});
                Common.alert('网络异常请重试');
            },
            success : function(res){
                //loadMack({off:'off'});
                if(0 === Number(res.error)){
                    Common.alert('操作成功', function () {window.location.reload();});
                }else{
                    Common.alert(res.msg);
                }
            }
        });
    });
}
</script>








