<?php

/**
 * 微信JS-SDK 签名生成类
 *
 * @author wangxiaohui
 * @Date : 2015-01-16 17:19
 */
class WxJsSign {
    /**
     * 微信 appId 参数,第三方用户唯一凭证
     */
    public $appId = null;
    /**
     * 微信 第三方用户唯一凭证密钥，即appsecret
     */
    public $appSecret = null;
    
    public $appInfo = null;
    /**
     * 微信 JS-SDK signType参数，签名方式
     */
    public $signType = 'sha1';
    /**
     * 微信 需要使用的JS-API LIST
     */
    public $jsApiList = array();
    public $accessToken = null;
    /**
     * 微信 获取jsapi_ticket URL
     */
    public $getTicketUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi';

    /**
     * 构造函数
     *
     * @param string $appid 微信APPID
     * @param string $appSecret 微信APPSECRET
     * @return void
     */
    public function __construct($app_info) {
        $this->appId = $app_info['app_id'];
        $this->appSecret = $app_info['app_secret'];
        $this->appInfo = $app_info;
    }

    /**
     * 获取签名串
     *
     * @param string $appid 微信APPID
     * @param string $appSecret 微信APPSECRET
     * @param array $jsApiType
     * @return void
     */
    public function getSignPackage($url = null) {
        $jsapiTicket = $this->_getJsApiTicket();
        if (empty($url)) {
        	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        	$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        $timestamp = time();
        $nonceStr = $this->_createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
        );
        return $signPackage;
    }

    /**
     * 获取卡券签名串
     *
     * @return string
     */
    public function getCardSign($cardId, $code = null, $openid = null, $balance = null) {
        //api_ticket、timestamp、card_id、code、openid、balance
    	$apiTicket = $this->_getJsApiTicket('wx_card');
        $sigArr = array(
        		$apiTicket, $cardId, strval(time()), $code, $openid, $balance
        );
        $sigArr = array_filter($sigArr);
        sort($sigArr, SORT_STRING);
        return sha1(implode($sigArr));
    }

    private function _getAccessToken() {
    	if ($this->appInfo['app_type'] == 2) {
    		//授权模式
    		$compConf = ComponectConfig::get('COUPON');
    		$compCoupon = array(
    				'comp_appid' => @$compConf['APP_ID'],
    				'comp_app_secret' => @$compConf['APP_SECRET'],
    				'authorizer_appid' => $this->appId
    		);
    		return getAuthorizerAccessToken($compCoupon);
    	}
        return getToken($this->appId, $this->appSecret);
    }

    private function _createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function _getJsApiTicket($type = 'jsapi') {
        $type = in_array($type, array('jsapi', 'wx_card')) ? $type : 'jsapi';
        if ('jsapi' == $type) {
            $cacherId = GlobalCatchId::WX_JS_API_TICKET . $this->appId;
        } else {
            $cacherId = GlobalCatchId::APP_CARD_TICKET . $this->appId;
        }
        $cacher = Factory::getCacher();
        $ticket = $cacher->get($cacherId);
        if (!$ticket) {
            $token = $this->_getAccessToken();
            if (!$token) {
                return false;
            }
            // 引入微信api
            if (!class_exists("WeiXinClient")) {
                include_once dirname(__FILE__) . "/../API/WeiXinApiCore.class.php";
            }
            $weixinApi = WeiXinApiCore::getClient($this->appId, $this->appSecret, $token);
            $ticket = $weixinApi->getJsApiTicket($type);
            if ($ticket) {
                $cacher->set($cacherId, $ticket, GlobalCatchExpired::APP_CARD_TICKET);
            }
        }
        return $ticket;
    }

    /**
     * 设置调用JsApi类型
     */
    public function setApiType($type = array()) {
        $apiList = array(

            // 分享
            1 => array(
                'onMenuShareTimeline', // 分享朋友圈
                'onMenuShareAppMessage', // 分享好友消息
                'onMenuShareQQ', // 分享QQ好友
                'onMenuShareWeibo'
            ) // 分享微博
        ,

            // 图像
            2 => array(
                'chooseImage', // 拍照或从手机相册中选图接口
                'previewImage', // 预览图片接口
                'uploadImage', // 上传图片接口
                'downloadImage'
            ) // 下载图片接口
        ,

            // 音频
            3 => array(
                'startRecord', // 开始录音接口
                'stopRecord', // 停止录音接口
                'onVoiceRecordEnd', // 监听录音自动停止接口
                'playVoice', // 播放语音接口
                'pauseVoice', // 暂停播放接口
                'stopVoice', // 停止播放接口
                'onVoicePlayEnd', // 监听语音播放完毕接口
                'uploadVoice', // 上传语音接口
                'downloadVoice'
            ) // 下载语音接口
        ,

            // 智能接口
            4 => array(
                'translateVoice'
            ) // 识别音频并返回识别结果接口
        ,

            // 设备信息
            5 => array(
                'getNetworkType'
            ) // 获取网络状态接口
        ,

            // 地理位置
            6 => array(
                'openLocation', // 使用微信内置地图查看位置接口
                'getLocation'
            ) // 获取地理位置接口
        ,

            // 界面操作
            7 => array(
                'hideOptionMenu', // 隐藏右上角菜单接口
                'showOptionMenu', // 显示右上角菜单接口
                'closeWindow', // 关闭当前网页窗口接口
                'hideMenuItems', // 批量隐藏功能按钮接口
                'showMenuItems', // 批量显示功能按钮接口
                'hideAllNonBaseMenuItem', // 隐藏所有非基础按钮接口
                'showAllNonBaseMenuItem'
            ) // 显示所有功能按钮接口
        ,

            // 扫一扫
            8 => array(
                'scanQRCode'
            ) // 调起微信扫一扫接口
        ,

            // 微信小店
            9 => array(
                'openProductSpecificView'
            ) // 跳转微信商品页接口

        );

        $jsApiList = array();

        foreach ($type as $val) {
            foreach ($apiList[$val] as $api) {
                $jsApiList[] = $api;
            }
        }

        $this->jsApiList = $jsApiList;
    }
}

?>