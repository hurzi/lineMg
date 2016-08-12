<?php
/**
 * 公共页首页
 */
include_once dirname(__FILE__).'/YkdxUHome.php';
class YkdxCommonAction extends Action{
	protected $_notNeedOauthActions = array(
			'Login.index' => 1,
			'Login.oauth' => 1,
			'Login.wxCallback' => 1,
			'Login.logout' => 1,
	);
	protected $_notNeedRegistActions = array(
			'User.ajax_sendVolideCode' => 1,
			'User.ajax_addUser' =>1,
			'User.regist' => 1,
			'Login.index' => 1,
			'Login.oauth' => 1,
			'Login.wxCallback' => 1,
			'Login.logout' => 1,
	);
	//登陆状态禁止访问页面
	protected $_notLoginActions = array(
			'User.regist' => 1,
	);
	
    public function __construct() {
        parent::__construct();  
        YkdxUHome::init();
        //$this->checkOauth(); //检查授权状态
        $this->checkRegist(); //检查注册状态        
    }
    
    public function showErrorH5($msg){
        //var_dump(get_included_files());exit;
        if(!$msg){
        	$msg = "系统异常，请稍后再试";
        }
        $this->display();
        exit;
    }
    
        
    /**
     * 检查授权
     */
    public function checkOauth () {    	 
    	$action = __ACTION_NAME__.'.'.__ACTION_METHOD__;
    	if (@$this->_notNeedOauthActions[$action]) {
    		return;
    	} 
    	
    	if (!YkdxUHome::isLogin()) {
    		if (isAjax()) {
    			printJson('', -100, '请先授权后进入');
    		}
    		$getParam = $this->getParam();
    		unset($getParam [ThirdPartyReqParams::SIG]);
    		unset($getParam [ThirdPartyReqParams::OPEN_ID]);
    		unset($getParam [ThirdPartyReqParams::APP_ID]);
    		unset($getParam [ThirdPartyReqParams::TIMESTAMP]);
    		$jumpurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    		$callbackUrl = resetUrl($jumpurl,$getParam);
    		$getParam['callback'] = urlencode($callbackUrl);
    		redirect(url("Login", 'oauth',$getParam));//去授权    		
    	}    	
    } 

    /**
     * 检查注册
     */
    public function checkRegist () {
    	$action = __ACTION_NAME__.'.'.__ACTION_METHOD__;
    	if (@$this->_notNeedRegistActions[$action]) {
    		return;
    	}    	 
    	if (!YkdxUHome::isRegist()) {
    		if (isAjax()) {
    			printJson('', -100, '请先注册后进入');
    		}
    		echo "9999999999";exit;
    		redirect(url("User", 'regist',$this->getParam()));//去授权
    	}
    }
}
