<?php
class MessageAction extends Action
{
	private static $modelMessage;
	private static $modelUser;
	private static $catchkey;
	private static $openid;
	
	public function __construct()
	{
		parent::__construct();
		self::$modelUser = M("AisuoWeb.Regist");
		self::$modelMessage = M("AisuoWeb.Message");
		self::$openid = getCurrOpenid(true);
		self::$catchkey = self::$openid."_ac_message_tmp";
	}
	
		
	/**
	 * 寄信首页
	 */
	public function index()
	{		
		$openid = getCurrOpenid(true);
				
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，			
			//$this->display("Regist.notMember");
			//header("location:".url("Regist","index"));
			header("location:".url("Regist","index"));
			exit;
		}
		//锁定后解锁后直接跳收信箱
		if($regObj['as_status'] == 2){
			header("location:".url("Message","inbox"));
			exit;
		}
		//未申请锁定
		if($regObj['as_status'] != 1){
			$this->display("Message.notLock");
			exit;
		}
		
		$otherUserObj = self::$modelUser->getObjByOpenid($regObj['as_openid']);
		$this->assign("otherObj",$otherUserObj);
		

		//获取头像
// 		$wxUserObj = @AiSuoFactory::getApiClient()->getUser($openid);
// 		//var_dump($wxUserObj);exit;
// 		if($wxUserObj){
// 			$regObj['truthname']=$wxUserObj->nickname;
// 			$regObj['sex']=$wxUserObj->sex;
// 			$regObj['headimgurl'] = $wxUserObj->headimgurl;
// 			self::$modelUser->saveOrUpdateAsUser($regObj,true);
// 		}
		
		$this->assign("ascode",$regObj['as_code']);
		$this->assign("selfObj",$regObj);
		$this->assign("currDate",date('Y.m.d'));
		$this->display("Message.index");
	}
	
	/**
	 * 收信箱首页
	 */
	public function inbox()
	{
		$openid = getCurrOpenid(true);
	
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			//$this->display("Regist.notMember");
			header("location:".url("Regist","index"));
			exit;
		}
		if(!$regObj){
			//还不是会员，
			$this->display("Regist.notMember");
			//header("location:".url("Regist","index"));
			exit;
		}
		//未申请锁定
		if($regObj['as_status'] == 0){
			$this->display("Message.notLock");
			//header("location:".url("Message","inbox"));
			exit;
		}
		
		//收信箱个数
		$inboxCount = self::$modelMessage->getCountToMe($regObj['uid']);
		if($regObj['as_status'] == 0){
			$inboxCount = 0;
		}
		
		//锁定状态不能查看
		if($regObj['as_status'] <> 2){		
			$otherUserObj = self::$modelUser->getObjByOpenid($regObj['as_openid']);
			
			$this->assign("inboxCount",$inboxCount);
			$this->assign("regObj",$regObj);
			$this->assign("otherObj",$otherUserObj);
			$this->display("Message.inbox_lockStatus");
			exit;
		}
		
		$this->assign("ascode",$regObj['as_code']);
		$this->assign("currDate",date('Y.m.d'));
		$this->assign("inboxCount",$inboxCount);
		$this->assign("regObj",$regObj);
		$this->display("Message.inbox");
	}
	
	/**
	 * 获取信件
	 */
	public function ajax_inboxList(){
		$openid = getCurrOpenid(true);
		$pagesize = $this->getParam("pagesize",AbcConfig::PAGE_SIZE);
		$pageindex = $this->getParam("pageindex",1);
		$pageindex = $pageindex<1?1:$pageindex;
		
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
			exit;
		}
		//锁定状态不能查看
		if($regObj['as_status'] <> 2){
			printJson(0,1,'非解锁状态，不能查看信件');
		}
		
		$msgList = self::$modelMessage->getListToMe($regObj['uid'],$pagesize,$pageindex);
		$hasNext = (!$msgList || count($msgList)<$pagesize)?0:1;
		printJson(array("hasNext"=>$hasNext,"list"=>$msgList));
	}

	/**
	 * 提交信件
	 */
	public function ajax_submit(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		$content = trim($this->getParam("content",null,false));
		if(!$content){
			printJson(0,1,'内容不能为空');
		}
		//自己的状态
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
		}		
// 		if($regObj['as_status'] != 1){
// 			//已经配对
// 			printJson(0,1,'您还未申请锁定');
// 			exit;
// 		}
		
		$data['openid'] = $openid;
		$data['uid'] = $regObj['uid'];
		$data['message_code'] = date('YmdHis');
		$data['to_uid'] = $regObj['as_uid'];
		$data['content'] = $content;
		$data['status'] = 0;
		$data['create_time'] = date('Y-m-d H:i:s');
		
		$result = self::$modelMessage->saveMessage($data);
		if(!$result){
			printJson(0,1,'寄信失败，请稍后再试');
		}
		printJson(1,0,"私信已投递");
		
	}
	
	
}