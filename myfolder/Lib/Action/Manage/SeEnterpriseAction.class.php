<?php
class SeEnterpriceAction extends BaseAction{

	private $SeEnterpriceModel = null;
	public function __construct(){
		parent::__construct();
		$this->SeEnterpriceModel = loadModel('SeEnterprice');
	}
	/**
	 * 显示企业列表(已经审核通过)
	 */
	public function index(){
		$keyword = trim($this -> getParam('keyword'));
		$args['pagesize'] = Config::PAGE_LISTROWS;
		$args['keyword'] = $keyword;
		$args['status'] = SeEnterpriceModel::STATUS_AUDITING_YES;
		
		$return = $this->SeEnterpriceModel->getEntList($args);
		$list = array();
		$page = '';

		if ($return) {
			$list = $return['list'];
			$page = $return['page'];
		}
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('keyword',$keyword);
		$this->display();
	}
	public function unAccountEntList(){
            $keyword = trim($this -> getParam('keyword'));
		$args['pagesize'] = Config::PAGE_LISTROWS;
		$args['keyword'] = $keyword;
		$args['status'] = SeEnterpriceModel::STATUS_AUDITING_YES;
		
		$return = $this->SeEnterpriceModel->getUnAccountEntList($args);
		$list = array();
		$page = '';

		if ($return) {
			$list = $return['list'];
			$page = $return['page'];
		}
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('keyword',$keyword);
		$this->display();
        }

        /**
	 * 显示企业审核列表(审核未通过/等待审核)
	 */
	public function indexAuditing(){
		$keyword = trim($this -> getParam('keyword'));
		$args['pagesize'] = Config::PAGE_LISTROWS;
		$args['keyword'] = $keyword;
		$args['status'] = array(SeEnterpriceModel::STATUS_AUDITING_DOING,SeEnterpriceModel::STATUS_AUDITING_NO);
	
		$return = $this->SeEnterpriceModel->getEntList($args,"status ,ent_id desc");
		$list = array();
		$page = '';
	
		if ($return) {
			$list = $return['list'];
			$page = $return['page'];
		}
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('keyword',$keyword);
		$this->display();
	}
	/**
	 * 编辑企业插件模板
	 */
	public function edit(){
		$id = (int)$this->getParam('ent_id'); //企业id
        if(strtolower(HttpRequest::method()) === 'post'){

            $param['ent_name']  	 = trim($this->getParam('ent_name'));
            $param['ent_email'] 	 = $this->getParam('ent_email');
            $param['address']        = $this->getParam('ent_address');
			$param['linkman']        = $this->getParam('ent_linkman');
			$param['telephone']        = $this->getParam('ent_telephone');
			$param['source_type']        = $this->getParam('ent_source_type');
			$param['source']        = $this->getParam('ent_source');

			if(!$param['ent_name']){
                printJson(null,-1,'企业名称不能为空');
            }
            if(!$param['ent_email']){
                printJson(null,-1,'企业邮箱不能为空');
            }
            if (!filter_var($param['ent_email'], FILTER_VALIDATE_EMAIL)) {
                printJson(null,-1,'企业邮箱格式错误');
            }

            $result = loadModel('SeEnterprice')->updateEnt($id,$param);

            if($result){
                printJson(1,0);
            }
            printJson(1,1,'更新失败');

        }
        $entInfo = loadModel('SeEnterprice')->getEntInfo($id);
        $this->assign('entInfo',$entInfo);
		$this->display('SeEnterprice.addEnt');
	}
        /**
	 * 编辑企业备注
	 */
	public function editMemo(){
	$id = (int)$this->getParam('ent_id'); //企业id
        if(strtolower(HttpRequest::method()) === 'post'){
        
            $param['memo'] = date("Y-m-d H:i:s",time())." ".$this->getParam('memo')."|".$this->getParam('old_memo');
            $param['last_update_time'] = time();
            
           // print_r($param);exit;
            $result = loadModel('SeEnterprice')->updateEnt($id,$param);

            if($result){
                printJson(1,0);
            }
            printJson(1,1,'更新失败');

        }
        $entInfo = loadModel('SeEnterprice')->getEntInfo($id);
        $this->assign('entInfo',$entInfo);
	$this->display();
	}
	/**
	 * 查看修改详情
	 */
	public function entInfo(){
		$id = (int)$this->getParam('ent_id'); //企业id
		$opt_type = (int)$this->getParam('opt_type',0); //操作类型  0：只查看信息 1;审核
		$entInfo = loadModel('SeEnterprice')->getEntInfo($id);
		$this->assign('entInfo',$entInfo);
		$this->assign('optType',$opt_type);
		$this->display();
	}
	
