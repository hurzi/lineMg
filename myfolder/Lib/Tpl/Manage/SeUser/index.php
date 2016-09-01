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
                        <a href="<?=url('ZgykdxEvaluating','index')?>">管理员管理</a>
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
                                <i class="fa fa-gift"></i>管理员管理
                            </div>
                        </div>
                        <div class="portlet-body">
                            <form method="get" action="<?=url('SeUser','index')?>">
                                <div class="form-group fl pull-left">
                                	<input class="form-control input-small" name="keyword" type="text" value="<?php echo $keyword;?>" class="txt" id="keyword"  placeholder="管理员姓名" />
                                </div>
                                <div class="form-group fl pull-left">
                                	<input class="form-control input-small" name="ent_name" type="text" value="<?php echo @$entName;?>" class="txt" id="ent_name"  placeholder="企业名称" />
                                </div>
                                <div class="form-group fl pull-left">
                                    <select name="level" class="form-control input-small">
                                        <option value="0">请选择用户类型</option>
					                    <?php if($levelList){
					                        foreach ($levelList as $k=>$v){
												if(UHome::getUserLevel()!=1 && $k == 1){
													continue;
												}
					                            ?>
					                            <option value="<?php echo $k;?>"><?php echo $v;?></option>
					                        <?php }}?>
                                    </select>
                                </div>
                                <input type="hidden" name="a" value="SeUser" />
                                <input type="hidden" name="m" value="index" />
                                <input class="btn submit blue marg_box" type="submit" value="查询" />
                                <div class="btn-group pull-right">
                                    <a class="btn blue dropdown-toggle" href="<?=url('SeUser','add');?>" > 添加管理员 </a>  <!-- data-toggle="dropdown" -->
                                </div>
                            </form>
                            <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover" id="sample_3">
                            <thead>
                            <tr>
                                <th style="text-align:center; width:60px;">
					                <input name="checkbox" type="checkbox" class="checkbox" onClick="selAll(this)" value="选择全部">ID
					            </th>
								<th >登陆账号</th>
								<th >姓名</th>
								<th>所属企业</th>
								<th>创建日期</th>
								<th >用户类型</th>
								<th style="text-align:center;">操作</th>		
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(count($list)){
                                foreach ($list as $key => $vo) {
                            ?>
                            <tr class="odd gradeX">
                            	<td >
					                <input name="ids[]" type="checkbox" id="ids[]" value="<?php echo $vo['user_id'];?>" class="checkbox" <?php if($vo['level']==1){ echo "disabled"; } ?>>
					                <?php echo $vo['user_id'];?>
								</td>
								<td><?php echo $vo['username'];?></td>
								<td><?php echo $vo['nickname'];?></td>
					            <?php if($vo['level']!=1): ?>
								<td><?php echo $ent_name = isset($entList[$vo['ent_id']]) ? $entList[$vo['ent_id']] : 'ID:'.$vo['ent_id'];?></td>
								<?php else : ?>
					            <td><span style="color: red;">[Adsit]</span></td>
					            <?php endif; ?>
					            <td><?=date('Y-m-d H:i:s',$vo['create_time']);?></td>
								<th><?php echo $levelList[$vo['level']];?></th>
								<td style="text-align:center;">
									<a href="javascript:;" name="<?php echo $vo['user_id'];?>"  class="edit">
										<button class="btn default btn-xs purple"><i class="fa fa-edit"></i>编辑</button>
									</a>
								</td>
                            </tr>
                            <?php }}else{ ?>
                            <tr  class="odd gradeX"><td colspan="9">暂无数据</td><tr>
                            <?php } ?>
                            </tbody>
                        </table>
                       </div>
                       
                       <div>
	                         <div>
	                            	<input class="btn submit blue marg_box" type="button" value="批量删除" />
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
$(function () {
	//批量删除
	$('#batch-delete').click(function () {
		var url	= "<?php echo url('EntSuperAdmin','delete');?>";
		var ids = [];
		$("input[name='ids[]']:checkbox:checked").each(function (){
			ids.push($(this).val());
		});
		delete_operate(url,ids);

	});
	$('.edit').click(function(){
		var name = encodeURIComponent($(this).attr('name'));
			window.location.href = "<?php echo url('SeUser', 'edit');?>&id="+name;
	});
});
</script>
