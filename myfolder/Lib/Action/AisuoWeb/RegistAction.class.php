<?php
class RegistAction extends Action
{
	private static $model;
	private static $catchkey;
	private static $openid;
	
	public function __construct()
	{
		parent::__construct();
		self::$model = M("AisuoWeb.Regist");
		self::$openid = getCurrOpenid(true);
		self::$catchkey = self::$openid."_ac_user_tmp";
	}
	
	
	private function getCacheParam(){
		$userdata = Factory::getCacher()->get(self::$catchkey);
		$this->assign('truthname',trim(@$userdata['truthname']));
		$this->assign('sex',@$userdata['sex']);
		$this->assign('mobile',@$userdata['mobile']);
		$this->assign('headimgurl',@$userdata['headimgurl']);
		
		return $userdata;
	}
		
	/**
	 * 注册首页
	 */
	public function index()
	{		
		//setcookie("TestCookie", "aaadfweteeee",time()+3600);
		//var_dump($_COOKIE);exit;
		$openid = getCurrOpenid(true);
		$truthname = $this->getParam("truthname");
		$sex = $this->getParam("sex");
		$province = $this->getParam("province");
		$city = $this->getParam("city");
		$mobile = $this->getParam("mobile");
		
		$regMobile = self::$model->getMobileByOpenid($openid);
		if($regMobile){
			$this->hasRegist();
			exit;
		}
		
		
		$this->getCacheParam();
		
		$this->assign('truthname',$truthname);	
		$this->assign('sex',$sex);	
		$this->assign('mobile',$mobile);	
		//获取头像
		$wxUserObj = AiSuoFactory::getApiClient()->getUser($openid);
		if($wxUserObj){
			$this->assign("wxObjImg",$wxUserObj->headimgurl);
			$this->assign('truthname',preg_replace("/\s+/",'',trim($wxUserObj->nickname)));	
			$this->assign('sex',$wxUserObj->sex);	
			$this->assign('province',$wxUserObj->province);	
			$this->assign('city',$wxUserObj->city);	
			$this->assign('mobile',$mobile);	
		}else{
			$this->assign("wxObjImg",null);
		}

		$this->display("Regist.index");
	}
	


	/**
	 * 已注册
	 */
	public function hasRegist(){
		$openid = getCurrOpenid(true);
		$regObj = self::$model->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，			
			//$this->display("Regist.notMember");
			header("location:".url("Regist","index"));
			exit;
		}
		$otherObj = array();
		if($regObj['as_status'] == 1){
			$otherObj = self::$model->getObjByOpenid($regObj['as_openid']);
			$astime = $regObj['as_lock_time'];
			
			$this->assign("otherTruthName",$otherObj['truthname']);
			$this->assign("lock_time",date('Y年m月d日',strtotime($astime)));
			$this->assign("as_durtion_day",floor((time()-strtotime($astime))/(3600*24)));
			Logger::info("--------------)))".(time()-strtotime($astime)));
			$durtionTime = (time() - strtotime($regObj['as_lock_time']));
			$hour =floor($durtionTime/(3600*24));
			$this->assign("durtionDay",$hour);
		}	
		
