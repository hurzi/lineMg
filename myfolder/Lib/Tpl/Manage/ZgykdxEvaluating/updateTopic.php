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
                                    <i class="fa fa-gift"></i>修改题目</div>
                                
                            </div>
                            <div class="portlet-body form">
                                <!-- BEGIN FORM-->
                                <form id="saveBusinessForm" class="form-horizontal" method="post"  enctype="multipart/form-data">
                                  <div class="form-body">
                                    <h4 class="form-section">基本信息</h4>
                                    	<div class="form-group margi_top">
                                            <label class="col-md-3 control-label">类型<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                            	<select class="form-control" id="topic_type" name="topic_type">
													<?php foreach ($evalTopicType as $key=>$val){?>
														<option value="<?=$key ?>" <?php if($topicInfo['topic_type']==$key) echo 'selected="selected"'; ?>><?=$val;?></option>
													<?php }?>
												</select>
                                                <span class="help-block">类型</span>
                                            </div>
                                        </div>                                         
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">题目<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="topic_title" id="topic_title" value="<?php echo $topicInfo['topic_title'];?>">
                                                <span class="help-block">题目，50字以内</span>
                                                <span class="help-block" id="onlyone" style='color:red;'></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">题示<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="topic_tip" id="topic_tip" value="<?php echo $topicInfo['topic_tip'];?>">
                                                <span class="help-block">提示，用于提示用户操作</span>
                                                <span class="help-block" id="onlyone" style='color:red;'></span>
                                            </div>
                                        </div>
                                        <div class="form-group pointBoxDiv">
                                            <label class="col-md-3 control-label">分数</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="topic_point" id="topic_point" value="<?php echo $topicInfo['topic_point'];?>">
                                                <span class="help-block">用户打分，此项最高不超过上述分数，最大不能超过100</span>
                                            </div>
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">题目描述</label>
                                            <div class="col-md-4">
                                            	<script type="text/plain" id="myEditor" style="width:600px;height:240px;"><?php echo $topicInfo['topic_content'];?></script>
                                            	<span class="help-block">选填,题目描述</span>
                                            </div>
                                        </div>
                                        <div class="itemBoxDiv">
                                        <h4 class="form-section">选项信息</h4>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"></label>
                                            <div class="col-md-4" style="width:600px;">                                        	
                                        	<div class="form-group pull-right">
                                    			<a class="btn blue dropdown-toggle" href="javascript:addItem()" > 添加选项 </a> 
                               				</div>
	                                        <table class="table table-striped table-bordered table-hover" id="item_table">
						                		<thead>
								                    <tr>
								                        <th>序号</th>
								                        <th>选项(A/B)</th>
		                                                <th>选项值</th>
								                        <th>分数</th>
								                        <th style="width: 50px">操作</th>
								                    </tr>
							                    </thead>
							                    <tbody>      
							                    	<?php if(!$topicInfo['items']){?>                                     
								                    <tr class="odd gradeX" >
								                        <td><input type="text" class="form-control" name="item_order" id="item_order" value="1"></td>
								                        <td><input type="text" class="form-control" name="item_key" id="item_key" value="A"></td>
								                        <td><input type="text" class="form-control" name="item_name" id="item_name" value=""></td>
								                        <td><input type="text" class="form-control" name="item_point" id="item_point" value=""></td>
								                        <td><a href="javascript:void(0)" target="_blank" class="_delItem">删除</a> 
								                        </td>
								                    </tr>                                           
								                    <tr class="odd gradeX" >
								                        <td><input type="text" class="form-control" name="item_order" id="item_order" value="2"></td>
								                        <td><input type="text" class="form-control" name="item_key" id="item_key" value="B"></td>
								                        <td><input type="text" class="form-control" name="item_name" id="item_name" value=""></td>
								                        <td><input type="text" class="form-control" name="item_point" id="item_point" value=""></td>
								                        <td><a href="javascript:void(0)" target="_blank" class="_delItem">删除</a> 
								                        </td>
								                    </tr>                                           
								                    <tr class="odd gradeX" >
								                        <td><input type="text" class="form-control" name="item_order" id="item_order" value="3"></td>
								                        <td><input type="text" class="form-control" name="item_key" id="item_key" value="C"></td>
								                        <td><input type="text" class="form-control" name="item_name" id="item_name" value=""></td>
								                        <td><input type="text" class="form-control" name="item_point" id="item_point" value=""></td>
								                        <td><a href="javascript:void(0)" target="_blank" class="_delItem">删除</a> 
								                        </td>
								                    </tr>                                           
								                    <tr class="odd gradeX" >
								                        <td><input type="text" class="form-control" name="item_order" id="item_order" value="4"></td>
								                        <td><input type="text" class="form-control" name="item_key" id="item_key" value="D"></td>
								                        <td><input type="text" class="form-control" name="item_name" id="item_name" value=""></td>
								                        <td><input type="text" class="form-control" name="item_point" id="item_point" value=""></td>
								                        <td><a href="javascript:void(0)" target="_blank" class="_delItem">删除</a> 
								                        </td>
								                    </tr>
								                    <?php }else{
								                    	foreach($topicInfo['items'] as $key=>$v){
								                    ?>								                                                                 
								                    <tr class="odd gradeX" >
								                        <td><input type="text" class="form-control" name="item_order" id="item_order" value="<?php echo $v['item_order'];?>"></td>
								                        <td><input type="text" class="form-control" name="item_key" id="item_key" value="<?php echo $v['item_key'];?>"></td>
								                        <td><input type="text" class="form-control" name="item_name" id="item_name" value="<?php echo $v['item_name'];?>"></td>
								                        <td><input type="text" class="form-control" name="item_point" id="item_point" value="<?php echo $v['item_point'];?>"></td>
								                        <td><a href="javascript:void(0)" target="_blank" class="_delItem">删除</a> 
								                        </td>
								                    </tr>
								                    <?php }}?>
							                    </tbody>
											</table>
											</div>
											</div>
                                        </div>
                                        
                                   
                                                                             
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                            	<input type="hidden" id="topic_id" name="topic_id" value="<?php echo $topicInfo['topic_id'];?>"/>
                                               	<input type="hidden" id="eval_id" name="eval_id" value="<?php echo $evalInfo['eval_id'];?>"/>
                                                <input type="hidden" id="eval_type" name="eval_type" value="<?php echo $evalInfo['eval_type'];?>"/>
                                                <button type="button" id="submit" class="btn blue">提交</button>
                                                <button type="button" onclick='history.go(-1)' class="btn">取消</button>
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
var um = UM.getEditor('myEditor');