	/**
	 * 审核企业
	 */
	public function ajax_auditingEnt(){
		$id = (int)$this->getParam('ent_id'); //企业id
		$status = (int)$this->getParam('status'); //状态
		$auditing_memo = $this->getParam('auditing_memo'); //备注
		if($status == SeEnterpriceModel::STATUS_AUDITING_NO && !$auditing_memo){
			printJson(1,1,'审核不通过时，必须写备注');
		}
		
		$result = $this->SeEnterpriceModel->auditingEnt($id,$status,$auditing_memo);
		if(!$result){
			printJson(1,1,'审核失败，请稍后再试');
		}
		printJson($result);
	}
	
	/**
	 * 添加企业模板
	 */
	public function addEnt(){
        $entInfo = array(
            'ent_id'=>'',
            'ent_name'=>'',
            'ent_email'=>'',
            'address'=>''
        );
        $this->assign('entInfo',$entInfo);
		$this->display();
	}
	/**
	 * 添加企业数据
	 */
	public function insertEnt(){
		$ent_name 			= trim($this->getParam('ent_name'));
		$ent_email 			= $this->getParam('ent_email');
		$ent_address 		= $this->getParam('ent_address');
		$ent_linkman 		= $this->getParam('ent_linkman');
		$ent_telephone 		= $this->getParam('ent_telephone');
		$ent_source_type 		= $this->getParam('ent_source_type');
		$ent_source 		= $this->getParam('ent_source');

		if(!$ent_name){
			printJson(null,-1,'企业名称不能为空');
		}
		if(!$ent_email){
			printJson(null,-1,'企业邮箱不能为空');
		}
		if (!filter_var($ent_email, FILTER_VALIDATE_EMAIL)) {
			printJson(null,-1,'企业邮箱格式错误');
		}

		$param['ent_name']  	 = $ent_name;
		$param['ent_email'] 	 = $ent_email;
		$param['address']        = $ent_address;
		$param['linkman']        = $ent_linkman;
		$param['telephone']        = $ent_telephone;
		$param['source_type']        = $ent_source_type;
		$param['source']        = $ent_source;
		$param['status'] = SeEnterpriceModel::STATUS_AUDITING_YES; //后台增加的自动审核通过
		$param['auditing_memo'] = "后台创建，自动审核通过";
		$param['auditing_time'] = time();

		$flagCreateEnt = loadModel('SeEnterprice')->insertEntInfo($param);
		if($flagCreateEnt){
			printJson(null,0,'创建企业成功');
		}
		printJson(null,-1,'创建企业失败');
	}

	/**
	 * 删除企业
	 * a=SeEnterprice&m=dropEnt&ent_id=
	 */
	public function dropEnt(){
        die("功能未完成");
		$entId = (int)$this->getParam('ent_id');
		$flagCreateEnt = loadModel('xxxxxx')->deleteEnt-AllInfo($entId);
		if($flagCreateEnt){
			printJson(null,0,'删除企业成功,delete enterprise success!');
		}
		printJson(null,-1,'删除企业失败,delete enterprise failure!');
	}

	private function genSqlInStr ($arr, $isString = true) {
		$str = "";
		if (empty($arr) || !is_array($arr)) return '';
		$k = 0;
		foreach ($arr as $value) {
			if ($k != 0) $str .= " , ";
			if ($isString) {
				$str .= "'" .  @mysql_escape_string(stripslashes($value)) . "'";
			} else {
				$str .= intval($value);
			}
			$k++;
		}
		return $str;
	}

	public function dataFetcherSet() {
		$entId = (int)$this->getParam('ent_id');
		$token = trim($this->getParam('token'));
		//token 只允许数组字母下划线
		if (!preg_match('/^[a-zA-Z0-9_]+$/', $token)) {
			printJson('', 1, 'TOKEN 只能由数组字母下划线组成');
		}
		$appModel = loadModel('EntConfigSet');
		$appinfo = $appModel->getEntAppById(' `ent_id` = '.$entId);
		if (!$appinfo) {
			printJson('', 2, '数据有误请刷新后重试');
		}

		if ($appinfo['data_fetcher_token'] == $token) {
			printJson('ok', 0, '修改成功');
		}

		$ret = $appModel->dataFetcherSet($entId, $token);
		if ($ret) {
			printJson('ok', 0, '修改成功');
		}
		printJson('', 3, '操作失败，请重试');
	}

