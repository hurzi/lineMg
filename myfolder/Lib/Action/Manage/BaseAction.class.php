<?php 
class BaseAction extends  Action{
	
		var $shop_id;
			
		protected static $loginList = array(
					'Login.index'=>1,
					'Login.doLogin'=>1,
					'Login.verify'=>1
		);	 
		
 		public function __construct(){
 			parent::__construct();
	  		UHome::init();
	  		$this->checkLogin();
	  		$this->leftMenu(); //自定义菜单
	  		$this->topData();//加载顶部数据
	  		$this->assign("title","管理后台");
 		}
 		
 		public function checkLogin(){
 			$r = $this->getParam('a') . '.' . $this->getParam('m');
			if (! UHome::isLogin() &&  ! isset(self::$loginList[$r])) {
				if ($this->isAjax()) {
					printJson('', 1, '你没有登录，请先登录');
				} else {
					$this->redirect(url('Login', 'index'));
				}
			}
			$this->shop_id = UHome::getUserId();
		}
		
		/**
		 * 
		 * 检测验证码
		 * @param unknown_type $rand
		 * @param unknown_type $pwd
		 */
		public function mdPwd($rand,$pwd){
			if(!$rand && !$pwd){
					return false;
			}
			return md5(md5($pwd).$rand);
		}
		
		/**
		 * 加载菜单
		 */
		protected function leftMenu () {
			$menu = include LIB_PATH . '/Action/Manage/Menu.php';
			$this->assign('sysmenu', $menu);
		}
		
		/**
		 * 加载顶部数据
		 */
		protected function topData(){
			$this->assign('unReadCount', 0);
		}
		
		/**
		 * 六位随机码(字符带数字)
		 * @param 长度 $length
		 * @param string $max
		 */
		public function getRandomString($length, $max=FALSE){
			if (is_int($max) && $max > $length) {
				$length = mt_rand($length, $max);
			}
			$output = '';
			for ($i=0; $i<$length; $i++){
				$which = mt_rand(0,2);
				if ($which === 0){
					$output .= mt_rand(0,9);
				}elseif ($which === 1){
					$output .= chr(mt_rand(65,90));
				}else{
					$output .= chr(mt_rand(97,122));
				}
			}
			return $output;
		}
		
		public function showError($msg){
			echo $msg;exit;
		}
		
}
