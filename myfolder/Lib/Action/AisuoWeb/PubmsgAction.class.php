<?php
class PubmsgAction extends Action
{
	private static $modelPubmsg;
	private static $modelUser;
	private static $catchkey;
	private static $openid;
	
	public function __construct()
	{
		parent::__construct();
		self::$modelUser = M("AisuoWeb.Regist");
		self::$modelPubmsg = M("AisuoWeb.Pubmsg");
		self::$openid = getCurrOpenid(true);
		self::$catchkey = self::$openid."_as_pubmsg_tmp";
	}
	
		
	/**
	 * 寄信首页
	 */
	public function index()
	{		
		
		$openid = getCurrOpenid(true);
		$pagesize = $this->getParam("pagesize",AbcConfig::PAGE_SIZE);
		$pageindex = $this->getParam("pageindex",1);
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，			
			//$this->display("Regist.notMember");
			//header("location:".url("Regist","index"));
			header("location:".url("Regist","index"));
			exit;
		}
// 		//未申请锁定
// 		if($regObj['as_status'] == 0){
// 			$this->display("Message.notLock");
// 			exit;
// 		}
// 		$msgList = self::$modelPubmsg->getPublic($pagesize,$pageindex);
// 		$msgList = self::$modelUser->addUserInfo($msgList);

		//获取头像
// 		$wxUserObj = @AiSuoFactory::getApiClient()->getUser($openid);
// 		//var_dump($wxUserObj);exit;
// 		if($wxUserObj){
// 			$regObj['truthname']=$wxUserObj->nickname;
// 			$regObj['sex']=$wxUserObj->sex;
// 			$regObj['headimgurl'] = $wxUserObj->headimgurl;
// 			self::$modelUser->saveOrUpdateAsUser($regObj,true);
// 		}
		
	 	$unReady = self::$modelPubmsg->getUnReadyTip($regObj["uid"]);
		
