<?php
class UnLockAction extends Action
{
	private static $modelApply;
	private static $modelUser;
	private static $catchkey;
	private static $openid;
	
	public function __construct()
	{
		parent::__construct();
		self::$modelUser = M("AisuoWeb.Regist");
		self::$modelApply = M("AisuoWeb.UnLock");
		self::$openid = getCurrOpenid(true);
		self::$catchkey = self::$openid."_as_apply_tmp";
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
			//$this->display("UnLock.notMember");
			header("location:".url("Regist","index"));
			exit;
		}
		if($regObj['as_status'] == 2){
			//已经解除
			$this->display("UnLock.hasUnLock");
			exit;
		}
		
		if($regObj['as_status'] == 0){
			//未锁定
			$this->display("UnLock.notLock");
			exit;
		}
		
// 		//获取头像
// 		$wxUserObj = @AiSuoFactory::getApiClient()->getUser($openid);
// 		//var_dump($wxUserObj);exit;
// 		if($wxUserObj){
// 			$regObj['truthname']=$wxUserObj->nickname;
// 			$regObj['sex']=$wxUserObj->sex;
// 			$regObj['headimgurl'] = $wxUserObj->headimgurl;
// 			self::$modelUser->saveOrUpdateAsUser($regObj,true);
// 		}
		
		
		$lastApplyObj = self::$modelApply->getLastObjByOpenid($openid);
		if($lastApplyObj && $lastApplyObj['apply_time'] && (time() - strtotime($lastApplyObj['apply_time']))< AbcConfig::APPLY_TIME_DURTION * 3600){
			header("location:".url("UnLock","applySuccess"));
			exit;
		}
		if($lastApplyObj && $lastApplyObj['status'] == 0 
			&& (time() - strtotime($lastApplyObj['apply_time']))> AbcConfig::APPLY_TIME_DURTION * 3600){
			header("location:".url("UnLock","confirmIndex"));
			exit;
		}
		$this->display("UnLock.index");
	}
	

	/**
	 * 配对提交
	 */
	public function ajax_submit(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		//自己的状态
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
			exit;
		}
		if($regObj['as_status'] != 1){
			//已经配对
			printJson(0,1,'您还未绑定，请先申请爱锁');
			exit;
		}
		$lastApplyObj = self::$modelApply->getLastObjByOpenid($openid);
		if($lastApplyObj && $lastApplyObj['apply_time'] && (time() - strtotime($lastApplyObj['apply_time']))< AbcConfig::APPLY_TIME_DURTION * 3600){
			printJson(0,1,AbcConfig::APPLY_TIME_DURTION.'小时内不能重复申请');
		}
		
		$data['openid'] = $openid;
		$data['uid'] = $regObj['uid'];
		$data['status'] = 0;
		$data['apply_time'] = date('Y-m-d H:i:s');
			
		$result = self::$modelApply->saveOrUpdateAsUnLockApply($data,false);
		if(!$result){
			printJson(0,1,'申请解锁失败请稍后再试');
		}
		$jumpUrl = url("UnLock","applySuccess");
		printJson(array("jumpUrl"=>$jumpUrl));		
	}
	
	/**
	 * 取消提交确认
	 */
	public function ajax_submit_cancel(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		//自己的状态
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
			exit;
		}
		if($regObj['as_status'] != 1){
			//已经配对
			printJson(0,1,'您还未绑定，请先申请爱锁');
			exit;
		}
		$lastApplyObj = self::$modelApply->getLastObjByOpenid($openid);
		if(!$lastApplyObj){
			printJson(0,1,'您还没有提交解锁申请');
		}
		if($lastApplyObj['status'] == 1){
			printJson(0,1,'您已确认解锁');
		}
		if($lastApplyObj['status'] == 2){
			printJson(0,1,'您已解锁成功');
		}
		if($lastApplyObj['status'] == -1){
			printJson(0,1,'您已取消解锁申请');
		}
		if($lastApplyObj['status'] != 0){
			printJson(0,1,'确认无效');
		}
		
		//自己的申请状态改变
		$lastApplyObj['status'] = -1;
		self::$modelApply->saveOrUpdateAsUnLockApply($lastApplyObj,true);
		$jumpUrl = url("UnLock","cancelSuccess");
		printJson(array("jumpUrl"=>$jumpUrl));
	}
	
	/**
	 * 解锁提交确认
	 */
	public function ajax_submit_confirm(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		//自己的状态
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
			exit;
		}
		if($regObj['as_status'] != 1){
			//已经配对
			printJson(0,1,'您还未绑定，请先申请爱锁');
			exit;
		}
		$lastApplyObj = self::$modelApply->getLastObjByOpenid($openid);
		if(!$lastApplyObj){
			printJson(0,1,'您还没有提交解锁申请');
		}
		if($lastApplyObj['status'] == 1){
			printJson(0,1,'您已确认解锁');
		}
		if($lastApplyObj['status'] == 2){
			printJson(0,1,'您已解锁成功');
		}
		if($lastApplyObj['status'] == -1){
			printJson(0,1,'您已取消解锁申请');
		}
		if($lastApplyObj['status'] != 0){
			printJson(0,1,'确认无效');
		}
		$otherUserObj = self::$modelUser->getObjByOpenid($regObj['as_openid']);
		$otherApplyObj = self::$modelApply->getLastObjByOpenid($regObj['as_openid']);
		if(!$otherUserObj || $otherUserObj['as_status'] != 1){
			Logger::error("数据有错。双方不是绑定状态确发起来解锁功能---需要排查".$openid);
			printJson(0,1,'对方不是锁定状态，不能发起解锁');
		}
		$unlockStatus = 1;
