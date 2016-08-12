<?php
class LoginAction extends YkdxCommonAction
{
	public function index () {
		$this->oauth();
	}
	public function oauth () {
		$callback = url('Login', 'wxCallback',  $this->getParam());
		oauthBack($callback);
	}
	
	public function wxCallback () {
		$checkRet = checkOauthParam();
		if (!$checkRet) {
			$this->showErrorH5("授权返回出错，请稍后再试");
		}
		$openid = $this->getParam("openid");
		$user = new YkdxUHome_User();
		$user->openid = $openid;
		$userinfo = loadModel("user")->getUserByOpenid($openid);
		if($userinfo){
			$user->userId = $userinfo['user_id'];
			$user->userName = $userinfo['user_name'];
		}
		$status = YkdxUHome::setUser($user,true);
		$callback = urldecode($this->getParam("callback",''));
		if(!$callback){
			$callback = url('User', 'index',$this->getParam());
		}
		header("Location: ".$callback);
	}
	
	public function logout () {
		YkdxUHome::logout();
		redirect(url('User', 'index'));
	}
}