<?php
/**
 * 管理后台首页
 */

include_once LIB_PATH . '/../AbcPHP/API/WxPay/Tools/WxPay.JsApiPay.php';
include_once LIB_PATH . '/../AbcPHP/Common/RequestClient.class.php';
class WxPayAction extends Action{
private $app_info;
    public function __construct() {
        parent::__construct();
    }
    public function qr(){
    	$shopId = $this->getParam('shopId',0);
    	$extId = $this->getParam('extId',0);
    	$ywType = $this->getParam('ywType',0);
    	
    	$url = WEB_PATH.'/Test/index.php?a=WxPay&shopId='.$shopId."&extId=".$extId."&ywType=".$ywType;
    	
    	$qrUrl = WEB_PATH.'/Common/qr.php?url='.urlencode($url);
    	header("location:".$qrUrl);
    	
    }
    
    private function redirectOauth(){    	
    	$jumpurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];
    	$url = getAuthUrl($jumpurl, 'snsapi_base');
    	header("Location: $url");
    }
    
    public function index(){
    	Logger::error("------------------test log");
    	redirect(url("WxPay","money",$this->getParam()));exit;
        //var_dump(get_included_files());exit;
        $openId = $this->getParam('openid');
        $shopId = $this->getParam('shopId',0);
        $extId = $this->getParam('extId',0);
        $ywType = $this->getParam('ywType',0);
        
        
        checkAndOauth();//检查是不是正常获取的openid,否则自动授权获取
        
        $attach = array("shopId"=>$shopId,"extId"=>$extId,"ywType"=>$ywType);
            
        $jsApiParameters = $this->addUnifiedOrder($openId, 1, $attach);
        if($jsApiParameters){
            $this->assign('jsApiParameters',$jsApiParameters);
            $this->display();
        }else{
          	echo "微信支付预下单失败，请重试。";
        }
    }
    
    /**
     * 自由付费测试
     */
    public function money(){
    	//var_dump(get_included_files());exit;
    	$openId = $this->getParam('openid');
    	$shopId = $this->getParam('shopId',0);
    	$extId = $this->getParam('extId',0);
    	$ywType = $this->getParam('ywType',0);
    	
    	checkAndOauth();//检查是不是正常获取的openid,否则自动授权获取
    	
    	$this->assign('shopId',$shopId);
    	$this->assign('extId',$extId);
    	$this->assign('openid',$openId);
    	$this->assign('ywType',$ywType);
    	$this->display();
    }
    
    /**
     * 进行预下单
     */
    public function ajax_addUnifiedOrder(){
    	$price = (int)$this->getParam("price",0);
        $shopId = $this->getParam('shopId',0);
        $extId = $this->getParam('extId',0);
    	$ywType = $this->getParam('ywType',0);
        $openId = $this->getParam('openId','');
        if(!$openId){
        	printJson("",-1,"没有取到openid");
        }
        if(!$price){
        	printJson("",-1,"没有设置价格");
        }
        
        $attach = array("shopId"=>$shopId,"extId"=>$extId,"ywType"=>$ywType);
        
        $jsApiParameters = $this->addUnifiedOrder($openId, $price, $attach);
        if($jsApiParameters){        	
        	$result = array(
        			"jsApiParameters"=>$jsApiParameters,
        	);
        	printJson($result);
        }else{
        	printJson("",-1,"微信支付预下单失败，请重试。");
        }
    }
    
    /**
     * 
     * @param unknown $openid
     * @param unknown $attach
     */
    private function addUnifiedOrder($openid,$price,$attach){
    	
    	$input = new WxPayUnifiedOrder();
    	$input->SetBody("test");
    	$input->SetAttach(json_encode($attach));
    	$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
    	$input->SetTotal_fee((String) (int)$price);
    	$input->SetTime_start(date("YmdHis"));
    	$input->SetTime_expire(date("YmdHis", time() + 600));
    	$input->SetGoods_tag("test");
    	$input->SetNotify_url(WxPayConfig::NOTIFY_URL);
    	$input->SetTrade_type("JSAPI");
    	$input->SetOpenid($openid);
    	$input->SetAppid(WxPayConfig::APPID);//公众账号ID
    	$input->SetMch_id(WxPayConfig::MCHID);//商户号
    	$input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
    	$input->SetNonce_str(getRandStr(32));//随机字符串
    	$input->SetNotify_url(WEB_PATH.'/Test/index.php?a=WxPayNotify');//
    	//签名
    	$input->SetSign(WxPayConfig::KEY);
    	$order = WxPayApi::unifiedOrder($input,WxPayConfig::KEY);
    	if($order){
    		$tools = new JsApiPay();
    		$jsApiParameters = $tools->GetJsApiParameters($order,WxPayConfig::KEY);
    		Logger::debug('jsApiParameters:',$jsApiParameters);
    		return $jsApiParameters;
    	}else{
    		return false;
    	}
    }
    
    /**
     * 模拟支付成功，并发送通知
     */
    public function moniNotify(){
    	$data = $this->getParam();
    	Logger::debug('-----------------模拟支付后开始抛送订单数据');
    	if(!$data['openid'] || strlen($data['openid'])!=28 || !$data['total_fee']){
    		Logger::debug('模拟支付异常,参数中openid和total_fee不合法',$data);
    		printJson("",-1,"模拟操作失败!");
    	}
    	 
    	//获得特定的参数
    	$param['trade_no'] = date('YmdHms').getRandStr(10);  	//订单流水
    	$param['trade_time'] = time();		//订单完成时间
    	$param['shopId'] = $data['shopId'];			//商户号
    	$param['extId'] = $data['extId'];				//扩展ID(员工编号)
    	$param['isFirstTrake'] = $data['isNewUser']=="true"?true:false;  				//是否新用户首单
    	$param['uid'] = $data['openid'];  				//用户ID
    	$param['trade_fee'] = $data['total_fee'];  		//总金额
    	
//     	if($data['ywType'] == 1){
//     		$noticeUrl = 'http://test.hzc.socialjia.com/Gratuity/Notice/index.php?a=Cofco&m=index';
//     	}else{
//     		$noticeUrl = 'http://test.hzc.socialjia.com/Gratuity/Notice/index.php?a=Index&m=index';
//     	}
//     	$reqResult = RequestClient::request($noticeUrl,"POST",$param);
//     	Logger::debug('-----------------请求结束,['.$noticeUrl.']  Hezq request Gratuity result:'.$reqResult,$param);
    	printJson("模拟操作成功");
    }
}