		$this->assign("asStatusStr",$regObj['as_status'] == 1 ? "恋爱" : "单身");
		$this->assign("regObj",$regObj);
		$this->assign("otherObj",$otherObj);
		$this->assign("asStatus",$regObj['as_status']);
		//$this->display();
		$this->display("Regist.hasRegist");
	
	}
	/**
	 * 注册提交
	 */
	public function ajax_step1submit(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		$regMobile = self::$model->getMobileByOpenid($openid);
		if($regMobile){
			printJson(0,1,'您已经注册过了');
		}
		
		$userdata = Factory::getCacher()->get(self::$catchkey);
		$truthname = trim($userdata["truthname"]);
		$sex =	trim($userdata["sex"]);
		$mobile = trim($userdata["mobile"]);
		$province =	trim($userdata["province"]);
		$city = trim($userdata["city"]);
		
		if(!$truthname ){
			printJson(0,1,'姓名不能为空');
		}
		if(!$sex ){
			printJson(0,1,'性别不能为空');
		}
		if(!$mobile){
			printJson(0,1,'没有验证手机号');
		}
	
		$data['openid'] = $openid;
		$data['truthname'] = $truthname;
		$data['sex'] = $sex == 0 ? 1:$sex;
		$data['mobile'] = $mobile;
		$data['province'] = $province;
		$data['city'] = $city;
		$data['as_code'] = mt_rand(100000, 999999);
		$data['create_time'] = date('Y-m-d H:i:s');
		$data['last_update_time'] = date('Y-m-d H:i:s');
		
		$result = self::$model->saveOrUpdateAsUser($data,false);
		if(!$result){
			printJson(0,1,'注册失败请稍后再试');
		}
		printJson(array("ascode"=>$data['as_code']));
	}
	
	/**
	 * 第二个页面的初始化
	 */
	public function ajax_step1_data(){
		$truthname = $this->getParam("truthname");
		$sex = $this->getParam("sex");
		$province = $this->getParam("province");
		$city = $this->getParam("city");
		$headimgurl = $this->getParam("headimgurl");
		$userdata['truthname'] = $truthname;
		$userdata['sex'] = $sex;
		$userdata['headimgurl'] = $headimgurl;
		$userdata['province'] = $province;
		$userdata['city'] = $city;
		
		Factory::getCacher()->set(self::$catchkey,$userdata,60*10);
		
		//var_dump($userdata);exit;
		
		$jumpUrl = url('Regist','step2init');
		printJson(array("jumpUrl"=>$jumpUrl));
	}
	
	
	/**
	 * 第二个页面的初始化
	 */
	public function step2init(){
		$this->getCacheParam();
		
		$this->display("Regist.step2");
	}
	
	/**
	 * 第二个页面的发送验证码
	 */
	public function ajax_step2_sendcode(){
		$openid = getCurrOpenid(false);
		if(!$openid){
			printJson(0,1,'用户未登录!'.$openid);
		}
		
		$mobile = $this->getParam("mobile");
		
		if(!$mobile){
			printJson(0,1,'手机号不能为空!');
		}
		
		$openid = self::$model->getOpenidByMobile($mobile);
		if($openid){
			printJson(0,1,'此号码已注册，不能重复申请!');
		}

		$mcount = Factory::getCacher()->get("sendcode_".$mobile);
		if($mcount && $mcount>=AbcConfig::SMS_MAXCOUNT_DAY){
			printJson(0,1,'此号码已超过每天发送'.AbcConfig::SMS_MAXCOUNT_DAY.'次限制');
		}
		
		//生成随机码
		$sendCode = mt_rand(100000, 999999);		
		//$msg = "【爱锁】您的短信验证码为:".$sendCode;
		$msg = "Hello，您注册爱锁的验证码是：".$sendCode."，请确认为本人操作并回到微信端提交申请。";
		$sendResult = $this->sendSMS($mobile,$msg);
		Logger::info("发送短信$mobile,----$sendCode,$msg,result:".$sendResult);
		if($sendResult != 0){
			printJson(0,1,'发送验证码失败!稍后再试!');
		}
		Factory::getCacher()->set("sendcode_".$mobile,($mcount?$mcount:0)+1,24*3600);
		
		$userdata = Factory::getCacher()->get(self::$catchkey);
		$userdata['tmpmobile'] = $mobile;
		$userdata['sendCode'] = $sendCode;
		$userdata['sendCodeTime'] = time();
		Factory::getCacher()->set(self::$catchkey,$userdata,60*60*2);
		
		printJson(2,0,$sendCode);
	}
	
	public function testSendSms(){
		$sendResult = $this->sendSMS('15101019215','【爱锁】验证码为12345,10分钟后失效');
		var_dump($sendResult);
	}
	
	/**
	 * 第二个页面的验证验证码
	 */
	public function ajax_step2_check(){
		$mobile = $this->getParam("mobile");
		$code = $this->getParam("code");
		
		if(!$mobile){
			printJson(0,1,'手机号不能为空!');
		}
		if(!$code){
			printJson(0,1,'验证码不能为空!');
		}
	
		$userdata = Factory::getCacher()->get(self::$catchkey);
		if(!$userdata || !@$userdata['sendCode']){
			printJson(0,1,'您还没有发过验证码!');
		}
		if(time() - $userdata['sendCodeTime']>10*60){
			printJson(0,1,'验证码已失效!');
		}
		if($code!=$userdata['sendCode']){
			printJson(0,1,'验证码错误!');
		}
		$userdata['mobile'] = $userdata['tmpmobile'];
		Factory::getCacher()->set(self::$catchkey,$userdata,60*10);
		
		$jumpUrl = url('Regist','step3init');
		printJson(array("jumpUrl"=>$jumpUrl));
	}
	

	/**
	 * 第三个页面的初始化
	 */
	public function step3init(){
		$this->getCacheParam();
	
		$this->display("Regist.step3");
	}
	

	/**
	 * 非会员
	 */
	public function notMember(){
		$this->display();
	}
	
	/**
	 * 注册提交
	 */
	public function ajax_step3submit(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		$regMobile = self::$model->getMobileByOpenid($openid);
		if($regMobile){
			printJson(0,1,'您已经注册过了');
		}
	
		$userdata = Factory::getCacher()->get(self::$catchkey);
		$truthname = $userdata["truthname"];
		$sex =	$userdata["sex"];
		$mobile = $userdata["mobile"];
		$province =	$userdata["province"];
		$city = $userdata["city"];
		
		if(!$truthname ){
			printJson(0,1,'姓名不能为空');
		}
		if(!$sex ){
			printJson(0,1,'性别不能为空');
		}
		if(!$mobile){
			printJson(0,1,'没有验证手机号');
		}
		
		$data['openid'] = $openid;
		$data['truthname'] = $truthname;
		$data['sex'] = $sex;
		$data['province'] = $province;
		$data['city'] = $city;
		$data['mobile'] = $mobile;
		$data['as_code'] = $this->getAsCode();
		$data['create_time'] = date('Y-m-d H:i:s');
		$data['last_update_time'] = date('Y-m-d H:i:s');
		//获取头像
		$wxUserObj = @AiSuoFactory::getApiClient()->getUser($openid);
		if($wxUserObj){
			$data['headimgurl'] = $wxUserObj->headimgurl;
		}else{
			$data['headimgurl'] = AbcConfig::BASE_WEB_DOMAIN_PATH."AisuoWeb/images/aslogo.jpg";
		}
		
		$result = self::$model->saveOrUpdateAsUser($data,false);
		if(!$result){
			printJson(0,1,'注册失败请稍后再试');
		}
		printJson(array("ascode"=>$data['as_code'],"jumpUrl"=>url("AsStatus","index")));
	}
	
	/**
	 * 生成邮政编码
	 */
	function getAsCode(){
		$code = mt_rand(100000, 999999);
		$lifeCode = array("000000","111111",'222222','333333','444444','555555','666666','777777','888888','999999');
		$maxTryCount = 0;
		while($maxTryCount>3){
			$code = mt_rand(100000, 999999);
			if(in_array($code, $lifeCode)){
				$maxTryCount += 1;
				continue;
			}else{
				break;
			}
		}
		return $code;
	}
	
	/**
	 * 发送短信
	 * @param unknown $phone
	 * @param unknown $content
	 * @return Ambigous <number, mixed, boolean, string, unknown>
	 */
	private function sendSMS($phone,$content){
		/**
		 * 定义程序绝对路径
		 */
		define ( 'SCRIPT_ROOT', dirname ( __FILE__ ) . '/../../Common/sms/' );
		require_once SCRIPT_ROOT . 'include/Client.php';
		
//		/**
//		 * 网关地址
//		 */
//		$gwUrl = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService?wsdl';
//		
//		/**
//		 * 序列号,请通过亿美销售人员获取
//		 */
//		$serialNumber = '0SDK-EBB-0130-NEWRN';
//		
//		/**
//		 * 密码,请通过亿美销售人员获取
//		 */
//		$password = '879635';
//		
//		/**
//		 * 登录后所持有的SESSION KEY，即可通过login方法时创建
//		 */
//		$sessionKey = '879635';
 		/**
 		 * 网关地址
 		 */
 		//$gwUrl = 'http://sdk999ws.eucp.b2m.cn:8080/sdk/SDKService';
 		$gwUrl = AbcConfig::SMS_WG_URL;
 		/**
 		 * 序列号,请通过亿美销售人员获取
 		 */
 		//$serialNumber = '0SDK-EBB-0130-NEWRN';
 		$serialNumber =AbcConfig::SMS_SERIAL_NUMBER;
 		/**
 		 * 密码,请通过亿美销售人员获取
 		 */
 		//$password = '879635';
 		$password = AbcConfig::SMS_PW;
 		/**
 		 * 登录后所持有的SESSION KEY，即可通过login方法时创建
 		 */
 		//$sessionKey = '510288';
 		$sessionKey = AbcConfig::SMS_SESSION_KEY;
		/**
		 * 连接超时时间，单位为秒
		 */
		$connectTimeOut = 2;
		
		/**
		 * 远程信息读取超时时间，单位为秒
		 */
		$readTimeOut = 10;
		
		/**
		 $proxyhost		可选，代理服务器地址，默认为 false ,则不使用代理服务器
		 $proxyport		可选，代理服务器端口，默认为 false
		 $proxyusername	可选，代理服务器用户名，默认为 false
		 $proxypassword	可选，代理服务器密码，默认为 false
		 */
		$proxyhost = false;
		$proxyport = false;
		$proxyusername = false;
		$proxypassword = false;
		
		$client = new Client ( $gwUrl, $serialNumber, $password, $sessionKey, $proxyhost, $proxyport, $proxyusername, $proxypassword, $connectTimeOut, $readTimeOut );
		/**
		 * 发送向服务端的编码，如果本页面的编码为GBK，请使用GBK
		 */
		$client->setOutgoingEncoding ( "UTF-8" );
		
		//$statusCode = $client->login ();
		/**
		 * 下面的代码将发送内容为 test 给 159xxxxxxxx 和 159xxxxxxxx
		 * $client->sendSMS还有更多可用参数，请参考 Client.php
		 */
		//$phone='13810569561';
		//$content='【爱锁】验证码为12345,10分钟后失效';
		$statusCode = $client->sendSMS ( array ($phone ), $content );
		//var_dump($client->getError());exit;
		//var_dump($statusCode);
		//var_dump($client->getBalance ());exit;
		return $statusCode;
	}
}
