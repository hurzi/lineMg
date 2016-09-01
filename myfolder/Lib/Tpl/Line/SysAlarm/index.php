<?php tpl('Common.header_1');?>
<!-- 搜索 -->
<div class="con_c_c">
<div class="con_bzk">
		<div style="padding: 10px;">
	
<form method="get" name="searchform" action="<?php echo url('SysAlarm', 'index');?>" id="search-form">
    <table class="fixwidth search-bar">
        <tr>
            <td>
                 等级:
                <select id="level" name="level">
                <option value="">选择等级</option>
                <option value="1" <?php if($level == 1) echo 'selected="selected"'?>>非常严重</option>
                <option value="2" <?php if($level == 2) echo 'selected="selected"'?>>较严重</option>
                <option value="3" <?php if($level == 3) echo 'selected="selected"'?>>严重</option>
                <option value="4" <?php if($level == 4) echo 'selected="selected"'?>>一搬</option>
                <option value="5" <?php if($level == 5) echo 'selected="selected"'?>>提示</option>
                </select>
                系统:
                <input name="system_name" type="text" value="<?php echo $system_name;?>" class="txt" id="system_name" />
                 子模块:
                <input name="alarm_name" type="text" value="<?php echo $alarm_name;?>" class="txt" id="alarm_name" />
                 时间:
                <input name="start_time" type="text" value="<?php echo $start_time;?>" class="txt" id="start_time" />--
                <input name="end_time" type="text" value="<?php echo $end_time;?>" class="txt" id="end_time" />
      	机器处理:
                <select id="auto_check" name="auto_check">
                <option value="">请选择</option>
                <option value="0" <?php if($auto_check === 0) echo 'selected="selected"'?>>未处理</option>
                <option value="1" <?php if($auto_check == 1) echo 'selected="selected"'?>>已处理</option>
                </select>
         	人工处理:
                <select id="manual_status" name="manual_status">
                <option value="">请选择</option>
                <option value="0" <?php if($manual_status === 0) echo 'selected="selected"'?>>未处理</option>
                <option value="1" <?php if($manual_status == 1) echo 'selected="selected"'?>>已处理</option>
                </select>
                  &nbsp;&nbsp;&nbsp;
                <input name="a" type="hidden" value="<?php echo __ACTION_NAME__;?>" />
                <input name="m" type="hidden" value="<?php echo __ACTION_METHOD__;?>" />
                <input id="search-button" type="button" value="搜索" class="button green medium addFz" style="height:21px;"/>
            </td>
        </tr>
    </table>
</form>
</div>
</div>
</div>
<!-- 搜出 end -->

<div class="con_c_c">
<table class="tab_con" cellpadding="0" cellspacing="0">
    <tr>
        <th>发生时间</th>
        <th>系统</th>
        <th>警告名称</th>
        <th>描述</th>
        <th>action</th>
        <th>方法</th>
        <th>操作</th>
    </tr>
<?php if ($list) : ?>
    <?php foreach ($list as $vo) : ?>
    <tr>
        <td><?= date("Y-m-d H:i:s",$vo['create_time']);?></td>
        <td><?=$vo['system_name'];?></td>
        <td><?=$vo['alarm_name'];?></td>
        <td><?=$vo['alarm_desc'];?></td>
        <td><?=$vo['action_name'];?></td>
        <td><?=$vo['method_name'];?></td>
        <td>
        <a href="<?php echo url("SysAlarm","showLog",array("id"=>$vo['id']));?>">查看详情</a>
        <a href="javascript:handling(<?php echo $vo['id'];?>,<?php echo $vo['manual_status'];?>)">处理结果</a>
        </td>
    </tr>
    <?php endforeach;?>
    <tr class="nobg">
        <td colspan="7" class="tdpage"><div class="tab_foot" style="padding:0 20px;"><?php echo $page;?></div></td>
    </tr>
<?php else:?>
    <tr>
        <td colspan="7" align="center">----无内容----</td>
    </tr>
<?php endif;?>
</table><br />
</div>
<script type="text/javascript">
    $(function () {
        //搜索
        $('#search-button').click(function (){
            $('#search-form').submit();
        });
    });
</script>
<?php tpl("Common.footer")?>