<?php
include_once ABC_PHP_PATH . '/API/WeiXinError.class.php';
include_once ABC_PHP_PATH . '/API/WeiXinApiRequest.class.php';
include_once ABC_PHP_PATH . '/Common/Json.class.php';

/**
 * 微信卡包相关api接口文件
 * @author paizhang  2014-09-29
 */

class WeiXinCardApi {
    /**
     * appid
     * @var string
     */
    protected $appId = '';
    /**
     * app secret
     * @var string
     */
    protected $appSecret = '';
    /**
     * access token
     * @var string
     */
    protected $accessToken = '';
    /**
     * 微信用户openid
     * @var string
     */
    protected $openid = '';

    /**
     * 最后错误代码
     */
    protected $_error_code = 0;

    /**
     * 最后错误信息
     */
    protected $_error_message = '';

    /**
     * api uri
     * @var string
     */
    protected $apiUri = 'https://api.weixin.qq.com/';

    public function __construct($appId, $appSecret, $accessToken) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->setAccessToken($accessToken);
    }

    /**
     * 获取错误code
     *
     * function_description
     *
     * @author mxg
     * @return int
     */
    public function getErrorCode() {
        return $this->_error_code;
    }

    /**
     * 获取错误信息
     *
     * function_description
     *
     * @author mxg
     * @return string
     */
    public function getErrorMessage() {
        return $this->_error_message;
    }

    /**
     * 设置access token
     * @param string $token
     */
    public function setAccessToken($token) {
        $this->accessToken = $token;
    }

    /**
     *
     * @param array $baseInfo see createCash()
     */
    /**
     * 创建代金券时，检查base_info信息是否合法
     * @param $baseInfo
     * @return bool
     */
    protected function _checkBaseInfo($baseInfo) {
        $result = array();
        if (empty($baseInfo['logo_url'])) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        $codeType = array('CODE_TYPE_BARCODE', 'CODE_TYPE_TEXT', 'CODE_TYPE_QRCODE');
        if (!in_array(@$baseInfo['code_type'], $codeType)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if (empty($baseInfo['brand_name']) || (mb_strlen($baseInfo['brand_name']) > 36)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if (empty($baseInfo['title']) || (mb_strlen($baseInfo['title']) > 27)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        $colorArr = array('Color010', 'Color020', 'Color030', 'Color040', 'Color050', 'Color060', 'Color070', 'Color080', 'Color090', 'Color100','Color081','Color082','Color101','Color102');
        if (!in_array(@$baseInfo['color'], $colorArr)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if (empty($baseInfo['notice']) || (mb_strlen($baseInfo['notice']) > 27)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if (empty($baseInfo['description']) || (mb_strlen($baseInfo['description']) > 900)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if (empty($baseInfo['date_info'])) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if (!in_array(@$baseInfo['date_info']['type'], array(1, 2))) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if (1 == $baseInfo['date_info']['type']) {
            if (empty($baseInfo['date_info']['begin_timestamp']) || !is_string($baseInfo['date_info']['begin_timestamp'])) {
                $this->_setError(WX_Error::PARAM_ERROR, false);
                return false;
            }
            if (empty($baseInfo['date_info']['end_timestamp']) || !is_string($baseInfo['date_info']['end_timestamp'])) {
                $this->_setError(WX_Error::PARAM_ERROR, false);
                return false;
            }
        } else if (2 == $baseInfo['date_info']['type']) {
            if (empty($baseInfo['date_info']['fixed_term'])) {
                $this->_setError(WX_Error::PARAM_ERROR, false);
                return false;
            }
        }
        if (empty($baseInfo['sku'])) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if (empty($baseInfo['sku']['quantity'])) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        return true;
    }

    /**
     * 创建代金券卡券接口
     * @param $baseInfo array
     *    array (
     *  logo_url : 卡券的商户logo，尺寸为300*300。(必填)
     *  code_type : code 码展示类型 "CODE_TYPE_TEXT"，文本"CODE_TYPE_BARCODE"，一维码"CODE_TYPE_QRCODE"，二维码(必填)
     *  brand_name : 商户名字,字数上限为12 个汉字(必填)
     *  title : 券名，字数上限为9 个汉字(必填)
     *  sub_title : 券名的副标题，字数上限为18 个汉字。
     *  color : 券颜色。按色彩规范标注填写Color010-Color100(必填)
     *  notice : 使用提醒，字数上限为9 个汉字。（一句话描述，展示在首页）(必填)
     *  service_phone : 客服电话
     *  source : 第三方来源名，例如同程旅游、格瓦拉。
     *  description : 使用说明。长文本描述，可以分行，上限为1000 个汉字。(必填)
     *  use_limit : 每人使用次数限制
     *  get_limit : 每人最大领取次数，不填写默认等于quantity。
     *  use_custom_code : 是否自定义code 码。填写true 或false，不填代表默认为false。
     *  bind_openid : 是否指定用户领取，填写true 或false。不填代表默认为否。
     *  can_share : 领取卡券原生页面是否可分享，填写true 或false，true 代表可分享。默认可分享。
     *  can_give_friend : 卡券是否可转赠，填写true或false,true 代表可转赠。默认可转赠。
     *  location_id_list : 门店位置ID。商户需在mp平台上录入门店信息或调用批量导入门店信息接口获取门店位置ID。
     *  date_info : array (
     *        type: 使用时间的类型,1：固定日期区间，2：固定时长（自领取后按天算）(必填)
     *        begin_timestamp: 固定日期区间专用，表示起用时间
     *        end_timestamp: 固定日期区间专用，表示结束时间
     *        fixed_term: 固定时长专用，表示自领取后多少天内有效
     *        fixed_begin_term: 固定时长专用，表示自领取后多少天开始生效
     *    )
     *  sku : array (
     *        quantity :    上架的数量(必填)
     *    )
     *  url_name_type : 商户自定义cell 名称。"URL_NAME_TYPE_TAKE_AWAY"，外卖
     *                "URL_NAME_TYPE_RESERVATION"，在线预订
     *                "URL_NAME_TYPE_USE_IMMEDIATELY"，立即使用
     *  custom_url : 商户自定义url 地址，支持卡券页内跳转
     *
     *  )
     * @param float $reduceCost 代金券专用，表示减免金额（单位为分）
     * @param float $leastCost 代金券专用，表示起用金额（单位为分）
     * @return string  the card id
     */
    public function createCash($baseInfo, $reduceCost, $leastCost = false) {
        //参数验证
        if (!$this->_checkBaseInfo($baseInfo)) {
            return null;
        }
        $param = array('card' => array('card_type' => 'CASH', 'cash' => array('base_info' => $baseInfo, 'least_cost' => $leastCost, 'reduce_cost' => $reduceCost)));
        $url = $this->_getUrl('card/create');
        //WeiXinApiRequest::$debug = 1;
        $json = new Json();
        $param = $json->encode($param, false);
        $response = WeiXinApiRequest::post($url, $param);
        Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCreateCard'));
    }

    /**
     * 创建折扣券
     * @param $baseInfo
     * @param $discount
     * @return mixed|null
     */
    public function createDiscount($baseInfo, $discount) {
        //参数验证
        if (!$this->_checkBaseInfo($baseInfo)) {
            return null;
        }
        $param = array('card' => array('card_type' => 'DISCOUNT', 'discount' => array('base_info' => $baseInfo, 'discount' => $discount)));
        $url = $this->_getUrl('card/create');
        //WeiXinApiRequest::$debug = 1;
        $json = new Json();
        $param = $json->encode($param, false);
        $response = WeiXinApiRequest::post($url, $param);
        Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCreateCard'));
    }

    /**
     * 创建礼品券
     * @param $baseInfo
     * @param $gift
     * @return mixed|null
     */
    public function createGift($baseInfo, $gift) {
        //参数验证
        if (!$this->_checkBaseInfo($baseInfo)) {
            return null;
        }
        $param = array('card' => array('card_type' => 'GIFT', 'gift' => array('base_info' => $baseInfo, 'gift' => $gift)));
        $url = $this->_getUrl('card/create');
        //WeiXinApiRequest::$debug = 1;
        $json = new Json();
        $param = $json->encode($param, false);
        $response = WeiXinApiRequest::post($url, $param);
        Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCreateCard'));
    }

    /**
     * 创建团购券
     * @param $baseInfo
     * @param $gift
     * @return mixed|null
     */
    public function createGroupon($baseInfo, $deal_detail) {
        //参数验证
        if (!$this->_checkBaseInfo($baseInfo)) {
            return null;
        }
        $param = array('card' => array('card_type' => 'GROUPON', 'groupon' => array('base_info' => $baseInfo, 'deal_detail' => $deal_detail)));
        $url = $this->_getUrl('card/create');
        //WeiXinApiRequest::$debug = 1;
        $json = new Json();
        $param = $json->encode($param, false);
        $response = WeiXinApiRequest::post($url, $param);
        Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCreateCard'));
    }

    /**
     * 创建优惠券（通用券）
     * @param $baseInfo
     * @param $gift
     * @return mixed|null
     */
    public function createGeneralCoupon($baseInfo, $default_detail) {
        //参数验证
        if (!$this->_checkBaseInfo($baseInfo)) {
            return null;
        }
        $param = array('card' => array('card_type' => 'GENERAL_COUPON', 'general_coupon' => array('base_info' => $baseInfo, 'default_detail' => $default_detail)));
        $url = $this->_getUrl('card/create');
        //WeiXinApiRequest::$debug = 1;
        $json = new Json();
        $param = $json->encode($param, false);
        $response = WeiXinApiRequest::post($url, $param);
        Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCreateCard'));
    }
    
    /**
     * 创建朋友的券(代金券)
     * @param $baseInfo
     * @param $gift
     * @return mixed|null
     */
    public function createFriendCash($baseInfo, $advancedInfo ,$reduceCost, $leastCost = false) {
    	//参数验证
    	//if (!$this->_checkBaseInfo($baseInfo)) {
    	//	return null;
    	//}
    	$param = array('card' => array('card_type' => 'CASH', 'cash' => array('base_info' => $baseInfo, 'advanced_info' => $advancedInfo,'least_cost' => $leastCost, 'reduce_cost' => $reduceCost)));
    	$url = $this->_getUrl('card/create');
    	//WeiXinApiRequest::$debug = 1;
    	Logger::info("------------------eade test-------------");
    	$json = new Json();
    	$param = $json->encode($param, false);
    	$response = WeiXinApiRequest::post($url, $param);
    	Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseCreateCard'));
    }
    
    /**
     * 创建朋友的券(礼品券)
     * @param $baseInfo
     * @param $gift
     * @return mixed|null
     */
    public function createFriendGift($baseInfo, $advancedInfo ,$giftName, $gift,
    			$giftNum,$giftUnit) {
    	//参数验证
    	if (!$this->_checkBaseInfo($baseInfo)) {
    		return null;
    	}
    	$param = array('card' => array('card_type' => 'GIFT', 
    				'gift' => array('base_info' => $baseInfo,
    						 'advanced_info' => $advancedInfo,
    						'gift_name' => $giftName,
    						'gift_num' => $giftNum,
    						'gift_unit' => $giftUnit,
    						'gift' => $gift)));
    	$url = $this->_getUrl('card/create');
    	//WeiXinApiRequest::$debug = 1;
    	$json = new Json();
    	$param = $json->encode($param, false);
    	$response = WeiXinApiRequest::post($url, $param);
    	Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseCreateCard'));
    }

    /**
     * 开通券点账户
     */
    public function friendPayActivate(){
    	$url = $this->_getUrl('card/pay/activate');
    	$param = array();
    	$response = WeiXinApiRequest::get($url, json_encode($param));
    	Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));    	
    }
    
    /**
     * 查询券余额
     */
    public function friendPayGetCoinsInfo(){
    	$url = $this->_getUrl('card/pay/getcoinsinfo');
    	$param = array();
    	$response = WeiXinApiRequest::get($url, json_encode($param));
    	Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    
    /**
     * 获取卡券增加指定库存后的价格订单
     */
    public function friendPayGetPayPrice($card_id,$quantity){
    	$url = $this->_getUrl('card/pay/getpayprice');
    	$param = array('card_id' => $card_id,"quantity"=>$quantity);
    	$response = WeiXinApiRequest::post($url, json_encode($param));
    	Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    
    /**
     * 确认增加库存
     */
    public function friendPayConfirm($card_id,$quantity,$order_id){
    	$url = $this->_getUrl('card/pay/confirm');
    	$param = array('card_id' => $card_id,"quantity"=>$quantity,"order_id"=>$order_id);
    	$response = WeiXinApiRequest::post($url, json_encode($param));
    	Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    
    /**
     * 充值券点接口
     */
    public function friendPayRecharge($coin_count){
    	$url = $this->_getUrl('card/pay/recharge');
    	$param = array('coin_count' => $coin_count);
    	$response = WeiXinApiRequest::post($url, json_encode($param));
    	Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    /**
     * 获取卡券详情
     * @param $card_id
     * @return mixed|null
     */
    public function getCardInfo($card_id) {
        if (empty($card_id)) {
            return null;
        }
        $url = $this->_getUrl('card/get');
        //WeiXinApiRequest::$debug = 1;
        $param = array('card_id' => $card_id);
        $response = WeiXinApiRequest::post($url, json_encode($param));
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseGetCardInfo'));
    }

    /**
     * 删除卡券（这里不仅仅只能删除代金券，包含了能创建的7种卡券）
     * @param $card_id 卡券id
     * @return mixed|null
     */
    public function deleteCard($card_id) {
        //参数验证
        if (empty($card_id)) {
            return null;
        }
        $param = array('card_id' => $card_id);
        $url = $this->_getUrl('card/delete');
        //WeiXinApiRequest::$debug = 1;
        $response = WeiXinApiRequest::post($url, json_encode($param));
        //print_r($response);exit;
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseGeneral'));
    }

    /**
     * 检查公众号是否有卡券权限（选择删除卡券接口来判断）
     * @param $card_id 卡券id
     * @return mixed|null
     */
    public function checkCardAuthorized($card_id) {
        //参数验证
        if (empty($card_id)) {
            return null;
        }
        $param = array('card_id' => $card_id);
        $url = $this->_getUrl('card/delete');
        //WeiXinApiRequest::$debug = 1;
        $response = WeiXinApiRequest::post($url, json_encode($param));
        return $response;
    }

    /**
     * 查询code
     * @param $code
     * @return mixed|null
     */
    public function getCodeInfo($code,$cardId = null) {
        //参数验证
        if (empty($code)) {
            return null;
        }
        $param = array('code' => $code);
        if($cardId){
        	$param['card_id'] = $cardId;
        }
        $url = $this->_getUrl('card/code/get');
        //WeiXinApiRequest::$debug = 1;
        $response = WeiXinApiRequest::post($url, json_encode($param));
        //print_r($response);exit;
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCodeInfo'));
    }

    /**
     * 设置卡券失效
     * @param $code
     * @param null $card_id
     * @return mixed|null
     */
    public function setCardCodeUnavailable($code, $card_id = NULL) {
        //参数验证
        if (empty($code)) {
            return null;
        }
        $param = array('code' => $code);
        if ($card_id) array_push($param, $card_id);
        $url = $this->_getUrl('card/code/unavailable');
        //WeiXinApiRequest::$debug = 1;
        $response = WeiXinApiRequest::post($url, json_encode($param));
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseGeneral'));
    }

    /**
     * 生成卡券二维码（返回ticket，根据ticket再换取二维码图片）
     * @param $card_id
     * @param null $code
     * @param null $openid
     * @param null $expire_seconds
     * @param bool $is_unique_code
     * @return bool|mixed|null
     */
    public function createCardQrcode($card_id, $code = null, $openid = null, $expire_seconds = null, $is_unique_code = false,$outer_id=null) {
        //参数验证
        if (empty($card_id)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        $card_info = $this->getCardInfo($card_id);
        if ($card_info['use_custom_code'] && empty($code)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        if ($card_info['bind_openid'] && empty($openid)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        $expire_seconds = intval($expire_seconds);
        if ($expire_seconds && ($expire_seconds < 60 || $expire_seconds > 1800)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        $param = array();
        $param['action_name'] = 'QR_CARD';
        if ($expire_seconds) {
        	$param['expire_seconds'] = $expire_seconds;
        }
        $action_info = array();
    	$action_info['card']['card_id'] = $card_id;
        if ($code) {
            $action_info['card']['code'] = $code;
        }
        if ($openid) {
            $action_info['card']['openid'] = $openid;
        }
        if ($is_unique_code) {
            $action_info['card']['is_unique_code'] = $is_unique_code;
        }
        $param['action_info']=$action_info;
        $param = array('action_name' => 'QR_CARD', 'action_info' => $action_info);
        $url = $this->_getUrl('card/qrcode/create');
        //WeiXinApiRequest::$debug = 1;
        $response = WeiXinApiRequest::post($url, json_encode($param));
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCreateCardQrcode'));
    }
    /**
     * 创建互通任务
     * @date 2015-5-21
     * @param type $wxCardId
     * @param int $quantity
     * @param type $showSrcName
     * @param type $referReceiveAppid
     * @return type array('task_id','errmsg','errcode')
     */
    public function createTask($wxCardId,$quantity,$showSrcName,$referReceiveAppid) {
        $info = array();
        $info['card_id'] = $wxCardId;
        $info['quantity'] = intval($quantity);
        $info['show_src_name'] = $showSrcName;
        $info['refer_receive_appid'] = $referReceiveAppid;
        $url = $this->_getUrl('card/task/create');
        $response = WeiXinApiRequest::post($url, $info);
        Logger::debug('createTask:', array('info'=>$info,'response'=>$response));
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCreateTask'));
    }
    /**
     * 获取二维码图片地址
     * @param string $ticket
     */
    public function getQrcUrl($ticket) {
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
    }

    /**
     * 核销卡券code（创建卡券时use_custom_code为true时，card_id必须填写）
     * @param $code
     * @param null $card_id
     * @return bool|mixed
     */
    public function consumeCardCode($code, $card_id = null) {
        //参数验证
        if (empty($code)) {
            $this->_setError(WX_Error::PARAM_ERROR, false);
            return false;
        }
        $url = $this->_getUrl('card/code/consume');
        //WeiXinApiRequest::$debug = 1;
        $param = array();
        $param['code'] = $code;
        if ($card_id) $param['card_id'] = $card_id;
        $response = WeiXinApiRequest::post($url, json_encode($param));
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseConsumeCardCode'));
    }

    /**
     * 设置测试用户白名单
     * @param $openid_arr
     * @return mixed|null
     */
    public function setTestWhiteList($openid_arr) {
        if (empty($openid_arr)) {
            //openid数组不能为空
            $openid_arr = array();
        }
        $param = array('openid' => $openid_arr);
        $url = $this->_getUrl('card/testwhitelist/set');
        $response = WeiXinApiRequest::post($url, json_encode($param));
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseGeneral'));
    }

    public function importCode ($wxCardId, $codeArr) {
    	$param = array('card_id' => $wxCardId, 'code'=>$codeArr);
    	$url = $this->_getUrl('card/code/deposit');
    	$response = WeiXinApiRequest::post($url, $param);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseImportCode'));
    }

    public function checkImportCode ($wxCardId, $codeArr) {
    	$param = array('card_id' => $wxCardId, 'code'=>$codeArr);
    	$url = $this->_getUrl('card/code/checkcode');
    	$response = WeiXinApiRequest::post($url, $param);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseCheckImportCode'));
    }

    public function createHuTongTask ($wxCardId, $quantity, $referReceiveAppid, $showSrcName = 1) {
    	$param = array('card_id' => $wxCardId, 'quantity'=>$quantity,
    				   'refer_receive_appid'=>$referReceiveAppid, 'show_src_name'=>$showSrcName);
    	$url = $this->_getUrl('card/task/create');
    	$response = WeiXinApiRequest::post($url, $param);
    	$result = call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseCreateHuTongTask'));
    	$data = $param;$data['task_id'] = $result['task_id'];
    	Logger::debug('CreateHuTongTask:', $data);
    	return $result;
    }

    public function receiveHuTongTask ($taskId) {
    	$param = array('task_id' => $taskId);
    	$url = $this->_getUrl('card/task/receive');
    	$response = WeiXinApiRequest::post($url, $param);
    	$result = call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseReceiveHuTongTask'));
    	$data = $param;$data['new_card_id'] = $result['new_card_id'];
    	Logger::debug('receiveHuTongTask:', $data);
    	return $result;
    }
    protected function _parseReceiveHuTongTask ($response) {
    	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (!isset($response['new_card_id']) || !$response['new_card_id']) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	return array('new_card_id'=>$response['new_card_id']);
    }
    protected function _parseCreateHuTongTask ($response) {
    	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (!isset($response['task_id']) || !$response['task_id']) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	return array('task_id'=>$response['task_id']);
    }
    /**
     * 设置错误信息
     * @param unknown $errorcode
     * @param unknown $message
     */
    private function _setErrorMsg($errorcode, $message) {
        $this->_error_code = $errorcode;
        $this->_error_message = $message;

    }

    /**
     * 检查门店数据是否合法
     * @param $location_arr
     * @return bool
     */
    protected function _checkLocationInfo($location_arr) {
        $result = array();
        if (empty($location_arr)) {
            $this->_setErrorMsg(-1, "门店数据检查出错，参数不能为空或是必须是数组");
            return false;
        }
        foreach ($location_arr as $rows) {
            if (empty($rows['business_name'])) {
                $this->_setErrorMsg(-2, "门店数据出错，门店名称不能为空");
                return false;
            }
            if (empty($rows['province'])) {
                $this->_setErrorMsg(-2, "门店数据出错，省名称不能为空");
                return false;
            }
            if (empty($rows['city'])) {
                $this->_setErrorMsg(-2, "门店数据出错，城市不能为空");
                return false;
            }
            if (empty($rows['district'])) {
                $this->_setErrorMsg(-2, "门店数据出错，区域不能为空");
                return false;
            }
            if (empty($rows['address'])) {
                $this->_setErrorMsg(-2, "门店数据出错，详细地址不能为空");
                return false;
            }
            if (empty($rows['telephone'])) {
                $this->_setErrorMsg(-2, "门店数据出错，电话不能为空");
                return false;
            }
            if (empty($rows['category'])) {
                $this->_setErrorMsg(-2, "门店数据出错，分类不能为空");
                return false;
            }
            if (empty($rows['longitude'])) {
                $this->_setErrorMsg(-2, "门店数据出错，纬度不能为空");
                return false;
            }
            if (empty($rows['latitude'])) {
                $this->_setErrorMsg(-2, "门店数据出错，经度不能为空");
                return false;
            }
        }
        return true;
    }

    /**
     * 批量导入门店信息
     * @param $location_arr
     * @return mixed|null
     */
    public function batchAdd($location_arr) {
        //参数验证
        if (!$this->_checkLocationInfo($location_arr)) {
            return false;
        }
        $param = array('location_list' => $location_arr);
        $url = $this->_getUrl('card/location/batchadd');
        //WeiXinApiRequest::$debug = 1;
        $json = new Json();
        $param = $json->encode($param, false);
        $response = WeiXinApiRequest::post($url, $param);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseBatchAdd'));
    }

    /**
     * 拉取门店列表
     * @param int $offset
     * @param int $count
     * @return mixed
     */
    public function batchGet($offset = 0, $count = 0) {
        $param = array('offset' => $offset, 'count' => $count);
        $url = $this->_getUrl('card/location/batchget');
        //WeiXinApiRequest::$debug = 1;
        $response = WeiXinApiRequest::post($url, json_encode($param));
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseBatchGet'));
    }

    /**
     * 获取颜色列表
     * @return mixed
     */
    public function getColors() {
        $url = $this->_getUrl('card/getcolors');
        //WeiXinApiRequest::$debug = 1;
        $response = WeiXinApiRequest::get($url);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseGetColors'));
    }
    /**
     * 获取卡券互通任务
     * @param type $taskId
     * @return mixed array('new_card_id','errmsg','errcode')
     */
    public function getTask($taskId) {
        $url = $this->_getUrl('card/task/receive');
        $response = WeiXinApiRequest::post($url,array('task_id'=>$taskId));
        Logger::debug("获取卡券互通任务:{$taskId}",$response);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseGetTask'));
    }

    /**
     * JS API 添加到卡包
     * @return mixed|null
     */
    public function batchAddCard($card_id) {
        $card_info = $this->getCardInfo($card_id);
        if ($card_info) {
            $card_ext = array();
            $timestamp = strval(time());
            $card_ext['timestamp'] = $timestamp;
            $paraMap = array($this->appSecret, $card_id, $timestamp);
            sort($paraMap);
            $signature = sha1(implode($paraMap));
            $card_ext['signature'] = $signature;
            $card_list = array('card_list' => array(array('card_id' => $card_id, 'card_ext' => json_encode($card_ext))));
            return json_encode($card_list);
        } else {
            Logger::error('WeiXinCardApi erorr, url: ' . WeiXinApiRequest::$url, '根据card_id：' . $card_id . '查询卡券信息失败！');
            return false;
        }
    }

    /**
     * 获取卡券ticket
     * @return array ticket=>ticket,expires_in=>expires_in
     */
    public function getCardTicket() {
        $param = array('type' => 'wx_card');
        $url = $this->_getUrl('cgi-bin/ticket/getticket'); //ticket/getticket
        $response = WeiXinApiRequest::get($url, $param);
        Logger::info("api_getCardTicket----response:".json_encode($response)."   url:".$url."   param:".json_encode($param));
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseTicket'));
    }

    /**
     * 修改代金券卡券接口
     * @param string $cardId 微信card id
     * @param array $baseInfo
     * @param float $reduceCost 代金券专用，表示减免金额（单位为分）
     * @param float $leastCost 代金券专用，表示起用金额（单位为分）
     */
    public function updateCash($cardId, $baseInfo, $reduceCost = false, $leastCost = false) {
        $url = $this->_getUrl('card/update');
        $param['card_id'] = $cardId;
        $param['cash']['base_info'] = $baseInfo;
        if ($reduceCost) {
            $param['cash']['reduce_cost'] = $reduceCost;
        }
        if ($leastCost) {
            $param['cash']['least_cost'] = $leastCost;
        }
        return $this->updateCoupon($param);
    }

    /**
     * 修改折扣券
     * @param string $cardId 微信card id
     * @param $baseInfo
     * @param $discount
     * @return bool
     */
    public function updateDiscount($cardId, $baseInfo, $discount = false) {
        $url = $this->_getUrl('card/update');
        $param['card_id'] = $cardId;
        $param['discount']['base_info'] = $baseInfo;
        if ($discount) {
            $param['discount']['discount'] = $discount;
        }
        return $this->updateCoupon($param);
    }

    /**
     * 修改礼品券
     * @param string $cardId 微信card id
     * @param $baseInfo
     * @param $gift
     * @return bool
     */
    public function updateGift($cardId, $baseInfo, $gift = false) {
        $url = $this->_getUrl('card/update');
        $param['card_id'] = $cardId;
        $param['gift']['base_info'] = $baseInfo;
        if ($gift) {
            $param['gift']['gift'] = $gift;
        }
        return $this->updateCoupon($param);
    }

    /**
     * 修改团购券
     * @param string $cardId 微信card id
     * @param $baseInfo
     * @param $gift
     * @return bool
     */
    public function updateGroupon($cardId, $baseInfo, $deal_detail = false) {
        $url = $this->_getUrl('card/update');
        $param['card_id'] = $cardId;
        $param['groupon']['base_info'] = $baseInfo;
        if ($deal_detail) {
            $param['groupon']['deal_detail'] = $deal_detail;
        }
        return $this->updateCoupon($param);
    }

    /**
     * 修改优惠券（通用券）
     * @param string $cardId 微信card id
     * @param array $baseInfo
     * @param string $default_detail 描述文本
     * @return bool
     */
    public function updateGeneralCoupon($cardId, $baseInfo, $default_detail = false) {
        $url = $this->_getUrl('card/update');
        $param['card_id'] = $cardId;
        $param['general_coupon']['base_info'] = $baseInfo;
        if ($default_detail) {
            $param['general_coupon']['default_detail'] = $default_detail;
        }
        return $this->updateCoupon($param);

    }

    //更新优惠券接口
    protected function updateCoupon($cardInfo) {
        $url = $this->_getUrl('card/update');
        $json = new Json();
        $param = $json->encode($cardInfo, false);
        $response = WeiXinApiRequest::post($url, $param);
        Logger::debug(__METHOD__.' wx_api_req: params:'.WeiXinApiRequest::$params.' ; response: '.WeiXinApiRequest::$response);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseUpdateCard'));
    }

    //修改卡券库存
    public function modifyStock($data) {
        $url = $this->_getUrl('card/modifystock');
        $json = new Json();
        $param = $json->encode($data, false);
        $response = WeiXinApiRequest::post($url, $param);
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseUpdateCard'));
    }
    /**
     * 创建优惠券landing page
     * @param array $data
     * array(
     * 		banner : [url]页面的 banner
     * 		page_title:页面的 title
     * 		can_share:页面能否分享（true/false)
     * 		scene:投放页面的场景值[参考文档]
     * 		card_list: array(
     * 			array(
     *				card_id:卡券唯一 ID
     *				thumb_url:缩略图 url
     * 			)
     * 		)
     * )
     */
    public function createLandingPage ($data) {
    	$url = $this->_getUrl('card/landingpage/create');
    	$response = WeiXinApiRequest::post($url, $data);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseCreateLandingPage'));
    }
    
    /**
     * 获取卡券数据概览
     * @param array $data
     * array(
     * 		begin_date :查询数据的起始时间
     * 		end_date:查询数据的截至时间
     * 		cond_source:卡券来源，0为公众平台创建
						的卡券数据、1是API 创建的
						卡券数据
     * )
     */
    public function getCardBizuinInfo ($data) {
    	$url = $this->_getUrl('datacube/getcardbizuininfo');
    	$response = WeiXinApiRequest::post($url, $data);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseGetCardBizuinInfo'));
    }
     /**
     * 获取卡券数据概览
     * @param array $data
     * array(
     * 		begin_date :查询数据的起始时间
     * 		end_date:查询数据的截至时间
     * 		cond_source:卡券来源，0为公众平台创建
						的卡券数据、1是API 创建的
						卡券数据
			card_id : 卡券ID
     * )
     */
    public function getCardCardInfo ($data) {
    	$url = $this->_getUrl('datacube/getcardcardinfo');
    	$response = WeiXinApiRequest::post($url, $data);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseGetCardCardInfo'));
    }
/**
     * 获取卡券数据概览
     * @param array $data
     * array(
     * 		begin_date :查询数据的起始时间
     * 		end_date:查询数据的截至时间
     * 		cond_source:卡券来源，0为公众平台创建
						的卡券数据、1是API 创建的
						卡券数据
     * )
     */
    public function getCardMemberCardInfo ($data) {
    	$url = $this->_getUrl('datacube/getcardmembercardinfo');
    	$response = WeiXinApiRequest::post($url, $data);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseGetCardMemberCardInfo'));
    }
    /**
     * 创建优惠券landing page
     * @param array $data
     * array(
     * 		begin_date :查询数据的起始时间
     * 		end_date:查询数据的截至时间
     * 		cond_source:卡券来源，0为公众平台创建
						的卡券数据、1是API 创建的
						卡券数据
			card_id : 卡券ID
     * )
     */
    public function getCardTaskCardInfo ($data) {
    	$url = $this->_getUrl('datacube/getcardtaskcardinfo');
    	$response = WeiXinApiRequest::post($url, $data);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseGetCardTaskCardInfo'));
    }

    /**
     * 获取api请求url
     * @param 请求模块路径 $path
     * @param 参数 $params
     * @return string
     */
    protected function _getUrl($path, $params = array()) {
        $path = trim(trim($path), '/');
        $url = $this->apiUri . $path . '?access_token=' . $this->accessToken;
        //echo $url;
        if ($params) {
            $url .= '?' . (is_array($params) ? http_build_query($params) : ltrim($params, '?'));
        }
        return $url;
    }
    
    /**
     * 卡券加上买单功能
     * @param unknown $wxCardId
     * @param unknown $codeArr
     * @return mixed
     */
    public function setPaycell ($wxCardId,$isOpen) {
    	$param = array('card_id' => $wxCardId, 'is_open'=>$isOpen);
    	$url = $this->_getUrl('card/paycell/set');
    	$response = WeiXinApiRequest::post($url, $param);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseSetPaycell'));
    }

    /**
     * 创建子商户接口
     * @param json $info
     * 		{ "brand_name": "aaaaaa",
     * 					"logo_url": "http://mmbiz.xxxx",
     * 					"protocol": "media_id",
     * 					"end_time": 1438990559,
     * 					"primary_category_id": 1,
     * 					"secondary_category_id": 101
     * 		}
     * @return mixed
     */
    public function subMerchant ($info) {
    	$param = array('info' => $info);
    	$url = $this->_getUrl('card/submerchant/submit');
        $json = new Json();
        $param = $json->encode($param, false);
    	$response = WeiXinApiRequest::post($url, $param);
    	Logger::debug(__METHOD__." log: [url:[".$url."]][params: [".WeiXinApiRequest::$params."]][response: [{$response}]]");
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    
    
    /**
     * 获取卡券类目
     * @return mixed
     */
    public function getApplyProtocol () {
    	$param = array();
    	$url = $this->_getUrl('card/getapplyprotocol');
    	$response = WeiXinApiRequest::post($url, $param);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    /**
     * 更新子商户接口
     * @param json $info
     * 		{ 			"merchant_id":12,
     * 					"brand_name": "aaaaaa",
     * 					"logo_url": "http://mmbiz.xxxx",
     * 					"protocol": "media_id",
     * 					"end_time": 1438990559,
     * 					"primary_category_id": 1,
     * 					"secondary_category_id": 101
     * 		}
     * @return mixed
     */
    public function updateMerchant ($info) {
    	$param = array('info' => $info);
    	$url = $this->_getUrl('card/submerchant/update');
        $json = new Json();
        $param = $json->encode($param, false);
    	$response = WeiXinApiRequest::post($url, $param);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    
    /**
     * 拉取单个子商户信息接口
     * @param int $merchantId
     * @return mixed
     */
    public function getMerchant ($merchantId) {
    	$param = array('merchant_id' => $merchantId);
    	$url = $this->_getUrl('card/submerchant/get');
    	$response = WeiXinApiRequest::post($url, $param);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    
    /**
     * 拉取多个子商户信息接口
     * @param int $merchantId
     * @return mixed
     */
    public function getBatchMerchant ($beginId,$limit,$status) {
    	$param = array('begin_id' => $beginId,"limit"=>$limit,"status"=>$status);
    	$url = $this->_getUrl('card/submerchant/batchget');
    	$response = WeiXinApiRequest::post($url, $param);
    	return call_user_func_array(array($this, '_parse'),
    			array(WeiXinApiRequest::$http_code, $response, '_parseBase'));
    }
    
    /**
     * 分析数据结果
     *
     * @param int $code curl发送请求的状态码
     * @param array $response 得到的结果
     * @param string $fun_name
     * @return mixed
     */
    protected function _parse($code, $response, $func_name = '') {
        $error = $this->_parseError($code, $response);
        if ($error == false) {
            return false;
        }
        if ($func_name) {
            return call_user_func_array(array($this, $func_name), array($response));
        }
        return true;
    }
    
    /**
     * 基础解析
     * @date 2015-5-21
     * @param $response
     * @return array
     */
    protected function _parseBase($response) {
    	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (0 == $response['errcode']) {
    		return $response;
    	}
    	return false;
    }
	protected function _parseImportCode($response) {
		if (!isset($response['errcode']) || !isset($response['errmsg'])) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return false;
		}
		return array(
				'succ_code' => (int)@$response['succ_code'],
				'duplicate_code' => (int)@$response['duplicate_code'],
				'fail_code' => (int)@$response['fail_code']
			);
	}
	protected function _parseCheckImportCode ($response) {
		if (!isset($response['errcode']) || !isset($response['errmsg'])) {
			$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
			return false;
		}
		return array(
				'exist_code' => (int)@$response['exist_code'],
				'not_exist_code' => (int)@$response['not_exist_code']
			);
	}

    protected function _parseUpdateCard($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return true;
        }
        return false;
    }

    /**
     * 分析创建卡券结果
     * @author huqian
     * @param  array $response
     * @return string|null $card_id
     */
    protected function _parseCreateCard($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response['card_id'];
        }
        return false;
    }

    /**
     * 查询卡券详情结果
     * @param $response
     * @return bool
     */
    protected function _parseGetCardInfo($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response['card'];
        }
        return false;
    }

    /**
     * 生成卡券二维码
     * @param $response
     * @return bool
     */
    protected function _parseCreateCardQrcode($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response['ticket'];
        }
        return false;
    }

    /**
     * 核销卡券code结果
     * @param $response
     * @return bool
     */
    protected function _parseConsumeCardCode($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response;
        }
        return false;
    }

    /**_parseGeneral
     * 分析执行普通请求返回的结果（返回的数据示例为：{"errcode":0,"errmsg":"OK"}）
     * @author huqian
     * @param  array $response
     * @return bool
     */
    protected function _parseGeneral($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return true;
        }
        return false;
    }

    /**
     * 查询code返回信息结果
     * @author huqian
     * @param  array $response
     * @return string|null
     */
    protected function _parseCodeInfo($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return array('openid' => $response['openid'], 'card' => $response['card']);
        }
        return false;
    }

    /**
     * 分析批量导入门店信息结果
     * @author huqian
     * @param  array $response
     * @return string|null $card_id
     */
    protected function _parseBatchAdd($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response['location_id_list'];
        }
        return false;
    }

    /**
     * 拉取门店列表结果
     * @param $response
     * @return bool
     */
    protected function _parseBatchGet($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response['location_list'];
        }
        return false;
    }

    /**
     * 解析ticket结果
     * @param $response
     * @return array
     */
    protected function _parseTicket($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return array('ticket' => $response['ticket'], 'expires_in' => $response['expires_in']);
        }
        return false;
    }
    /**
     * 解析task结果
     * @param $response
     * @return array
     */
    protected function _parseCreateTask($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == intval($response['errcode'])) {
            return $response;
        }
        return false;
    }
    
    protected function _parseCreateLandingPage  ($response) {
    	if (!isset($response['errcode']) || !isset($response['errmsg']) || !@$response['url'] || !@$response['page_id']) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (0 != intval($response['errcode'])) {
    		return false;
    	}
    	return array(
    		'url' => $response['url'],
    		'page_id' => $response['page_id']
    	);
    }

    /**
     * 获取颜色列表结果
     * @param $response
     * @return bool
     */
    protected function _parseGetColors($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response['colors'];
        }
        return false;
    }
    /**
     * 获取卡券互通任务
     * @date 2015-5-21
     * @param $response
     * @return array
     */
    protected function _parseGetTask($response) {
        Logger::debug('获取卡券互通任务',$response);
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response;
        }
        return false;
    }

    /**
     * 获取卡券数据概况
     * @date 2015-5-21
     * @param $response
     * @return array
     */
    protected function _parseGetCardBizuinInfo($response) {
    	Logger::debug('获取卡券数据概况',$response);
    	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (0 == $response['errcode']) {
    		return $response;
    	}
    	return false;
    }
    
    /**
     * 获取某一卡券的数据报表
     * @date 2015-5-21
     * @param $response
     * @return array
     */
    protected function _parseGetCardCardInfo($response) {
    	Logger::debug('获取某一卡券的数据报表',$response);
    	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (0 == $response['errcode'] ) {
    		return $response;
    	}
    	return false;
    }
    
    /**
     * 获取会员卡数据报表
     * @date 2015-5-21
     * @param $response
     * @return array
     */
    protected function _parseGetCardMemberCardInfo($response) {
    	Logger::debug('获取会员卡数据报表',$response);
    	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (0 == $response['errcode']) {
    		return $response;
    	}
    	return false;
    }
    
    /**
     * 获取卡券互通任务
     * @date 2015-5-21
     * @param $response
     * @return array
     */
    protected function _parseGetCardTaskCardInfo($response) {
    	Logger::debug('获取卡券互通任务',$response);
    	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (0 == $response['errcode']) {
    		return $response;
    	}
    	return false;
    }
    
    /**
     * 设置卡券买单功能
     * @date 2015-5-21
     * @param $response
     * @return array
     */
    protected function _parseSetPaycell($response) {
    	if (!isset($response['errcode']) || !isset($response['errmsg'])) {
    		$this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
    		return false;
    	}
    	if (0 == $response['errcode']) {
    		return $response;
    	}
    	return false;
    }
    

    /**
     * 分析错误
     *
     * @param  int $code
     * @param  array $response
     * @return bool
     */
    protected function _parseError($code, $response) {
        $this->_error_code = WX_Error::NO_ERROR;
        if (200 == $code && (isset($response['errcode']) && $response['errcode'])) {
            $code = $response['errcode'];
        }
        //与微信链接失败
        if (0 == $code) {
            $code = 5100;
        }

        switch ($code) {
            case 200:
                return true;
            //http code
            case 404:
                $error_code = WX_Error::HTTP_FORBIDDEN_ERROR;
                break;
            case 503:
                $error_code = WX_Error::HTTP_SERVICE_UNAVAILABLE_ERROR;
                break;
            case 40013:
                $error_code = WX_Error::INVALID_APP_ID_ERROR;
                break;
            case 41001:
                $error_code = WX_Error::KOTEN_MISSING_ERROR;
                break;
            case 40073: //invalid card id
                $error_code = WX_Error::INVALID_CARD_ID;
                break;
            case 40056: //invalid serial code
                $error_code = WX_Error::INVALID_CARD_CODE;
                break;
            case 40001 :
                $error_code = WX_Error::INVALID_CREDENTIAL_ERROR;
                $this->clearTokenCache();
                break;
            case 40003 :
                $error_code = WX_Error::INVALID_USER_ERROR;
                break;
            case 42001 :
                $error_code = WX_Error::TOKEN_EXPIRED_ERROR;
                $this->clearTokenCache();
                break;
            case 40079:
                $error_code = WX_Error::INVALID_CARD_TIME;
                break;
            case 61016:
            	$error_code = WX_Error::API_AUTH_NOT_CONFIRM;
            	break;
            default:
                $error_code = $code;
        }
        $this->_setError($error_code);
        return false;
    }

    /**
     * 清楚token缓存陈胖
     */
    protected function clearTokenCache() {
        if (function_exists("clearToken") && function_exists("getToken")) {
            $oldToken = getToken($this->appId, $this->appSecret, false);
            if ($oldToken && $oldToken != $this->accessToken) {
                Logger::debug(__METHOD__.' clear token 时使用的token与cache中不同无需清除 cacheToken:'
                		.$oldToken.'  usedToken:'.$this->accessToken);
            	return;
            }
            $c1 = clearToken($this->appId);
            $c2 = true;
            if (function_exists("clearAuthorizerAccessToken")) {
                $c2 = clearAuthorizerAccessToken("COUPON", $this->appId);
            }
            if (!$c1 || !$c2) {
                Logger::error('wxapiclient clear token cache fail : clearToken:' . (int)$c1
                    . "  clearAuthorizerAccessToken:" . (int)$c2);
            }
        }
    }

    /**
     * 获取api请求错误
     *
     * @param  int $code
     * @return void
     */
    protected function _setError($code, $log_enabled = true) {
        //记录错误日志
        if ($log_enabled) $this->_log();

        $this->_error_code = $code;
        $this->_error_message = WX_Error::getMessage($code);
    }

    /**
     * 解析token
     *
     * @param  array $response
     * @return object WX_Token
     */
    protected function _parseToken($response) {
        if (!isset($response['access_token']) || !$response['access_token']) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return null;
        }
        return new WX_Token($response['access_token'], $response['expires_in']);
    }

    /**
     * 记录错误日志
     *
     * @author mxg
     * @return void
     */
    protected function _log() {
        $params = array();
        $params['data'] = WeiXinApiRequest::$params;
        $params['response'] = WeiXinApiRequest::$response;
        $params['app_id'] = $this->appId;
        $params['http_code'] = WeiXinApiRequest::$http_code;
        Logger::error('WeiXinCardApi last erorr, url: ' . WeiXinApiRequest::$url, $params);
    }

    /**
     * encrypt_code转为code
     * @author zhanglong
     * @param $code
     * @return mixed|null
     */
    public function encryptCode_decode($code) {
        //参数验证
        if (empty($code)) {
            return null;
        }
        $param = array('encrypt_code' => urlencode(str_replace(" ", "+", urldecode($code))));
        $url = $this->_getUrl('card/code/decrypt');
        //WeiXinApiRequest::$debug = 1;
        $response = WeiXinApiRequest::post($url, urldecode(json_encode($param)));
        //print_r($response);exit;
        return call_user_func_array(array($this, '_parse'),
            array(WeiXinApiRequest::$http_code, $response, '_parseCodeDecode'));
    }

    /**
     * encrypt_code转为code返回信息结果
     * @author zhanglong
     * @param  array $response
     * @return string|null
     */
    protected function _parseCodeDecode($response) {
        if (!isset($response['errcode']) || !isset($response['errmsg'])) {
            $this->_setError(WX_Error::RESPONSE_FOMAT_ERROR);
            return false;
        }
        if (0 == $response['errcode'] && strtolower($response['errmsg']) == 'ok') {
            return $response['code'];
        }
        return false;
    }

}