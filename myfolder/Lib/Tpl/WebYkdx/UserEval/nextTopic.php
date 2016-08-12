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
				<p class="tex_wei font-we"><span id="currTopicNum">1</span>/<span id="maxTopicNum">10</span></p>
				<p class="tex_wei1 gra1_color_size font-size12">答题进度</p>
			</div>
			<div class="boxw1_2" style="border: none;">
				<p class="tex_wei"><span id="currSumPoint">34</span></p>
				<p class="tex_wei1 gra1_color_size font-size12">当前评分</p>			
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="use_main mar_top10">
        <div class="dl_box" onclick="window.location.href = '<?=url('StoreClerk','myMateriel')?>'">
			<dl>
				<dd>
					<h2 id="titleDiv">[单选题]李白笔下的“飞流直下三千尺,疑是银河落九天”指的是哪个风景区?</h2>
					<div class="gra2_color_size font-we" style="font-size: 12px;" id="descDiv">这里是图片的描述</div>
				</dd>
			</dl>
		</div>
        <div class="dl_box mar_top10">
			<dl>
				<dd id="itemsDiv">
					<div name="A" class="item_div">
					A 庐山					
					</div>
					<div name="B" class="item_div">
					B 香山					
					</div>
					<div name="C" class="item_div">
					C 华山					
					</div>
					<div name="D" class="item_div">
					D 恒山					
					</div>
					
				</dd>
			</dl>
		</div>        
	</div>
</div>
    <div style="width: 100%;height: 50px"></div>
<div class="burst-main abs_bott" style="padding: 0 10px;">
	<div class="button_wrap">
		<div class="boxw1_2" style="border: none;">
			<button class="submit_btn gray_bj pad50 borde-rad white_color_size" id="preBtn">上一题</button>			
		</div>
		<div class="boxw1_2" style="border: none;">
			<button class="submit_btn gray_bj pad50 borde-rad white_color_size" id="nextBtn">下一题</button>			
		</div>
		<div class="clear"></div>
	</div>
</div>

<script type="text/javascript">
$(function(){
	
});

$(document).on('click', '#confirm', function () {
	var store_id = $('input[name="optionsRadios"]:checked ').val();
        var store_name = $('input[name="optionsRadios"]:checked ').attr("store_name");
	if (!store_id||store_id=='') {
		alert('请至少选择一个门店');
	} else {
		if(apidata == null || apidata == undefined){
			var opener = api.opener;
			if((typeof opener.pushStoreList) == "function"){
				opener.pushStoreList(store_id,store_name);
			}
		}else{
			if(apidata.ok_callback) {
				apidata.ok_callback.call(null,store_id,store_name);
				api.close();
			}
		}
		//页面增加选中的门店列表
		//parent.pushStoreList(store_id);
	}
})

 function loadingMore(){

        var _max_store_id = $("#max_store_id").val();

        if(_max_store_id==0){
            $("#more").html("没有更多的数据了。");
            return false;
        }
        var _work_id = "<?=$work_id?>";
        var url = "<?=url('Store','getMoreStore')?>";
         Common.loading({type:'on',title:'处理中请稍后...'});
        $.getJSON(url,{max_store_id:_max_store_id,work_id:_work_id},function(data){
            Common.loading({type:'off'});
            if(data.error == 0){
                var list = data.data.list;
                var max_store_id = data.data.max_store_id;
                var html = "";
                for(var i=0; i<list.length; i++){
                    html += '<div class="cx-box1">'+
			    		'<div class="cx_left">'+
			    			'<span class="img_t wid_10 mar_top20">'+
			    				'<div class="cheb_l">'+
				    				'<span class="ch" data="'+list[i].encode_store_id+'" ></span>'+
				    				'<div class="clear"></div>'+
				    			'</div>'+
			    			'</span>'+
			    			'<div class="text_w">'+
			    				'<span class="tex mar_top5 gra2_color_size">'+list[i].store_name+'</span>'+
                                                        
			    			'</div>'+
			    			
			    		'</div>'+
			    		
			    		'<div class="clear"></div>'+
			    	'</div>'
                }
                $(".cuxy_box").append(html);
                $("#max_store_id").val(max_store_id);
                $("#more").html("");
            }else{
                $("#more").html(data.msg);
            }
        })
    };
</script>
<?php tpl('Common.foot')?>