	/**
	 * 企业配置授权 OAuth 2.0 授权权限 （基本<snsapi_base>，用户信息<snsapi_userinfo>）
	 */
	public function entSetAuth(){
		//企业ID
		$id = (int)$this->getParam('id');
		$ent = loadModel('AppManager')->getAppInfo($id);

		$list = array('snsapi_base'=>'基本授权', 'snsapi_userinfo'=>'用户信息授权');
		$modelAuth = loadModel('EntSetAuth');
		$setKey = EntSettingKey::OAUTH_SCOPE;
		$where = " ent_id = {$id} and set_key='{$setKey}'";
		$setValue = $modelAuth->getEntSetAuth($where);
		$listIds  =  array();
		if($setValue){
			$listIds = unserialize($setValue);
		}
		$this->assign('list', $list);
		$this->assign('listIds',$listIds);
		$this->assign('ent', $ent);
		$this->display();
	}
	/**
	 * 企业配置授权 OAuth 2.0 授权权限 更新操作
	 */
	public function entSetAuthUpdate(){

		$entId = (int) $this->getParam('id');
		$operate_ids = $this->getParam('ids');
		$param['entId'] 		= $entId;
		$param['operatteIds']	= $operate_ids;

		$modelAuth = loadModel('EntSetAuth');
		$modelAuth->validAuth($param);
		$setKey 	 = EntSettingKey::OAUTH_SCOPE;
		//$where		 = " ent_id = {$id} and set_key='{$setKey}'";
		$set['set_value'] = serialize($operate_ids);
		$set['set_key']   = $setKey;
		$set['ent_id']    = $entId;
		if (! $modelAuth->updateEntSetAuth($set)) {
			printJson(0, -1, '操作失败！');
		}
		printJson();
	}

	public function verifyOpened(){
		$entId = (int) $this->getParam('id');
		$modelEntConfigSet = loadModel('EntConfigSet');
		$flag = $modelEntConfigSet -> verifyEntOpened($entId);

		if(!$flag){
			//printJson(Null,$modelEntConfigSet->getErrorCode(),$modelEntConfigSet->getError());
			echo "<script>alert('.{$modelEntConfigSet->getErrorCode()}.{$modelEntConfigSet->getError()}.');window.location.href='http://my.weixinv2.com/Adsit/index.php?a=SeEnterprice&m=index';</script>";
			exit;
		}
		//printJson(Null,0,'企业已开通微信接口访问权限');
		echo "<script>alert('企业已开通微信接口访问权限');window.location.href='http://my.weixinv2.com/Adsit/index.php?a=SeEnterprice&m=index';</script>";
	}


	public function apiTestPage () {
		$entId = (int)$this->getParam('ent_id', 0);
		$appinfo = array();
		if ($entId) {
			$appModel = loadModel('EntConfigSet');
			$appinfo = $appModel->getEntAppById(' `ent_id` = '.$entId);
		}
		$this->assign("appinfo", $appinfo);
		$this->display();
	}

