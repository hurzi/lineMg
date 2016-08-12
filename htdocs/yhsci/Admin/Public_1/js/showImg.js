//图文模版
function tw(){
	this.ImgTpl = function(title,description,url,img_url){ 
		if(url.indexOf("http://")<0){
			url = 'http://'+url;
		}
		var str = '<div  class="ImgWord"><h1>'+title+'</h1><div style="position: relative;">'
				+ '<a href="'+url+'"  target="_blank"><div class="IWbg" style="background-image:url('+img_url+');"></div></a><p>'+description+'</p></div></div>';
		return str;
	}
}


function showTW(){
	//显示填充视图
	this.show = function(showTpl){
		$('#showWt').html(showTpl);
	}
	this.run = function(tw_array,ms_id){
			var tt = new tw;
			var data_num = tw_array[ms_id].length;
			if(typeof(data_num)=='undefined'){
				alert('error id');
				return;
			}
			var data = tw_array[ms_id];
			var showTpl = '';
			for(var i=0;i<data.length;i++){
				//showTpl += '<tr><td width="300" align="center" colspan="2">图文信息'+(i+1)+'</td></tr>';
				showTpl += tt.ImgTpl(data[i]['title'],data[i]['description'],data[i]['url'],data[i]['picurl']);		
			}
			var con = '<div id = "showWt" style="background:url(\'/Admin/Public/images/pattern.png\') repeat scroll 0 6px #F4F6F8; height:290px;overflow:auto;" ></div>';	
			var wx = new jsbox({
						 onlyid:'tw_'+ms_id,
						 title :'图文信息',
						 conw : 600,
						 //conh : 290*data_num,
						 FixedTop : 150,
						 content : con,
						 mack : true,
						 footer : true,
			}).show();
			this.show(showTpl);
	}
}