// 		if($otherApplyObj && $otherApplyObj['status'] == 1){
// 			$unlockStatus = 1;
// 		}
		
		if($unlockStatus){
			//自己的用户状态改变
			$regObj['as_status'] = 2;
			$regObj['as_uid'] = null;
			$regObj['as_openid'] = null;
			$regObj['as_lock_time'] = null;
			$regObj['as_unlock_time'] = date('Y-m-d H:i:s');
			self::$modelUser->saveOrUpdateAsUser($regObj,true);
			if($otherUserObj){
				//对方的用户状态改变
				$otherUserObj['as_uid'] = null;
				$otherUserObj['as_openid'] = null;
				$otherUserObj['as_lock_time'] = null;
				$otherUserObj['as_status'] = 2;
				$otherUserObj['as_unlock_time'] = date('Y-m-d H:i:s');
				self::$modelUser->saveOrUpdateAsUser($otherUserObj,true);
			}
			
			//自己的申请状态改变
			$lastApplyObj['status'] = 2;
			$lastApplyObj['over_time'] = date('Y-m-d H:i:s');
			self::$modelApply->saveOrUpdateAsUnLockApply($lastApplyObj,true);
			if($otherApplyObj){
				//对方的申请状态改变
				$otherApplyObj['status'] = 2;
				$otherApplyObj['over_time'] = date('Y-m-d H:i:s');
				self::$modelApply->saveOrUpdateAsUnLockApply($otherApplyObj,true);	
			}
			
			//发给自己消息
			$sendMsg['type'] = "news";
			$sendMsg['to_users'] = $regObj['openid'];
			$sendMsg['articles'][0]['title'] = "解锁成功！点击查看[收信]";
			$sendMsg['articles'][0]['description'] = "有些坚持并不容易! 分手快乐!";
			$sendMsg['articles'][0]['picurl'] = "http://ww4.sinaimg.cn/bmiddle/58a342d7tw1eii56uf8y2j20a005k752.jpg";
			//$sendMsg['articles'][0]['url'] = url("Message","inbox");
			$sendMsg['articles'][0]['url'] = url("UnLock","unLockSuccess");
			AiSuoFactory::getSendMessage()->send($sendMsg);
				
			//发给对方消息
			$sendMsg['type'] = "news";
			$sendMsg['to_users'] = $otherUserObj['openid'];
			$sendMsg['articles'][0]['title'] = "解锁成功！点击查看[收信]";
			$sendMsg['articles'][0]['description'] = "有些坚持并不容易! 分手快乐!";
			$sendMsg['articles'][0]['picurl'] = "http://ww4.sinaimg.cn/bmiddle/58a342d7tw1eii56uf8y2j20a005k752.jpg";
			$sendMsg['articles'][0]['url'] = url("UnLock","unLockSuccess");
			//$sendMsg['articles'][0]['url'] = url("Message","inbox");
			AiSuoFactory::getSendMessage()->send($sendMsg);
				
			
			$jumpUrl = url("UnLock","unLockSuccess");
			printJson(array("jumpUrl"=>$jumpUrl));
		}else{
			//自己的申请状态改变
			$lastApplyObj['status'] = 1;
			self::$modelApply->saveOrUpdateAsUnLockApply($lastApplyObj,true);
			$jumpUrl = url("UnLock","confirmSuccess");
			printJson(array("jumpUrl"=>$jumpUrl));
		}
		
	}
	
	/**
	 * 锁定成功
	 */
	public function unLockSuccess(){
		$openid = getCurrOpenid();
		if(!$openid ){
			echo "error";
			exit;
		}
		$this->display();
	}
	
	/**
	 * 申请成功
	 */
	public function applySuccess(){
		$openid = getCurrOpenid(true);
		
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			$this->display("UnLock.notMember");
			exit;
		}
		if($regObj['as_status'] == 2){
			//已经配对
			$this->display("UnLock.hasUnLock");
			exit;
		}
		
		$lastApplyObj = self::$modelApply->getLastObjByOpenid($openid);
		if($lastApplyObj && $lastApplyObj['apply_time'] && (time() - strtotime($lastApplyObj['apply_time']))> AbcConfig::APPLY_TIME_DURTION * 3600){
			$this->display("UnLock.confirmIndex");
			exit;
		}
		$durtionTime = AbcConfig::APPLY_TIME_DURTION * 3600 - (time() - strtotime($lastApplyObj['apply_time']));
		$hour = floor($durtionTime/(3600));
		$miniter = floor(($durtionTime%3600)/60);
		$durtionStr = $hour."小时".$miniter."分";
		$this->assign("durtionTime",$durtionTime);
		$this->assign("durtionStr",$durtionStr);
		
		$this->display("UnLock.applySuccess");
	}
	

	/**
	 * 确认解锁首页
	 */
	public function confirmIndex(){
		$this->display();
	}
	
	/**
	 * 取消成功
	 */
	public function cancelSuccess(){
		$this->display();
	}

	/**
	 * 确认成功
	 */
	public function confirmSuccess(){
		$this->display();
	}
	
}