	public function apiTest () {

		$appId = trim($this->getParam('app_id', ''));
		$appSecret = trim($this->getParam('app_secret', ''));
		$code = trim($this->getParam('code', ''));
		$state = trim($this->getParam('state', ''));
		$weixinName = trim($this->getParam('weixin_name', ''));
		$result = array();

		if (!$appId || !$appSecret || !$state || $state != QrCodeParamter::STATE_VALUE) {
			Logger::error('http params error:', $this->getParam());
			$this->assign('error', '非法操作');
			$this->display();exit;
		}
		if (!$code) {
			$this->assign('error', '您已经取消授权');
			$this->display();exit;
		}
		if (!class_exists('WeiXinApiCore')) {
			include_once WX_PATH . "/Api/WeiXinApiCore.class.php";
		}
		$oauthClient = WeiXinApiCore::getOAuthClient($appId,$appSecret);

		$token = $oauthClient->getAccessToken($code);
		if(!$token || !$token->openId){
			Logger::error('analysisCode error; code:'.$code, $token);
			$this->assign('error', '获取openid出错，请重试');
			$this->display();exit;
		}
		$openid = $token->openId;
		$token = getToken($appId,$appSecret);
		if (!$token) {
			$this->assign('error', '获取token时出错，请重试');
			$this->display();exit;
		}
		$apiClient = WeiXinApiCore::getClient($appId,$appSecret,$token);
		$user = $apiClient->getUser($openid);
		if ($user) {
			if (!$user->subscribe) {
				$this->assign('error', '请关注后，重试<br/><a href="weixin://addfriend/'.$weixinName.'">关注&nbsp;&nbsp;'.$weixinName.'</a>');
				$this->display();exit;
			}
			$result['获取用户接口权限'][0]='ok';
		} else {
			$result['获取用户接口权限'][0]='no';
			if ($apiClient->getErrorCode() == WX_Error::REQUIRE_SUBSCRIBE) {
				$this->assign('error', '请关注后，重试<br/><a href="weixin://addfriend/'.$weixinName.'">关注&nbsp;&nbsp;'.$weixinName.'</a>');
				$this->display();exit;
			}
			$result['获取用户接口权限'][1] = 'error : '. $apiClient->getErrorCode().'::'.$apiClient->getErrorMessage();
			Logger::error('get_user error:', $apiClient->getErrorCode().'::'.$apiClient->getErrorMessage());
		}
		unset($user);
		$mediaId = $apiClient->createImage(dirname(LIB_PATH) . '/www/Adsit/Public/images/logo_a.jpg');

		if ($mediaId) {
			$result['上传资源接口权限'][0] = 'ok';
		} else {
			$result['上传资源接口权限'][0] = 'no';
			$result['上传资源接口权限'][1] = 'error : '. $apiClient->getErrorCode().'::'.$apiClient->getErrorMessage();
			Logger::error('upload_media error:', $apiClient->getErrorCode().'::'.$apiClient->getErrorMessage());
		}
		$mediaUrl = $apiClient->getMediaUrl($mediaId);
		$imgCon = file_get_contents($mediaUrl);
		$imgJson = json_decode($imgCon, true);
		if (!$imgCon || $imgJson) {
			$result['获取资源接口权限'][0] = 'no';
			$result['获取资源接口权限'][1] = 'error : '. $imgCon;
		} else {
			$result['获取资源接口权限'][0] = 'ok';
		}
		unset($imgCon);
		$message_body = new WX_Message_Body();
		$message_body->to_users = $openid;
		$message_body->type = 'text';
		$message_body->content = '测试api客服发送接口权限，您收到了吗？嘿嘿！';
		$send = $apiClient->sendMessage($message_body);
		unset($message_body);
		if ($send) {
			$result['客服发送接口权限'][0] = 'ok';
		} else {
			$result['客服发送接口权限'][0] = 'no';
			$result['客服发送接口权限'][1] = 'error : '. $apiClient->getErrorCode().'::'.$apiClient->getErrorMessage();
			Logger::error('operator_send error:', $apiClient->getErrorCode().'::'.$apiClient->getErrorMessage());
		}
		$menu = $apiClient->getMenu();
		if ($menu) {
			$result['自定义菜单接权限'][0] = 'ok';
		} else {
			$result['自定义菜单接权限'][0] = 'no';
			$result['自定义菜单接权限'][1] = 'error : '. $apiClient->getErrorCode().'::'.$apiClient->getErrorMessage();
			Logger::error('custom_menu error:', $apiClient->getErrorCode().'::'.$apiClient->getErrorMessage());
		}
		$qrcUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$token;
		$qrcParam = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 9999999999}}}';
		$qrcRet = WeiXinApiRequest::post($qrcUrl, $qrcParam, false, false);
		$result['带参数二维码权限'][0] = 'no';
		if (WeiXinApiRequest::$http_code == '200') {
			if ($qrcRet && @$qrcRet['ticket']) {
				$result['带参数二维码权限'][0] = 'ok';
			} else {
				$result['带参数二维码权限'][1] = 'error: '. WeiXinApiRequest::$response;
			}
		} else {
			$result['带参数二维码权限'][1] = 'error: connect weixin error';
		}
		$this->assign('result', $result);
		$this->display();

	}

	public function genTestQrCode() {

		$appId          = $this->getParam('app_id', '');
		$appSecret      = $this->getParam('app_secret', '');
		$scope          = trim($this->getParam('scope', ''));
		$flag           = (int)$this->getParam('flag', 0);
		$weixinName     = trim($this->getParam('weixin_name', ''));

		$redirectUrl    = url(
                            'SeEnterprice',
                            'apiTest',
                            array(
                                'app_id'=>$appId,
                                'app_secret'=>$appSecret,
                                'weixin_name'=>$weixinName
                            )
                        );

		$url            =  ""; //TODO //str_replace(
                          //  array('APP_ID','REDIRET_URI','SCOPE','STATE', '#wechat_redirect'),
                           // array($appId,urlencode($redirectUrl),$scope, QrCodeParamter::STATE_VALUE, ''),
                            //ConfigBase::SCAN_WX_URL
                      //  );

		$ret            = array(
                            'url'=>$url,
                            'imageUrl'=>'http://chart.apis.google.com/chart?'.'chs=qr&chs=400x400&cht=qr&chld=L|0&chl='.urlencode($url)
                        );
		printJson($ret);
		//直接输出url
		if ($flag == 0) {

		}

		$ch = curl_init();
		$url = urlencode($url);
		curl_setopt($ch, CURLOPT_URL, 'http://chart.apis.google.com/chart');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'chs=qr&chs=400x400&cht=qr&chld=L|0&chl='.urlencode($url));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		if($info['http_code']!=200){
			echo 'curl http error';var_dump($info);
		} else {
			echo $response;
		}
	}

}
