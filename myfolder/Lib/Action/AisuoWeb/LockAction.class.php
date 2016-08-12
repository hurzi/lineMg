<?php
class LockAction extends Action
{
	private static $modelApply;
	private static $modelUser;
	private static $modelMessage;
	private static $catchkey;
	private static $openid;
	
	public function __construct()
	{
		parent::__construct();
		self::$modelUser = M("AisuoWeb.Regist");
		self::$modelApply = M("AisuoWeb.Lock");
		self::$modelMessage = M("AisuoWeb.Message");
		self::$openid = getCurrOpenid(true);
		self::$catchkey = self::$openid."_ac_apply_tmp";
	}
	
		
	/**
	 * 申请首页
	 */
	public function index()
	{		
		$openid = getCurrOpenid(true);
		
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，			
			//$this->display("Lock.notMember");
			//$this->display("Regist.index");	
			header("location:".url("Regist","index"));		
			exit;
		}
		if($regObj['as_status'] == 1){
			//已经配对
			$this->display("Lock.hasLock");
			exit;
		}
		
		//获取头像
// 		$wxUserObj = @AiSuoFactory::getApiClient()->getUser($openid);
// 		if($wxUserObj){
// 			$regObj['truthname']=$wxUserObj->nickname;
// 			$regObj['sex']=$wxUserObj->sex;
// 			$regObj['headimgurl'] = $wxUserObj->headimgurl;
// 			self::$modelUser->saveOrUpdateAsUser($regObj,true);
// 		}
		
		$this->assign("selfObj",$regObj);
			
