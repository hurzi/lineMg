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
                        <a href="javascript:;">商户管理</a>
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
                                <i class="fa fa-gift"></i>试题管理
                            </div>
                        </div>
                        <div class="portlet-body">    
                        	<form method="get" action="<?=url('ZgykdxEvaluating','index')?>">
                                <div class="form-group pull-right">
                                    <a class="btn blue dropdown-toggle" href="<?=url('ZgykdxEvaluating','addTopic',array("eval_id"=>$evalInfo['eval_id']));?>" > 添加题目 </a>  <!-- data-toggle="dropdown" -->
                                </div>
                            </form>                        
                            <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover" id="sample_3">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>题型</th>
                                <th>题干</th>
                                <th>分数</th>
                                <th>创建时间</th>
                                <th>更新时间</th>
                                <th>选项数</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(count($list)){
                                foreach ($list as $key => $vo) {
                            ?>
                            <tr class="odd gradeX">
                            	<td height="25"><?php echo $vo['topic_id']?></td>
								<td><?php echo $evalTopicType[$vo['topic_type']]?></td>
								<td><?php echo $vo['topic_title']?></td>
								<td><?php echo $vo['topic_point']?></td>
								<td><?php echo $vo['create_time']?></td>
								<td><?php echo $vo['last_update_time']?></td>
								<td><?php echo $vo['item_count']?></td>
								<td>
                                    <a href="<?=url('ZgykdxEvaluating','updateTopic', array('topic_id'=>$vo['topic_id'],"eval_id"=>$vo['eval_id']))?>"><button class="btn default btn-xs purple"><i class="fa fa-edit"></i>编辑</button></a>
                                    <a href="javascript:delTopic(<?php echo $vo['topic_id']?>)"><button class="btn default btn-xs purple" ><i class="fa fa-edit"></i>删除</button></a>
                                </td>
                            </tr>
                            <?php }}else{ ?>
                            <tr  class="odd gradeX"><td colspan="9">暂无数据</td><tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        </div>
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
function delTopic (topicid) {
    Common.confirm('您是否删除此此题吗？', function () {
        var params = {
            topic_id: topicid
        };
        var setUrl = "<?php echo url('ZgykdxEvaluating', 'ajax_deleteEvaluatingTopic');?>";
		var jumpHref = "<?php echo url('ZgykdxEvaluating', 'indexTopicList',array("eval_id"=>$evalInfo['eval_id']));?>";
		Common.request(setUrl, params, 
				function(result,status){
					doAjax = false;
					Common.alert("操作成功",function(){
						window.location.href = jumpHref;
					});
		});
	});
}
</script>








