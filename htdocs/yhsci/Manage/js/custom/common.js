var Common;
(function(){
    var common = function(){
        this.domain = document.domain;
        var url = location.href.split('?')[0].split('/');
        url.pop();        
        this.url = url.join('/');
        this.jsArr = [];
        this.lastError = '';
        this.submitBtn = true;
    };
    common.prototype.importjs = function(path){
        var file = this.getPath(path);
        this.load(file);
    };
    common.prototype.getPath = function(path){
        return this.url + '/js/' + path + '.js';
    };
    common.prototype.load = function(url){
        if(this.isLoad(url))return;
        $.ajax({
            url:url,            
            async:false,
            cache : true
        });
        this.jsArr.push(url);
    };
    common.prototype.isLoad = function(url){
        var index = $.inArray(url, this.jsArr);
        return (-1 == index) ? false : true;
    }; 
    common.prototype.get = function(id){
        return document.getElementById(id);
    };
    common.prototype.alertSet = function(msg,id){
        this.get(id+'_err').innerHTML = msg;
        this.lastError = msg;
    };
    common.prototype.submitEnable = function(id,set){
        if(set){
            return this.submitBtn = set;
        }
        var bl = this.submitBtn;
        this.submitBtn && (this.submitBtn = !this.submitBtn);
        return bl;
    };
    common.prototype.dialog = function(content,callback){
    	var obj = null;
    	if(window.top && window.top.bootbox){
    		obj = window.top.bootbox;
    	}else if($.dialog){
    		obj = bootbox;
    	}else {
    		alert(msg);
    		if (callback) {callback();}
    	}
    	obj.dialog({
    		title: "提示",
    		message: content,
    		size: 'small',
    		className: 'agent-bootbox-alert',
    		animate: true,
    		callback: function () {if (callback) setTimeout(callback, 50);}
    	});
    	$(".agent-bootbox-alert").draggable({handle: ".modal-header"});
    };
    
    common.prototype.alert = function(msg,callback, type){
    	if(window.top && window.top.bootbox){
    		var obj = window.top.bootbox;
    	}else if($.dialog){
    		var obj = bootbox;
    	}else {
    		alert(msg);
    		if (callback) {callback()}
    	}
    	obj.alert({
    		title: "提示",
    		message: this.dom1(type, msg),
    		size: 'small',
    		className: 'agent-bootbox-alert',
    		animate: true,
    		callback: function () {if (callback) setTimeout(callback, 50)}
    	});
    	$(".agent-bootbox-alert").draggable({handle: ".modal-header"});
    };
    common.prototype.confirm = function(msg,ok_callback,cancel_callback){
    	if(window.top && window.top.bootbox){
    		var obj = window.top.bootbox;
    	}else if($.dialog){
    		var obj = bootbox;
    	}else {
    		if (confirm(msg)) {
    			if (ok_callback) {ok_callback();}
    		} else {
    			if (cancel_callback) {cancel_callback();}
    		}
    	}
    	obj.confirm({
    		title: "确认提示",
    		message: this.dom1('confirm', msg),
    		size: 'small',
    		className: 'agent-bootbox-confirm',
    		animate: true,
    		callback: function (status) {
    			if (status) {
    				if (ok_callback){setTimeout(ok_callback, 50)}
    			} else {
    				if (cancel_callback){setTimeout(cancel_callback, 50)}
    			}
    		}
    	});
    	$(".agent-bootbox-confirm").draggable({handle: ".modal-header"});
    };
    common.prototype.dom1 = function (type, msg) {
    	msg = msg || '';
    	var css = 'glyphicon-info-sign font-blue';
    	switch (type) {
	    	case "ok":
	    		css = 'glyphicon-ok-sign font-green';
	    		break;
	    	case "warning":
	    		css = 'glyphicon-warning-sign font-yellow';
	    		break;
	    	case "error":
	    		css = 'glyphicon-remove-sign font-red';
	    		break;
	    	case "confirm":
	    		css = 'glyphicon-question-sign font-yellow';
	    		break;
    	}
    	var t = '<div class="mar_text">'
    		  + '<span class="item font_zi_size">'
    		  + '<span aria-hidden="true" class="fon_size_big glyphicon '+css+' "></span>'
    		  + '<span style="font-size:16px;">&nbsp;'+msg+'</span></span>';
    		  + '</div>';
    	return t;
    };
    common.prototype.ajax = function(url,param,callback,type){
        var self = this;
        type = type || 'post';
		$.ajax({
			url : url,
			type: type,
			data: param,		
			dataType : 'json',
			beforeSend: function(){},		
			complete : function(){},
			error : function(){
	                    self.alert('网络链接失败',function(){
	                        if (callback) callback.call(window, false);
	                        self.submitBtn = true;
	                    });                    
			},
			success : function(result){			
	                    if (result.error == '0') {
	                        if (callback) callback.call(window, true, result);
	                    }else{
	                        self.alert(result,function(){
	                            if (callback) callback.call(null, false, result);
	                        });
	                    }
			}
		});
    };
    /**
     * loadding的样式
     * @param param
     * @param loadParam
     */
    common.prototype.loading = function(type,loadParam){
    	type = type || 'on';
    	loadParam = loadParam || {};
    	var tip = loadParam.tip || '加载中...';
    	var id = '__common_agent_loading__';
    	if (type == 'off') {
    		$('#'+id).modal('hide');
    		return;
    	}
    	if (!$("#"+id).attr('id')) {
    		var ht = ''
        		+'<div data-backdrop="static" aria-hidden="true" role="basic" id="'+id+'" class="modal fade bs-modal-sm">'
    				+'<div class="modal-dialog modal-sm">'
    					+'<div class="modal-content">'
    						+'<div class="modal-body text-center" style="padding:30px 0">'
    						+'<img alt="" src="img/loading-spinner-grey.gif">'
    						+'<span>&nbsp;&nbsp;'+tip+'</span>'
    						+'</div>'
    					+'</div>'
    				+ '</div>'
    			+ '</div>';
    		$('body').append(ht);
    	}
    	$('#'+id).modal('show');
    };
    /**
     * 异步加载
     * @param url  url
     * @param param  参数
     * @param ok_callback  error==0时的回调函数
     * @param error_callback error!=0时的回调函数
     * @param type		post/get 默认post
     * @param loadtype  loadding样式
     */
    common.prototype.request = function(url,param,ok_callback,error_callback,type,loadParam){
        var self = this;
        type = type || 'post';
        loadParam = loadParam || {};
        //loadMack({off:'on'});    	
        self.loading("on",loadParam);     
		$.ajax({
			url : url,
			type : type,
			data : param,
			dataType : 'json',
			beforeSend : function() {
			},
			complete : function() {
			},
			error : function() {
				alert("error");
				//loadMack({off : 'off'});
				self.loading("off",loadParam);
				if(loadParam['type'] == "divtip" && loadParam['contentid']){
					var html = "";
					html += '<div style="text-align:center;font-size:12px;padding:10px">';
					html += '系统异常,稍后再试!';
					html += '</div>';
					$("#"+contentid).html(html);
				}else{
					self.alert('系统异常,稍后再试!', function() {
						if (error_callback){
							alert("tttt");
							error_callback.call(null, false, false);
						}							
						self.submitBtn = true;
					});
				}
				
			},
			success : function(result) {
				//loadMack({off : 'off'});
				self.loading("off",loadParam);
				if (result.error == '0') {
					if (ok_callback)
						ok_callback.call(window,result, true);
				} else {
					if (error_callback) {
						error_callback.call(window,result, false);
					} else {
						if(loadParam['type'] == "divtip" && loadParam['contentid']){
							var html = "";
							html += '<div style="text-align:center;font-size:12px;padding:10px">';
							html += result.msg;
							html += '</div>';
							$("#"+contentid).html(html);
						}else{
							self.alert(result.msg);
						}
						
					}

				}
			}
		});
    };
    Common = new common();  
})();