		$this->display("Lock.index");
	}
	

	/**
	 * 配对提交
	 */
	public function ajax_submit(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		$mobile = $this->getParam("mobile");
		$iscontinue = $this->getParam("iscontinue");
		if(!$mobile){
			printJson(0,1,'手机号不能为空');
		}
		//自己的状态
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
			exit;
		}
		if($regObj['as_status'] == 1){
			//已经配对
			printJson(0,1,'您已经绑定成功了');
			exit;
		}
		if($regObj['mobile'] == $mobile){
			printJson(0,1,'您不能输入自己的手机号');
			exit;
		}
		$lastApplyObj = self::$modelApply->getLastObjByOpenid($openid);
		if($lastApplyObj && $lastApplyObj['apply_time'] && (time() - strtotime($lastApplyObj['apply_time']))< AbcConfig::APPLY_TIME_DURTION * 3600){
			printJson(0,1,AbcConfig::APPLY_TIME_DURTION.'小时内不能重复申请');
		}
		//对方的状态
		$otherUserObj = self::$modelUser->getObjByMobile($mobile);
		if (!$otherUserObj && !$iscontinue) {
			printJson(0,1,"此手机号未注册爱锁会员");
			//printJson(array("mobile"=>$mobile),2,"您填写的手机号码{$mobile}不是爱锁会员，去召唤他(她)吧");
		}
		if($otherUserObj['as_status']==1){
			printJson(0,1,"您填写的{$mobile}<br/>已经绑定");
		}
		$otherApplyObj = null;
		if($otherUserObj){
			$otherApplyObj = self::$modelApply->getLastObjByOpenid($otherUserObj['openid']);
		}
		$lockStatus = 0;
		//直接配对成功
		if($otherUserObj && $otherApplyObj && 
			 $otherUserObj['as_status']!=1 && 
			 $regObj['as_status']!=1 &&
			 $otherApplyObj['status'] == 0 &&
			 $otherApplyObj['as_mobile'] == $regObj['mobile'] &&
			 $mobile == $otherUserObj['mobile'] ){
			$lockStatus = 1;
		}
		if($lockStatus){
			//更新自己的状态
			$regObj['as_status'] = 1;
			$regObj['as_uid'] = $otherUserObj['uid'];
			$regObj['as_openid'] = $otherUserObj['openid'];
			$regObj['as_lock_time'] = date('Y-m-d H:i:s');
			$regObj['as_unlock_time'] = null;
			$regObj['last_update_time'] = date('Y-m-d H:i:s');
			self::$modelUser->saveOrUpdateAsUser($regObj,true);
			//更新对方的状态
			$otherUserObj['as_status'] = 1;
			$otherUserObj['as_uid'] = $regObj['uid'];
			$otherUserObj['as_openid'] = $regObj['openid'];
			$otherUserObj['as_lock_time'] = date('Y-m-d H:i:s');
			$otherUserObj['last_update_time'] = date('Y-m-d H:i:s');
			self::$modelUser->saveOrUpdateAsUser($otherUserObj,true);
			//更新对方的申请状态
			$otherApplyObj['status'] =1;
			$otherApplyObj['over_time'] = date('Y-m-d H:i:s');
			self::$modelApply->saveOrUpdateAsApplay($otherApplyObj,true);
			//创建自己的状态
			$data['openid'] = $openid;
			$data['uid'] = $regObj['uid'];
			$data['as_mobile'] = $mobile;
			$data['status'] = 1;
			$data['over_time'] = date('Y-m-d H:i:s');
			$data['apply_time'] = date('Y-m-d H:i:s');
			self::$modelApply->saveOrUpdateAsApplay($data);
			//清除自己的私信发送
			//self::$modelMessage->clearMessage($regObj['uid']);
			
			//发给自己消息
			$sendMsg['type'] = "news";
			$sendMsg['to_users'] = $regObj['openid'];
			$sendMsg['articles'][0]['title'] = "恭喜！锁定已成功！";
			$sendMsg['articles'][0]['description'] = "恭喜！您与".$otherUserObj['truthname']."锁定成功！";
			$sendMsg['articles'][0]['picurl'] = "http://ww4.sinaimg.cn/bmiddle/58a342d7tw1eii2ubhzepj20a005kq3k.jpg";
			//$sendMsg['articles'][0]['url'] = url("Message","index");
			$sendMsg['articles'][0]['url'] = url("Lock","lockSuccess",array("openid"=>$regObj['openid'],"otherUid"=>$otherUserObj['uid'],"selfUid"=>$regObj['openid']));
			AiSuoFactory::getSendMessage()->send($sendMsg);
			
			//发给对方消息
			$sendMsg['type'] = "news";
			$sendMsg['to_users'] = $otherUserObj['openid'];
			$sendMsg['articles'][0]['title'] = "恭喜！锁定已成功！";
			$sendMsg['articles'][0]['description'] = "恭喜！您与".$regObj['truthname']."锁定成功！";
			$sendMsg['articles'][0]['picurl'] = "http://ww4.sinaimg.cn/bmiddle/58a342d7tw1eii2ubhzepj20a005kq3k.jpg";
			//$sendMsg['articles'][0]['url'] = url("Message","index");
			$sendMsg['articles'][0]['url'] = url("Lock","lockSuccess",array("openid"=>$otherUserObj['openid'],"otherUid"=>$regObj['uid'],"selfUid"=>$otherUserObj['openid']));
			AiSuoFactory::getSendMessage()->send($sendMsg);
				
			//设置对方之前的消息对我可见
			self::$modelMessage->updateMessageToMe($regObj['uid'],$otherUserObj['uid'],empty($otherUserObj['as_unlock_time'])?'2014-01-01':$otherUserObj['as_unlock_time']);
			//设置我之前的消息对方可见
			self::$modelMessage->updateMessageToMe($otherUserObj['uid'],$regObj['uid'],empty($regObj['as_unlock_time'])?'2014-01-01':$regObj['as_unlock_time']);
			
			//清空所有发给我的私信
			self::$modelMessage->clearSendToMeMessage($regObj['uid']);
			//清空所有发给对方的私信
			self::$modelMessage->clearSendToMeMessage($otherUserObj['uid']);
				
			$jumpUrl = url("Lock","lockSuccess",array("otherUid"=>$otherUserObj['uid']));
			printJson(array("jumpUrl"=>$jumpUrl));
		}else{
			$data['openid'] = $openid;
			$data['uid'] = $regObj['uid'];
			$data['as_mobile'] = $mobile;
			$data['status'] = 0;
			$data['apply_time'] = date('Y-m-d H:i:s');
			
			$result = self::$modelApply->saveOrUpdateAsApplay($data,false);
			if(!$result){
				printJson(0,1,'申请失败请稍后再试');
			}
			$jumpUrl = url("Lock","applySuccess");
			printJson(array("jumpUrl"=>$jumpUrl));
		}	
		
	}
	
	/**
	 * 锁定成功
	 */
	public function lockSuccess(){
		$openid = getCurrOpenid();
		$otherUid = $this->getParam("otherUid");
		if(!$openid ){
			echo "error";
			exit;
		}
		if(!$otherUid){
			echo "error";
			exit;
		}
		$otherUserObj = self::$modelUser->getObjByUid($otherUid);
		$userObj = self::$modelUser->getObjByOpenid($openid);
		
		
		$this->assign("othername",$otherUserObj['truthname']);
		$this->assign("othersex",$otherUserObj['sex']==2?'女':'男');
		$this->assign("otherObj",$otherUserObj);
		$this->assign("selfname",$userObj['truthname']);
		$this->assign("selfsex",$userObj['sex']==2?'女':'男');
		$this->assign("selfObj",$userObj);
		$this->display();
	}
	
	/**
	 * 申请成功
	 */
	public function applySuccess(){
		$this->display();
	}
	
}