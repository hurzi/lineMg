<?php
class WarnAction extends Action
{
	private static $modelWarn;
	private static $modelUser;
	private static $catchkey;
	private static $openid;
	
	public function __construct()
	{
		parent::__construct();
		self::$modelUser = M("AisuoWeb.Regist");
		self::$modelWarn = M("AisuoWeb.Warn");
		self::$openid = getCurrOpenid(true);
		self::$catchkey = self::$openid."_ac_warn_tmp";
	}
	
		
	/**
	 * 提醒首页
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
// 		//未申请锁定
// 		if($regObj['as_status'] == 0){
// 			$this->display("Message.notLock");
// 			exit;
// 		}
		$list = self::$modelWarn->getListByUid($regObj['uid']);
		$typeList = self::$modelWarn->getAllWarnType();
		
		$this->assign("list",$list);
		$this->assign("typeList",$typeList);
		$this->display("Warn.index");
	}
	
	

	/**
	 * 提交信件
	 */
	public function ajax_submit(){
		$openid = getCurrOpenid();
		if(!$openid ){
			printJson(0,1,'用户未登录');
		}
		$warn_date = $this->getParam("warn_date");
		$warn_type_id = $this->getParam("warn_type_id");
		$warnid = $this->getParam("warnid");
		if(!$warn_date){
			printJson(0,1,'提醒时间不能为空');
		}
		if(!$warn_type_id){
			printJson(0,1,'提醒类型不能为空');
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
		$warnCount = self::$modelWarn->getCountByUid($regObj['uid']);
		if($warnCount>=AbcConfig::MAX_WARN_COUNT){
			printJson(0,1,'最多设置二个提醒');
		}
		
		$data['openid'] = $openid;
		$data['uid'] = $regObj['uid'];
		$data['warn_type_id'] = $warn_type_id;
		$data['warn_date'] = $warn_date;
		$data['create_time'] = date('Y-m-d H:i:s');
		$data['last_update_time'] = date('Y-m-d H:i:s');
		if($warnid){
			$data['id'] = $warnid;
		}
		
		$result = self::$modelWarn->saveWarn($data,!empty($warnid));
		if(!$result){
			printJson(0,1,'保存失败，请稍后再试');
		}
		
		$typeName = self::$modelWarn->getWarnTypeNameById($warn_type_id);
		
		printJson(array("typeName"=>$typeName,"warnDate"=>$warn_date),0,"保存成功");		
	}
	
	
}