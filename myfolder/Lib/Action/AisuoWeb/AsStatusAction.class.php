<?php
class AsStatusAction extends Action
{
	
	private static $modelUser;
	
	public function __construct()
	{
		parent::__construct();
		self::$modelUser = M("AisuoWeb.Regist");
	}
	
	/**
	 * 直接跳转页面获取openid,中转入口
	 */
	public function index()
	{
		$this-> userinfo();
		return;
		$openid = $this->getParam("showopenid");
		$isself = false;
		$curropenid = getCurrOpenid(true);
		if(!$openid){
			$openid = $curropenid;
			$isself = true;
		}else if($openid == $curropenid){
			$isself = true;
		}
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，			
			//$this->display("Regist.notMember");
			header("location:".url("Regist","index"));
			exit;
		}
		$otherObj = array();
		$this->assign("durtionDay",0);
		if($regObj['as_status'] == 1){
			$otherObj = self::$modelUser->getObjByOpenid($regObj['as_openid']);
			$astime = $regObj['as_lock_time'];
			
			$this->assign("otherTruthName",$otherObj['truthname']);
			$this->assign("lock_time",date('Y年m月d日',strtotime($astime)));
			$this->assign("as_durtion_day",floor((time()-strtotime($astime))/(3600*24)));
			Logger::info("--------------)))".(time()-strtotime($astime)));
			$durtionTime = (time() - strtotime($regObj['as_lock_time']));
			$hour =floor($durtionTime/(3600*24));
			$this->assign("durtionDay",$hour);
		}	
		
		//获取头像
		$wxUserObj = @AiSuoFactory::getApiClient()->getUser($openid);
		//var_dump($wxUserObj);exit;
		if($regObj['province'] && $regObj['city']){
			$this->assign("asZone",$regObj['province']." ".$regObj['city']);
		}else if($wxUserObj){
			$this->assign("asZone",$wxUserObj->province." ".$wxUserObj->city);
		}else{
			$this->assign("asZone","无");
		}
		
		$this->assign("asStatusStr",$regObj['as_status'] == 1 ? "恋爱" : "单身");
		$this->assign("regObj",$regObj);
		$this->assign("otherObj",$otherObj);
		$this->assign("isself",$isself);
		$this->assign("asStatus",$regObj['as_status']);
		$this->display("AsStatus.index");
	}
	
	/**
	 * 直接跳转页面获取openid,中转入口
	 */
	public function userinfo()
	{
		$openid = $this->getParam("showopenid");
		$isUpdate = $this->getParam("isUpdate");
		$isself = false;
		$curropenid = getCurrOpenid(true);
		if(!$openid){
			$openid = $curropenid;
			$isself = true;
		}else if($openid == $curropenid){
			$isself = true;
		}
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，			
			//$this->display("Regist.notMember");
			header("location:".url("Regist","index"));
			exit;
		}
		
		if($isUpdate && $isself){
			//获取头像
			$wxUserObj = @AiSuoFactory::getApiClient()->getUser($openid);
			//var_dump($wxUserObj);exit;
			if($wxUserObj){
				$regObj['sex']=$wxUserObj->sex;
				$regObj['headimgurl'] = $wxUserObj->headimgurl;
				$regObj['province'] = $wxUserObj->province;
				$regObj['city'] = $wxUserObj->city;
				self::$modelUser->saveOrUpdateAsUser($regObj,true);
			}
		}

		$otherObj = array();
		$this->assign("durtionDay",0);
		if($regObj['as_status'] == 1){
			$otherObj = self::$modelUser->getObjByOpenid($regObj['as_openid']);
			$astime = $regObj['as_lock_time'];
			
			$this->assign("otherTruthName",$otherObj['truthname']);
			$this->assign("lock_time",date('Y年m月d日',strtotime($astime)));
			$this->assign("as_durtion_day",floor((time()-strtotime($astime))/(3600*24)));
			Logger::info("--------------)))".(time()-strtotime($astime)));
			$durtionTime = (time() - strtotime($regObj['as_lock_time']));
			$hour =floor($durtionTime/(3600*24));
			$this->assign("durtionDay",$hour);
		}	
		
		//获取头像
		$wxUserObj = @AiSuoFactory::getApiClient()->getUser($openid);
		//var_dump($wxUserObj);exit;
		if($regObj['province'] || $regObj['city']){
			$this->assign("asZone",$regObj['province']." ".$regObj['city']);
		}else if($wxUserObj){
			$this->assign("asZone",$wxUserObj->province." ".$wxUserObj->city);
			
			$upArr['province'] = $wxUserObj->province;
			$upArr['city'] = $wxUserObj->city;
			self::$modelUser->UpdateUserInfoArr($openid,$upArr);
			
		}else{
			$this->assign("asZone","无");
		}
		
		$this->assign("asStatusStr",$regObj['as_status'] == 1 ? "恋爱" : "单身");
		$this->assign("regObj",$regObj);
		$this->assign("otherObj",$otherObj);
		$this->assign("isself",$isself);
		$this->assign("asStatus",$regObj['as_status']);
		$this->display("AsStatus.userinfo");
	}
	
	/**
	 * 直接跳转页面获取openid,中转入口
	 */
	public function username()
	{
		$openid = $this->getParam("showopenid");
		$isself = false;
		$curropenid = getCurrOpenid(true);
		if(!$openid){
			$openid = $curropenid;
			$isself = true;
		}else if($openid == $curropenid){
			$isself = true;
		}
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，			
			//$this->display("Regist.notMember");
			header("location:".url("Regist","index"));
			exit;
		}
		$this->assign("regObj",$regObj);		
		$this->display("AsStatus.username");
	}

	/**
	* 更新用户基本信息
	*/
	public function ajax_updateUserinfo(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		$truthname = $this->getParam("truthname");
		if(!$truthname ){
			printJson(0,1,'姓名不能为空');
		}
		$result = self::$modelUser->UpdateUserName($openid,$truthname);
		if(!$result){
			printJson(0,1,'修改失败请稍后再试');
		}
		printJson(array("jumpUrl"=>url("AsStatus","userinfo")));
		
	}
}