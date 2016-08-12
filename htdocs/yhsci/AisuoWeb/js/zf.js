function onBridgeReady(){
	WeixinJSBridge.on('menu:share:timeline', function(argv){
	WeixinJSBridge.invoke('shareTimeline',{
	"img_url"    : 'http://wx.hysci.com.cn/yhsci/Common/weixin/upload/201407310002101692.jpg',
	"img_width"  : "640",
	"img_height" : "640",
	"link"       : 'http://wx.hysci.com.cn/yhsci/News/show.php?mid=4&index=1',
	"desc"       : '这是一个神秘的邮局，把你藏在心中的话写在信纸上，寄给爱人...',
	"title"      : '爱锁'
	}, function(res) {
	            });
	
	});

	// 发送给好友; 
    WeixinJSBridge.on('menu:share:appmessage', function(argv){
    
	WeixinJSBridge.invoke('sendAppMessage',{
	  "appid"      : '',
	  "img_url"    : 'http://wx.hysci.com.cn/yhsci/Common/weixin/upload/201407310002101692.jpg',
		"img_width"  : "640",
		"img_height" : "640",
		"link"       : 'http://wx.hysci.com.cn/yhsci/News/show.php?mid=4&index=1',
		"desc"       : '这是一个神秘的邮局，把你藏在心中的话写在信纸上，寄给爱人...',
		"title"      : '爱锁'
		}, function(res) {});
	});
}

if( document.addEventListener )
document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
