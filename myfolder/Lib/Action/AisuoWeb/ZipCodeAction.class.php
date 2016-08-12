<?php
class ZipCodeAction extends Action
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
		$openid = getCurrOpenid(true);
		$regObj = self::$modelUser->getObjByOpenid($openid);
		if(!$regObj){
			//还不是会员，			
			//$this->display("Regist.notMember");
			header("location:".url("Regist","index"));
			exit;
		}
		
		$this->assign("regObj",$regObj);
		$this->display();
	}
	
	
}