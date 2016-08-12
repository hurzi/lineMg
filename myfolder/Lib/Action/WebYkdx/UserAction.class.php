<?php
/**
 * 用户注册
 */

class UserAction extends YkdxCommonAction{
	private $userModel;
    public function __construct() {
        parent::__construct();
        $this->userModel = loadModel("User");
    }
    
    public function index(){
        //var_dump(get_included_files());exit;
        $openid = $this->getParam('openid');
                
        $user = $this->userModel->getUserByOpenid($openid);
        if($user){
        	redirect(url("User","Regist"),3,"未注册，自动跳转注册页");
        }
        
        $param['user_id'] = $user['user_id'];
        
        $evalList = $this->userEvalModel->getList($param);             
        $this->assign("evalList",$evalList);
        $this->assign("user",$user);
        $this->display();
    }
    
    /**
     * 注册
     */
    public function regist(){
    	
    	$this->display();
    }
    
    
    
    /**
     * 发送验证码
     */
    public function ajax_sendVolideCode(){
    	$userPhone = $this->getParam('user_phone');
    	if(!$userPhone){
    		printJson(0,1,"手机号不能为空");
    	}
    	$clientIp = getIp();
    	$ipCacheKey = GlobalCatchId::ABC_BASE_KEY."sms_".$clientIp;
    	$ipCacheCount = Factory::getCacher()->get($ipCacheKey);
    	if($ipCacheCount && $ipCacheCount>AbcConfig::SMS_IP_MAXCOUNT_DAY){
    		printJson(0,1,"一个IP一天不能提交".AbcConfig::SMS_IP_MAXCOUNT_DAY."次手机验证码");
    	}
    	//手机号限制
    	$phoneCacheKey = GlobalCatchId::ABC_BASE_KEY."sms_".$userPhone;
    	$phoneCacheCount = Factory::getCacher()->get($phoneCacheKey);
    	if($phoneCacheCount && $phoneCacheCount>0){
    	    printJson(0,1,"手机号1分钟内不能多次提交");
    	}
    	//手机号注册过
    	$userinfo = $this->userModel->getUserByPhone($userPhone);
    	if($userinfo){
    	    printJson(0,1,"该手机号已经注册过了.");
    	}
    	$smsCode = rand(100000, 999999);//短信验证码
    	//发送短信
    	if (C('IS_LOCAL')) {
    		$smsCode = 111111;
    	}else{
    		$msg = str_replace("__CODE__", $smsCode, C('SMS_MSG_TEMPLATE'));
    		//发送短信
    		$sendResult = sendSMSByAli($userPhone, '{\"code\":\"'+$smsCode+'\",\"product\":\"小何\"}');
    		if(!$sendResult){
    			printJson(0,1,"发送短信验证码失败");
    		}
    	}
    	 
    	//设置缓存
    	$codeCacheKey = GlobalCatchId::ABC_BASE_KEY."smscode_".$userPhone;
    	Factory::getCacher()->set($codeCacheKey,$smsCode,10*60);  //验证码
    	Factory::getCacher()->set($ipCacheKey,$ipCacheCount?($ipCacheCount+1):1,24*3600); //ip限制
    	Factory::getCacher()->set($phoneCacheKey,$phoneCacheCount?($phoneCacheCount+1):1,60);
    	 
    	printJson(1);
    }
    
    
    
    
    /**
     * 提交答题(整页显示答题的)
     */
    public function ajax_addUser(){
    	$openid = $this->getParam('openid','asdfiwotqwet');
    	$userPhone = $this->getParam('user_phone');
    	$valideCode = $this->getParam('valide_code');
    	$userNumber = $this->getParam('user_number');
    	$userName = $this->getParam('user_name');
    	$userAge = $this->getParam('user_age');
    	if(!$userPhone || !$valideCode){
    		printJson("",-1,"参数出错");
    	}
    	$codeCacheKey = GlobalCatchId::ABC_BASE_KEY."smscode_".$userPhone;
    	$validRet = Factory::getCacher()->get($codeCacheKey);
    	if(!$validRet){
    		printJson("",-1,"验证码错误");
    	}
    	$param = array(
    			"openid"=>$openid,
    			"user_phone"=>$userPhone,
    			"user_number"=>$userNumber,
    			"user_name"=>$userName,
    			"user_age"=>$userAge
    	);
    
    	$ret = $this->userModel->addUser($param);
    	if(!$ret){
    		printJson("",-1,"注册失败");
    	}
    	printJson("注册成功");
    }
    
    
}