$(function () {
	var doAjax = false;
	//删除选 项
	$(document).delegate('._delItem', 'click',function(){
		if($('#item_table tbody').children().length<=1){
			Common.alert("至少保留一个选项");
			return;
		}
		$(this).parent().parent().remove();
	});

	//修改类型时自动识别
	$("#topic_type").change(function(){
		typeChange();
	});
	
	
	$("#submit").click(function(){
		var topic_id = $('#topic_id').val();
		var eval_id = $('#eval_id').val();
		var eval_type = $('#eval_type').val();
		var topic_type = $('#topic_type').val();
		var topic_point = $('#topic_point').val();
		var topic_title = $.trim($('#topic_title').val());
		var topic_tip = $('#topic_tip').val();
		var topic_content = $.trim(UM.getEditor('myEditor').getContent());	
		var items = '';
		
		if (topic_title == '') {
			Common.alert('请输入题目');
			return false;
		} else if(topic_type == ''){
			Common.alert('请选择类型');
			return false;
		}else if((topic_type == 3 || topic_type==4) && !topic_point){
			Common.alert('请输入分数');
			return false;
		}

		//检测题项
		if(topic_type == 1 || topic_type==2 || topic_type==4){
			items = getItems();
			for(var i=0,l=items.length;i<l;i++){
				var j = 0;
				for(var key in items[i]){
					 if(key == 'item_point'){
						 if(eval_type!=2 && eval_type!=3){
							 j++;
							 continue;
						 }
			    	 }
			    	 if(!items[i][key]){
						 var columnName = $("#item_table thead tr th").eq(j).html();
				    	 errMsg = "选项第["+(i+1)+"]行["+(j+1)+"]列["+columnName+"]不能为空";
				    	 Common.Alert(errMsg);
				    	 return false;
					 }
					j++;
				}
			}
		}

		if((eval_type==2 || eval_type==3) && topic_type != 3){
			topic_point = getMaxPoint(items);
		}
		

		var setUrl = "<?php echo url('ZgykdxEvaluating', 'ajax_updateEvaluatingTopic');?>";
		var jumpHref = "<?php echo url('ZgykdxEvaluating', 'indexTopicList',array("eval_id"=>$evalInfo['eval_id']));?>";
		var params = {
				topic_id : topic_id,
				eval_id : eval_id,
				topic_type : topic_type,
				topic_point : topic_point,
				topic_title : topic_title,
				topic_tip : topic_tip,
				topic_content : topic_content,
				items : items
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
		//初始化类型再来的ui影响
		typeChange();
});

function addItem(){	
	var tr = $("#item_table tbody tr").eq(0).clone();
	tr.appendTo("#item_table tbody");
}

function typeChange(){
	var topic_type = $('#topic_type').val();
	if(topic_type == 1 || topic_type==2 || topic_type==4){
		$(".itemBoxDiv").show();
	}else{
		$(".itemBoxDiv").hide();
	}
	if(topic_type==3 || topic_type==4){
		$(".pointBoxDiv").show();
		$("#item_table thead tr th").eq(3).show();
		$("#item_table tbody").children().each(function(i,n){
			$(n).find("td").eq(3).show();
		});
			
	}else{
		$(".pointBoxDiv").hide();
		$("#item_table thead tr th").eq(3).hide();
		$("#item_table tbody").children().each(function(i,n){
			$(n).find("td").eq(3).hide();
		});
	}
}

/**
 * 获取数据
 */
function getItems(){
	var items = [];
	$("#item_table tbody").children().each(function(i,n){
	     var subParam = {};
	     $(n).find("input").each(function(j,sn){
	    	 var subobj = $(sn);
	    	 subParam[subobj.attr("name")] = subobj.val();
	     });
	     items[i] = subParam;
	});
	return items;
}


/**
 * 获得最大的分数
 */
function getMaxPoint(items){
	var max_point = 0;
	for(var i=0,l=items.length;i<l;i++){
		if(items[i]['item_point']>max_point){
			max_point = items[i]['item_point'];
		}
	}
	return max_point;
}

</script>

