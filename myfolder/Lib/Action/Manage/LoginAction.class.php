<?php
class LoginAction extends BaseAction
{
	/**
	 * 
	 *展示登录页面
	 */
	public function login(){
		forward("Login","index");
	}
	
	public function index(){
		if (!UHome::isLogin()) {
			$this->display();
		} else {
			$this->redirect(Url('Index', 'index'));
		}
	}
	
 	/**
	 * 验证码显示
	 */
	public function verify()
	{
		randcode();
	}
	
	/**
	 * 
	 * 验证登录信息
	 */
	public function doLogin(){
		$userArr = array("hezq"=>array("id"=>1,"pw"=>"testhezq"),"qyu70481"=>array("id"=>2,"pw"=>"123456abc"));
		#获取页面信息
		$name  = trim($this->getParam('admin_name'));
		$pwd   = $this->getParam('admin_pwd');
		$checkCode	 = trim($this->getParam('seccode'));
		$randcode = @$_COOKIE['randcode'];
		$jumpUrl = $this->getParam('jumpUrl');
		if (md5($checkCode) != $randcode) {
			printJson('', 10003, '验证码错误,请重新输入!');
		} else {
// 			$user = M('User');
// 			$adminUser = $user->getUser($name);
// 			if(!$adminUser){
// 					printJson('','1001','该账户不存在或已删除!');
// 			}else{
// 				if($adminUser['password']!=$this->mdPwd($adminUser['rand'], $pwd)){
// 					printJson('','1002','密码错误!');
// 				}else{
// 					 #设置用户数据
// 					 $Uchome = new UHome();
// 					 $Uchome->userId=$adminUser['id'];
// 					 $Uchome->userName=$adminUser['user'];
// 					 UHome::setUser($Uchome);
// 					 printJson(1,'','登录成功!');
// 				}
// 			}
			if(!$userArr[$name]){
					printJson('','1001','该账户不存在或已删除!');
			}else{
				if($userArr[$name]["pw"]!= $pwd){
					printJson('','1002','密码错误!');
				}else{
					if(!$jumpUrl){
						$jumpUrl = url("Index","index");
					}
					
					 #设置用户数据
					 $Uchome = new UHome();
					 $Uchome->userId=$userArr[$name]['id'];
					 $Uchome->userName=$name;
					 UHome::setUser($Uchome);
					 printJson($jumpUrl,'','登录成功!');
				}
			}
		}
	}
	
	
	/**
	 *退出登录 
	 */
	public function loginout(){
		UHome::logout();
		$this->display('Login.index');	
	}
	
	/**
	 * 
	 * 修改密码页面
	 */
	public function alterPassword(){
		$this->display();
	}
	
	
	/**
	 * 
	 * 修改密码
	 */
	public function updatePass(){
		$passOld = $this->getParam('passwordOld');
		$passNew = $this->getParam('passwordNew');
		$passNewTwo = $this->getParam('passwordNewConf');
		if($passNew!=$passNewTwo){
			printJson('','1','新密码和确认密码不一样,请重新输入');
		}
		$model = M('Admin.Shop');
		$info = $model->getInfo($this->shop_id);
		if(!$info){
			printJson('','1','参数错误!');
		}
		#检测旧密码
		if($info['password']!=md5(md5($passOld).$info['rand'])){
			printJson('','1','旧密码错误!');
		}
		$newRand =$this->getRandomString(5);
		if(!$model->alterUpdate(md5(md5($passNew).$newRand),$newRand,$this->shop_id)){
			printJson('','1','修改失败!');
		}
		printJson(1);
	}
	
}