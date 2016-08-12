<?php tpl('Common.header')?>
<body>
<div id="wrap">
	<div class="user_header">
		<dl>
			<dt></dt>
			<dd>
				<h2>五一调查问券</h2>
				<h2>时间：2016-04-10至2016-05-01</h2>
			</dd>
		</dl>
	</div>
	<div class="use_main">
		<div class="use_m_2 gray-color-border-top">
			<div class="boxw1_2 gray-color-border"  onclick="window.location.href = '<?=url('Income','index')?>'">
				<p class="tex_wei font-we">1/10</p>
				<p class="tex_wei1 gra1_color_size font-size12">答题进度</p>
			</div>
			<div class="boxw1_2" style="border: none;">
				<p class="tex_wei">34</p>
				<p class="tex_wei1 gra1_color_size font-size12">当前评分</p>			
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
    <div style="width: 100%;height: 50px"></div>
<div class="burst-main abs_bott" style="padding: 0 10px;">
	<div class="button_wrap">
		<div class="" style="border: none;">
			<input type="hidden" id="eval_id" name="eval_id" value="<?php echo $evalInfo['eval_id'];?>" />
			<button class="submit_btn gray_bj pad50 borde-rad white_color_size">确认答题</button>			
		</div>
		<div class="clear"></div>
	</div>
</div>

<script type="text/javascript">
$(function(){
	$(".submit_btn").click(function(){
		var url = '<?php echo url("UserEval","ajax_joinEval");?>';
		var href = '<?php echo url("UserEval","nextTopic",array("eval_id"=>$evalInfo['eval_id']));?>';
		var eval_id = $("#eval_id").val();
		if(!eval_id){
			Common.alert("参数错误[未知的评教]");
			return;
		}
		var param = {
			eval_id : eval_id
		}
		Common.request(url,param,function(result){
			var ue_id = result.data.ue_id;
			window.location.href=href+"&ue_id="+ue_id;
		});
	});
});

</script>
<?php tpl('Common.foot')?>