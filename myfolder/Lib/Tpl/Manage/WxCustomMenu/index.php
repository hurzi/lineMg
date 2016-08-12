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

            <!-- 表格开始 -->
        <div class="row">
                <div class="col-md-12">
                    <div class="portlet box grey-cascade">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-gift"></i>菜单管理
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="form-group ">
                                    <a class="btn blue dropdown-toggle" href="<?php echo url('WxCustomMenu', 'add',array("is_thread"=>$isThread));?>" > 增加菜单 </a>  <!-- data-toggle="dropdown" -->
                                    <a class="btn blue dropdown-toggle" href="javascript:synchronousMenu()" > 同步菜单到微信 </a>  <!-- data-toggle="dropdown" -->
                            </div>
                            <div class="form-group ">
							菜单最近一次同步到微信时间：<?php echo $lastSynchronousTime;?> &nbsp;&nbsp;菜单最近一次更新时间：<?php echo $lastUpdateTime;?>
							</div>
                            <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover" id="sample_3">
                            <thead>
                            <tr>
                                <th>菜单名称</th>
								<th>排序值</th>
								<th>内容类型</th>
								<th>消息内容</th>
								<th width="150">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
							if ($list) {
								foreach ($list as $vo) {
									?>
                            <tr class="odd gradeX">                            
								<td class="text-left"><?php echo $vo['name'];?></td>
								<td><?php echo $vo['order'];?></td>
								<td><?php
									if(isset($vo['children'])){
										echo '---';
									}else{
										echo $vo['type_name'];
									}
									?>
								</td>
								<td>
								<div class="break" style="width:300px;">
									<?php
									if(isset($vo['children'])){
										echo '---';
									}else{
										echo $vo['html'];
									}
									?>
									</div>
								</td>
								<td>
                                	<a class="edit" name="<?php echo $vo['id'];?>" href="javascript:;"><button class="btn default btn-xs purple"><i class="fa fa-edit"></i>编辑</button></a>
                                    <a class="del" name="<?php echo $vo['id'];?>" href="javascript:;"><button class="btn default btn-xs purple"><i class="fa fa-edit"></i>删除</button></a>
                                </td>
                            </tr>
                            <?php
							if (isset($vo['children']) && ! empty($vo['children'])) {
								foreach ($vo['children'] as $v) {
									?>
							<tr>
								<td class="text-left"><span>----</span><?php echo $v['name'];?></td>
								<td><?php echo $v['order'];?></td>
								<td><?php echo $v['type_name'];?></td>
								<td>
									<div class="break" style="width:300px;"><?php echo $v['html'];?></div>
								</td>
								<td>
                                	<a class="edit" name="<?php echo $v['id'];?>" href="javascript:;"><button class="btn default btn-xs purple"><i class="fa fa-edit"></i>编辑</button></a>
                                    <a class="del" name="<?php echo $v['id'];?>" href="javascript:;"><button class="btn default btn-xs purple"><i class="fa fa-edit"></i>删除</button></a>
                                </td>
							</tr>
							<?php }}?>
                            
                            
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
$(function(){
		$('.edit').click(function(){
			var id = $(this).attr('name');
			window.location.href = "<?php echo url('WxCustomMenu', 'edit') .'&is_thread=' . $isThread;?>&id="+id;
		});
		//单个删除
		$('.del').click(function(){
			var id = $(this).attr('name');
			id =[id];
			var url = '<?php echo url('WxCustomMenu', 'delete');?>';
		removeOne(url, id);
	});
});
var synchronousWxUrl = '<?php echo url('WxCustomMenu', 'synchronousMenu').'&is_thread=' . $isThread;?>';
var clearWxUrl = '<?php echo url('WxCustomMenu', 'clearWxMenu').'&is_thread=' . $isThread;?>';

var msgList = <?php echo $msgList;?> || {};
$("a.more").on('click',function(){
	var id = $(this).attr('name');
	showMessage(msgList[id]['type'], msgList[id]['data']);
});
</script>


