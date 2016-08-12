<?php
class RegistUserAction extends AdminAction
{
	public function __construct()
	{
		parent::__construct();
		self::$model = loadModel('Admin.RegistUser');
	}

	public function index()
	{
		$openid = trim($this->getParam('openid'));
		$sex = $this->getParam('sex', -1);
		$truthname = $this->getParam('truthname', null);
		$mobile = trim($this->getParam('mobile'));
		$province = trim($this->getParam('province'));
		$city = trim($this->getParam('city'));
		$openid = trim($this->getParam('openid'));
		$as_status = (int)($this->getParam('as_status',-1));
		$paged = (int) $this->getParam(Config::VAR_PAGE, 1);
		$pagesize = Config::PAGE_LISTROWS;
		
		
		$args['openid'] 		= $openid;
		$args['sex'] 			= (int) $sex;
		$args['truthname']  = $truthname;
		$args['mobile']		= $mobile;
		$args['paged'] 			= $paged;
		$args['pagesize'] 		= $pagesize;
		$args['province'] 		= $province;
		$args['city'] 			= $city;
		$args['as_status']  = (int) $as_status;
	
		$sexList = self::$model->getSexList();

		$list = array();
		$page = '';

		$result = self::$model->getUsers($args);
		//$moveGroupListJson = array();
		$userGroupList = array();
		if ($result) {
			$list = $result['list'];
			$count = $result['count'];
			$pageObj = new Page($count, $pagesize);
			$page = $pageObj->show();

			//$userGroupList = self::$model->getGroupList(1, false);
			//$moveGroupListJson = self::$model->getMoveGroupListJson();
		}
		
		$statusCount = self::$model->getStatusCount();

		$this->assign('openid', $openid);
		$this->assign('truthname', $truthname);
		$this->assign('sex', $sex);
		$this->assign('as_status', $as_status);
		$this->assign('mobile', $mobile);
		$this->assign('province', $province);
		$this->assign('city', $city);
		$this->assign('action', "RegistUser");
		$this->assign('method', "index");


		$this->assign('statusCount', $statusCount);
		$this->assign('sexList', $sexList);
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->display();

	}

	
	/**
	 * 发送短信
	 * @param unknown $phone
	 * @param unknown $content
	 * @return Ambigous <number, mixed, boolean, string, unknown>
	 */
	private function getBalance(){
		/**
		 * 定义程序绝对路径
		 */
		define ( 'SCRIPT_ROOT', dirname ( __FILE__ ) . '/../../Common/sms/' );
		require_once SCRIPT_ROOT . 'include/Client.php';
	
		
		/**
		 * 网关地址
		*/
		$gwUrl = AbcConfig::SMS_WG_URL;
		/**
		 * 序列号,请通过亿美销售人员获取
		 */
		$serialNumber =AbcConfig::SMS_SERIAL_NUMBER;
		/**
		 * 密码,请通过亿美销售人员获取
		 */
		$password = AbcConfig::SMS_PW;
		/**
		 * 登录后所持有的SESSION KEY，即可通过login方法时创建
		 */
		$sessionKey = AbcConfig::SMS_SESSION_KEY;
		/**
		 * 连接超时时间，单位为秒
		 */
		$connectTimeOut = 2;
	
		/**
		 * 远程信息读取超时时间，单位为秒
		 */
		$readTimeOut = 10;
	
		/**
		 $proxyhost		可选，代理服务器地址，默认为 false ,则不使用代理服务器
		 $proxyport		可选，代理服务器端口，默认为 false
		 $proxyusername	可选，代理服务器用户名，默认为 false
		 $proxypassword	可选，代理服务器密码，默认为 false
		 */
		$proxyhost = false;
		$proxyport = false;
		$proxyusername = false;
		$proxypassword = false;
	
		$client = new Client ( $gwUrl, $serialNumber, $password, $sessionKey, $proxyhost, $proxyport, $proxyusername, $proxypassword, $connectTimeOut, $readTimeOut );
		/**
		 * 发送向服务端的编码，如果本页面的编码为GBK，请使用GBK
		*/
		$client->setOutgoingEncoding ( "UTF-8" );
	
		
		$result = $client->getBalance();
		return $result;
	}
	
}