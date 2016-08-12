<?php
class WeixinAction extends Action
{
	
	private static $modelUser;
	
	public function __construct()
	{
		parent::__construct();
		self::$modelUser = M("AisuoWeb.Regist");
	}
	
	/**
	 * 自定义菜单入口
	 */
	public function index()
	{
		$openid = $this->getParam("openid");
		//自己的状态
		$regObj = self::$modelUser->getObjByOpenid($openid);
		$result["error"] = 0;
		$result["msg"]="";
		$result["data"]=array();
		if(!$regObj){
			//还不是会员，
			$result["data"]["msg_type"] = "text";
			$result["data"]["text"] = $openid."您还不是会员";
			echo json_encode($result);
			exit;
		}else{
			//还不是会员，
			$result["data"]["msg_type"] = "text";
			$result["data"]["text"] = $openid."您已经是会员";
			echo json_encode($result);
			exit;
		}
	}
	
	
}