	 	//var_dump($unReady);exit;
		
// 		$this->assign("msgList",$msgList);
	 	$this->assign("unReady",$unReady);
		$this->assign("selfObj",$regObj);
		$this->assign("currDate",date('Y.m.d'));
		$this->assign("timestamp",time());
		$this->display("Pubmsg.index");
	}
	

	/**
	 * 个人主页
	 */
	public function onePeople()
	{
		$openid = getCurrOpenid(true);
		$uid = $this->getParam("uid");
		$pagesize = $this->getParam("pagesize",AbcConfig::PAGE_SIZE);
		$pageindex = $this->getParam("pageindex",1);
		if(!$uid){
			header("location:".url("Index","error",array("msg"=>"用户不存在")));
			exit;
		}
	
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			//$this->display("Regist.notMember");
			//header("location:".url("Regist","index"));
			header("location:".url("Regist","index"));
			exit;
		}
		$otherObj = self::$modelUser->getObjByUid($uid);
		if(!$otherObj){
			//还不是会员，
			header("location:".url("Index","error",array("msg"=>"用户不存在")));
			exit;
		}
		// 		//未申请锁定
		// 		if($regObj['as_status'] == 0){
		// 			$this->display("Message.notLock");
		// 			exit;
		// 		}
		// 		$msgList = self::$modelPubmsg->getPublic($pagesize,$pageindex);
		// 		$msgList = self::$modelUser->addUserInfo($msgList);
	
	
		// 		$this->assign("msgList",$msgList);
		$this->assign("otherObj",$otherObj);
		$this->assign("selfUid",$regObj['uid']);
		$this->assign("uid",$uid);
		$this->assign("isself",$openid == $otherObj['openid']);
		$this->assign("currDate",date('Y.m.d'));
		$this->display("Pubmsg.onePeople");
	}
	
	
	/**
	 * 发布信池
	 */
	public function writePubmsg()
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
		// 		//未申请锁定
		// 		if($regObj['as_status'] == 0){
		// 			$this->display("Message.notLock");
		// 			exit;
		// 		}
		// 		$msgList = self::$modelPubmsg->getPublic($pagesize,$pageindex);
		// 		$msgList = self::$modelUser->addUserInfo($msgList);
		
		// 		$this->assign("msgList",$msgList);
		$this->assign("selfObj",$regObj);
		$this->assign("currDate",date('Y.m.d'));
		$this->display("Pubmsg.writePubmsg");
	}
	
	
	/**
	 * 某一信池消息的详情页
	 */
	public function onePubmsg()
	{
		$openid = getCurrOpenid(true);
		$as_pubmsg_id	 = $this->getParam("pid");
	
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$as_pubmsg_id){
			header("location:".url("Index","error",array("msg"=>"系统出错")));
			exit;
		}
		if(!$regObj){
			//还不是会员，
			header("location:".url("Regist","index"));
			exit;
		}
		$msginfo = self::$modelPubmsg->getPubmsgById($as_pubmsg_id);
		if(!$msginfo){
			header("location:".url("Index","error",array("msg"=>"消息不存在")));
			exit;
		}
		if($msginfo['is_delete'] == 1){
			header("location:".url("Index","error",array("msg"=>"帖子已删除")));
			exit;
		}
		$otherObj = self::$modelUser->getObjByOpenid($msginfo['openid']);
		if($msginfo['is_private']){
			$otherObj['truthname'] = AbcConfig::DEFAULT_USER_TRUTHNAME;
			$otherObj['headimgurl'] = AbcConfig::DEFAULT_USER_HEADIMGURL;
		}
		
		$zanlist = self::$modelPubmsg->getZanByPubmsgid($as_pubmsg_id);
		
		
		$this->assign("zanlist",$zanlist);
		$this->assign("msginfo",$msginfo);
		$this->assign("msgOwnerObj",$otherObj);
		$this->assign("selfObj",$regObj);
		$this->assign("allowDel",$openid == $msginfo['openid']);
		$this->display("Pubmsg.onePubmsg");
	}
	
	/**
	 * 获取我的通知列表
	 */
	public function pubMsgNotice()
	{
		$openid = getCurrOpenid(true);
	
		$regObj = self::$modelUser->getObjByOpenid($openid);
		
		if(!$regObj){
			//还不是会员，
			header("location:".url("Regist","index"));
			exit;
		}
		
		$this->assign("selfObj",$regObj);
		$this->display("Pubmsg.pubMsgNotice");
	}
	
	
	
	/**
	 * 获取列表
	 */
	public function ajax_nextList(){
		$openid = getCurrOpenid(true);
		$pagesize = $this->getParam("pagesize",AbcConfig::PAGE_SIZE);
		$pageindex = $this->getParam("pageindex",1);
		$pageindex = $pageindex<1?1:$pageindex;
		$timestamp = $this->getParam("timestamp",time());
		
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
			exit;
		}
		
		$msgList = self::$modelPubmsg->getPublic($pagesize,$pageindex,true,true,$timestamp);
		$msgList = self::$modelUser->addUserInfo($msgList);
		
		
		$hasNext = (!$msgList || count($msgList)<$pagesize)?0:1;
		printJson(array("hasNext"=>$hasNext,"list"=>$msgList,"openid"=>$openid));
	}
	
	/**
	 * 获取列表
	 */
	public function ajax_noticList(){
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
	
		$msgList = self::$modelPubmsg->getUnReadyList($regObj["uid"],$pagesize,$pageindex);
		//$msgList = self::$modelUser->addUserInfo($msgList);
		for($i = 0; $i < count ( $msgList ); $i ++) {
			// foreach ($arrData as $v){
			$v = $msgList [$i];
			if($v['is_private'] == 1){
				$msgList[$i]['truthname'] = AbcConfig::DEFAULT_USER_TRUTHNAME;
				$msgList[$i]['headimgurl'] = AbcConfig::DEFAULT_USER_HEADIMGURL;
			}
		}
		
		$hasNext = (!$msgList || count($msgList)<$pagesize)?0:1;
		printJson(array("hasNext"=>$hasNext,"list"=>$msgList,"openid"=>$openid));
	}
	
	
	/**
	 * 获取某一用户的列表
	 */
	public function ajax_nextListByUid(){
		$openid = getCurrOpenid(true);
		$pagesize = $this->getParam("pagesize",AbcConfig::PAGE_SIZE);
		$pageindex = $this->getParam("pageindex",1);
		$pageindex = $pageindex<1?1:$pageindex;
		$uid = $this->getParam("uid");
		if(!$uid){
			printJson(0,1,'用户不存在');
		}
	
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
			exit;
		}
	
		$msgList = self::$modelPubmsg->getPublicByUid($uid,$pagesize,$pageindex,true,true);
		$msgList = self::$modelUser->addUserInfo($msgList);
		
		if($msgList){
			for($i = 0 ;$i<count($msgList);$i++){
				$msgList[$i]['monthStr'] = '';
				$dt = $msgList[$i]['create_time'];
				$durtion = (strtotime(date('Y-m-d'))-strtotime(date('Y-m-d',strtotime($dt))))/(3600*24);
				if($durtion == 0){
					$msgList[$i]['dtstr'] = '今天';
				}else if($durtion == 1){
					$msgList[$i]['dtstr'] = '昨天';
				}else{
					$msgList[$i]['dtstr'] = date('d',strtotime($dt));
					$msgList[$i]['monthStr'] = date('m',strtotime($dt))."月";
				}
				
			}
		}
	
		$hasNext = (!$msgList || count($msgList)<$pagesize)?0:1;
		printJson(array("hasNext"=>$hasNext,"list"=>$msgList,"openid"=>$openid));
	}

	/**
	 * 获取我的收藏列表
	 */
	public function ajax_nextStoreList(){
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
		
		$msgList = self::$modelPubmsg->getMyStore($regObj['uid'],$pagesize,$pageindex);
		//$msgList = self::$modelUser->addUserInfo($msgList);
		for($i = 0; $i < count ( $msgList ); $i ++) {
			// foreach ($arrData as $v){
			$v = $msgList [$i];
			if($v['is_private'] == 1){
				$msgList[$i]['truthname'] = AbcConfig::DEFAULT_USER_TRUTHNAME;
				$msgList[$i]['headimgurl'] = AbcConfig::DEFAULT_USER_HEADIMGURL;
			}
		}
		
		$hasNext = (!$msgList || count($msgList)<$pagesize)?0:1;
		printJson(array("hasNext"=>$hasNext,"list"=>$msgList,"openid"=>$openid));
	}
	
	/**
	 * 获取列表
	 */
	public function ajax_nextReplyList(){
		$openid = getCurrOpenid(true);
		$as_pubmsg_id = $this->getParam("as_pubmsg_id");
		$pagesize = $this->getParam("pagesize",AbcConfig::PAGE_SIZE);
		$pageindex = $this->getParam("pageindex",1);
		$pageindex = $pageindex<1?1:$pageindex;
		if(!$as_pubmsg_id){
			//还不是会员，
			printJson(0,1,'帖子不存在了');
			exit;
		}
		
		
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			printJson(0,1,'您还不是会员，请先申请成为会员');
			exit;
		}
	
		$msgList = self::$modelPubmsg->getReplysByPubmsgid($as_pubmsg_id,$pagesize,$pageindex);
		//$msgList = self::$modelUser->addUserInfo($msgList);
	
		$hasNext = (!$msgList || count($msgList)<$pagesize)?0:1;
		printJson(array("hasNext"=>$hasNext,"list"=>$msgList));
	}
	/**
	 * 提交点赞
	 */
	public function ajax_addZan(){
		$openid = getCurrOpenid();
		$as_pubmsg_id = $this->getParam("as_pubmsg_id");
		if(!$openid ){
			printJson(0,1,'用户未登录');
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

		$check = self::$modelPubmsg->checkZanCondition($as_pubmsg_id,$openid);
		if(!$check){
			printJson(0,1,'您已经点过赞了');
		}
		$result = self::$modelPubmsg->addPubmsgZan($as_pubmsg_id,$regObj['uid'],$openid);
		if(!$result){
			printJson(0,1,'点赞失败，请稍后再试');
		}
		printJson(1,0,"点赞成功");
		
	}
	
	/**
	 * 提交点赞
	 */
	public function ajax_deleteZan(){
		$openid = getCurrOpenid();
		$as_pubmsg_id = $this->getParam("as_pubmsg_id");
		if(!$openid ){
			printJson(0,1,'用户未登录');
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
	
		$check = self::$modelPubmsg->checkZanCondition($as_pubmsg_id,$openid);
		if($check){
			printJson(0,1,'您还没有点过赞，不能取消');
		}
		$result = self::$modelPubmsg->deletePubmsgZan($as_pubmsg_id,$regObj['uid'],$openid);
		if(!$result){
			printJson(0,1,'取消点赞失败，请稍后再试');
		}
		printJson(array("truthname"=>$regObj['truthname']),0,"取消点赞成功");
	
	}

	/**
	 * 提交收藏
	 */
	public function ajax_addStore(){
		$openid = getCurrOpenid();
		$as_pubmsg_id = $this->getParam("as_pubmsg_id");
		if(!$openid ){
			printJson(0,1,'用户未登录');
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
	
		$check = self::$modelPubmsg->checkStoreCondition($as_pubmsg_id,$openid);
		if(!$check){
			printJson(0,1,'您已经收藏过了');
		}
		$result = self::$modelPubmsg->addPubmsgStore($as_pubmsg_id,$regObj['uid'],$openid);
		if(!$result){
			printJson(0,1,'收藏失败，请稍后再试');
		}
		printJson(1,0,"收藏成功");
	
	}
	


	/**
	 * 取消收藏
	 */
	public function ajax_deleteStore(){
		$openid = getCurrOpenid();
		$as_pubmsg_id = $this->getParam("as_pubmsg_id");
		if(!$openid ){
			printJson(0,1,'用户未登录');
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
	
		$check = self::$modelPubmsg->checkStoreCondition($as_pubmsg_id,$openid);
		if($check){
			printJson(0,1,'您还没有收藏，不能取消');
		}
		$result = self::$modelPubmsg->deletePubmsgStore($as_pubmsg_id,$regObj['uid'],$openid);
		if(!$result){
			printJson(0,1,'取消收藏失败，请稍后再试');
		}
		printJson(1,0,"取消收藏成功");
	
	}
	
	

	/**
	 * 提交点评
	 */
	public function ajax_addReply(){
		$openid = getCurrOpenid();
		$as_pubmsg_id = $this->getParam("as_pubmsg_id");
		$is_private= $this->getParam("is_private",0);
		$content = trim($this->getParam("content"));
		$replyuid = $this->getParam("replyuid","");
		$replyname = $this->getParam("replyname","");
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
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
		$result = self::$modelPubmsg->addPubmsgReply($as_pubmsg_id,$content,$is_private,$regObj['uid'],$openid,$replyuid,$replyname);
		if(!$result){
			printJson(0,1,'评论失败，请稍后再试');
		}
		printJson(1,0,"评论成功");
	
	}
	
	/**
	 * 发布帖子
	 */
	public function ajax_addPubmsg(){
		$openid = getCurrOpenid();
		$is_private= $this->getParam("is_private",0);
		$content =	trim($this->getParam("content",null,false));
		//echo $content;exit;
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
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
		$data['uid'] = $regObj['uid'];
		$data['openid'] = $regObj['openid'];
		$data['content'] = nl2br($content);
		$data['create_time'] = date('Y-m-d H:i:s');
		$data['is_private'] = $is_private;
		$data['last_update_time'] =  date('Y-m-d H:i:s');
	
		Logger::info("-----贴子内容:".$content,$data);
		$result = self::$modelPubmsg->savePubmsg($data);
		if(!$result){
			printJson(0,1,'发布失败，请稍后再试');
		}
		printJson(1,0,"发布成功");
	
	}
	

	/**
	 * 取消帖子
	 */
	public function ajax_deletePubmsg(){
		$openid = getCurrOpenid();
		$as_pubmsg_id = $this->getParam("as_pubmsg_id");
		if(!$openid ){
			printJson(0,1,'用户未登录');
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
	
		
		$result = self::$modelPubmsg->deletePubmsg($as_pubmsg_id);
		if(!$result){
			printJson(0,1,'删除帖子失败，请稍后再试');
		}
		printJson(array("$as_pubmsg_id"=>$as_pubmsg_id),0,"删除帖子成功");
	
	}
	
	/**
	 * 取消回复
	 */
	public function ajax_deletePubmsgReply(){
		$openid = getCurrOpenid();
		$as_pubmsg_reply_id = $this->getParam("as_pubmsg_reply_id");
		if(!$openid ){
			printJson(0,1,'用户未登录');
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
	
	
		$result = self::$modelPubmsg->deletePubmsgReply($as_pubmsg_reply_id);
		if(!$result){
			printJson(0,1,'删除回复失败，请稍后再试');
		}
		printJson(1,0,"删除回复成功");
	
	}
	
	
	
	public function myStore(){
		$openid = getCurrOpenid(true);
		$pagesize = $this->getParam("pagesize",AbcConfig::PAGE_SIZE);
		$pageindex = $this->getParam("pageindex",1);
		
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，
			//$this->display("Regist.notMember");
			//header("location:".url("Regist","index"));
			header("location:".url("Regist","index"));
			exit;
		}
		// 		//未申请锁定
		// 		if($regObj['as_status'] == 0){
		// 			$this->display("Message.notLock");
		// 			exit;
		// 		}
		// 		$msgList = self::$modelPubmsg->getPublic($pagesize,$pageindex);
		// 		$msgList = self::$modelUser->addUserInfo($msgList);
		
		
		// 		$this->assign("msgList",$msgList);
		$this->assign("selfObj",$regObj);
		$this->assign("selfUid",$regObj['uid']);
		$this->assign("currDate",date('Y.m.d'));
		$this->display("Pubmsg.myStore");
	}
}